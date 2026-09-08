<?php

namespace App\Http\Controllers\Judge;

use App\Events\ScoreSubmitted;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CustomCategoryScore;
use App\Models\FitnessScore;
use App\Models\IndigenousAttireScore;
use App\Models\ProductionScore;
use App\Models\QaScore;
use App\Models\TraditionalAttireScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class JudgeScoringController extends Controller
{
    /**
     * Get model class by category slug.
     */
    protected function getScoreModel(string $category)
    {
        return match ($category) {
            'production' => ProductionScore::class,
            'fitness' => FitnessScore::class,
            'traditional-attire' => TraditionalAttireScore::class,
            'indigenous-attire' => IndigenousAttireScore::class,
            'qa', 'qanda' => QaScore::class,
            default => null,
        };
    }

    /**
     * Display scoring pad view for category.
     */
    protected function renderScoringView(Request $request, string $categoryName, string $categorySlug, string $iconPath)
    {
        $judgeId = Auth::id();
        $candidates = Candidate::orderBy('candidate_number')->get();

        $modelClass = $this->getScoreModel($categorySlug);
        $rawScores = $modelClass ? $modelClass::where('judge_id', $judgeId)->get()->keyBy('candidate_id') : collect();

        $scores = [];
        foreach ($rawScores as $candId => $sObj) {
            $scores[$candId] = (float) $sObj->score;
        }

        if (in_array($categorySlug, ['qa', 'qanda'])) {
            $topFinalists = Candidate::getTopQualifiedFinalists();
            $maleCandidates = $topFinalists['male'];
            $femaleCandidates = $topFinalists['female'];
        } else {
            $maleCandidates = $candidates->filter(fn ($c) => $c->gender === 'Male');
            $femaleCandidates = $candidates->filter(fn ($c) => $c->gender === 'Female');
        }

        // Validate URL query parameters for initial pair index focus
        $initialPairIndex = 0;
        $requestedPair = $request->query('pair');
        $requestedCandidateId = $request->query('candidate_id');

        if ($requestedCandidateId && is_numeric($requestedCandidateId)) {
            $candId = (int) $requestedCandidateId;
            // Find which pair index contains this candidate_id
            if (in_array($categorySlug, ['qa', 'qanda'])) {
                $mIndex = $maleCandidates->search(fn ($c) => $c->id === $candId);
                $fIndex = $femaleCandidates->search(fn ($c) => $c->id === $candId);
                if ($mIndex !== false) {
                    $initialPairIndex = (int) $mIndex;
                } elseif ($fIndex !== false) {
                    $initialPairIndex = (int) $fIndex;
                }
            } else {
                $candObj = $candidates->firstWhere('id', $candId);
                if ($candObj) {
                    $allNumbers = $maleCandidates->pluck('candidate_number')
                        ->concat($femaleCandidates->pluck('candidate_number'))
                        ->unique()
                        ->sort()
                        ->values();
                    $nIndex = $allNumbers->search($candObj->candidate_number);
                    if ($nIndex !== false) {
                        $initialPairIndex = (int) $nIndex;
                    }
                }
            }
        } elseif ($requestedPair && is_numeric($requestedPair)) {
            $initialPairIndex = max(0, ((int) $requestedPair) - 1);
        }

        return view('judge.scoring.index', compact(
            'categoryName',
            'categorySlug',
            'iconPath',
            'maleCandidates',
            'femaleCandidates',
            'scores',
            'initialPairIndex'
        ));
    }

    public function production(Request $request)
    {
        return $this->renderScoringView(
            $request,
            'Production',
            'production',
            'M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5'
        );
    }

    public function fitness(Request $request)
    {
        return $this->renderScoringView(
            $request,
            'Fitness',
            'fitness',
            'M3.75 13.5l10.5-10.5m0 0L18 6.75M14.25 3l3.75 3.75M3 14.25l3.75 3.75m0 0l10.5-10.5M6.75 18L3 14.25'
        );
    }

    public function traditionalAttire(Request $request)
    {
        return $this->renderScoringView(
            $request,
            'Traditional Attire',
            'traditional-attire',
            'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09z'
        );
    }

    public function indigenousAttire(Request $request)
    {
        return $this->renderScoringView(
            $request,
            'Indigenous Attire',
            'indigenous-attire',
            'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 21v-2.25m-6.364-.386l1.591-1.591M3 12h2.25m.386-6.364l1.591 1.591M12 18.75a6.75 6.75 0 100-13.5 6.75 6.75 0 000 13.5z'
        );
    }

    public function qa(Request $request)
    {
        return $this->renderScoringView(
            $request,
            'Q & A',
            'qa',
            'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09z'
        );
    }

    /**
     * Submit score for a candidate.
     * Handles both built-in categories and dynamic custom categories (slug = 'custom:{key}').
     */
    public function saveScore(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'candidate_id' => 'required|exists:candidates,id',
            'score' => 'required|numeric|min:1|max:10',
        ]);

        if (in_array($validated['category'], ['qa', 'qanda'], true)) {
            $qualifiedIds = Candidate::getTop5QualifiedIds();
            if (! in_array((int) $validated['candidate_id'], $qualifiedIds, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Candidate is not qualified for Q&A (Top 5 only).',
                ], 422);
            }
        }

        $judgeId = Auth::id();

        // Handle dynamic custom categories
        if (str_starts_with($validated['category'], 'custom:')) {
            $catKey = substr($validated['category'], 7);
            $scoreObj = CustomCategoryScore::updateOrCreate(
                [
                    'candidate_id' => $validated['candidate_id'],
                    'judge_id' => $judgeId,
                    'category_key' => $catKey,
                ],
                ['score' => $validated['score']]
            );

            try {
                broadcast(new ScoreSubmitted(
                    $validated['category'],
                    (int) $validated['candidate_id'],
                    (int) $judgeId,
                    (float) $scoreObj->score,
                    'saved'
                ));
            } catch (\Throwable $e) {
                Log::warning('Real-time score broadcast failed: '.$e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Score submitted successfully!',
                'score' => number_format((float) $scoreObj->score, 2),
                'candidate_id' => $validated['candidate_id'],
            ]);
        }

        $modelClass = $this->getScoreModel($validated['category']);
        if (! $modelClass) {
            return response()->json(['success' => false, 'message' => 'Invalid category'], 400);
        }

        $scoreObj = $modelClass::updateOrCreate(
            [
                'candidate_id' => $validated['candidate_id'],
                'judge_id' => $judgeId,
            ],
            [
                'score' => $validated['score'],
            ]
        );

        // Broadcast real-time event to Reverb & Echo listeners
        try {
            broadcast(new ScoreSubmitted(
                $validated['category'],
                (int) $validated['candidate_id'],
                (int) $judgeId,
                (float) $scoreObj->score,
                'saved'
            ));
        } catch (\Throwable $e) {
            Log::warning('Real-time score broadcast failed: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Score submitted successfully!',
            'score' => number_format((float) $scoreObj->score, 2),
            'candidate_id' => $validated['candidate_id'],
        ]);
    }

    /**
     * Reset score for a candidate.
     * Handles both built-in categories and dynamic custom categories (slug = 'custom:{key}').
     */
    public function resetScore(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'candidate_id' => 'required|exists:candidates,id',
        ]);

        $judgeId = Auth::id();

        // Handle dynamic custom categories
        if (str_starts_with($validated['category'], 'custom:')) {
            $catKey = substr($validated['category'], 7);
            CustomCategoryScore::where('candidate_id', $validated['candidate_id'])
                ->where('judge_id', $judgeId)
                ->where('category_key', $catKey)
                ->delete();

            try {
                broadcast(new ScoreSubmitted(
                    $validated['category'],
                    (int) $validated['candidate_id'],
                    (int) $judgeId,
                    null,
                    'reset'
                ));
            } catch (\Throwable $e) {
                Log::warning('Real-time score reset broadcast failed: '.$e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Score reset successfully!',
                'candidate_id' => $validated['candidate_id'],
            ]);
        }

        $modelClass = $this->getScoreModel($validated['category']);
        if (! $modelClass) {
            return response()->json(['success' => false, 'message' => 'Invalid category'], 400);
        }

        $modelClass::where('candidate_id', $validated['candidate_id'])
            ->where('judge_id', $judgeId)
            ->delete();

        // Broadcast real-time reset event to Reverb & Echo listeners
        try {
            broadcast(new ScoreSubmitted(
                $validated['category'],
                (int) $validated['candidate_id'],
                (int) $judgeId,
                null,
                'reset'
            ));
        } catch (\Throwable $e) {
            Log::warning('Real-time score reset broadcast failed: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Score reset successfully!',
            'candidate_id' => $validated['candidate_id'],
        ]);
    }
}
