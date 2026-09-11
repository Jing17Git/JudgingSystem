<?php

namespace App\Http\Controllers\Judge;

use App\Events\ScoreSubmitted;
use App\Http\Controllers\Controller;
use App\Models\AuditRecord;
use App\Models\Candidate;
use App\Models\CriteriaSetting;
use App\Models\CustomCategoryScore;
use App\Models\FitnessScore;
use App\Models\IndigenousAttireScore;
use App\Models\JudgeCategorySubmission;
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
        // Check if category is disabled
        $settingKey = str_replace('-', '_', $categorySlug);
        $searchKeys = [$categorySlug, $settingKey, str_replace('_', '-', $categorySlug)];
        if (in_array($categorySlug, ['qa', 'qanda'])) {
            $searchKeys[] = 'qa_score';
            $searchKeys[] = 'qa-score';
        }
        $categorySetting = CriteriaSetting::whereIn('key', $searchKeys)->first();
        $isCategoryDisabled = $categorySetting && !$categorySetting->is_enabled;

        $judgeId = Auth::id();
        $candidates = Candidate::orderBy('candidate_number')->get();

        $isFinalized = JudgeCategorySubmission::isFinalized($judgeId, $categorySlug) || $isCategoryDisabled;
        $submission = JudgeCategorySubmission::getSubmission($judgeId, $categorySlug);
        $finalizedAt = $submission?->finalized_at;

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
            'initialPairIndex',
            'isFinalized',
            'finalizedAt'
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

        // Guard: Check if scoring for this category is already finalized & disabled
        $catKeyCheck = str_starts_with($validated['category'], 'custom:')
            ? substr($validated['category'], 7)
            : str_replace('-', '_', $validated['category']);
        $searchKeys = [$catKeyCheck, $validated['category'], str_replace('_', '-', $validated['category'])];
        if (in_array($validated['category'], ['qa', 'qanda'])) {
            $searchKeys[] = 'qa_score';
            $searchKeys[] = 'qa-score';
        }
        $catSetting = CriteriaSetting::whereIn('key', $searchKeys)->first();

        if (JudgeCategorySubmission::isFinalized($judgeId, $validated['category']) || ($catSetting && !$catSetting->is_enabled)) {
            return response()->json([
                'success' => false,
                'message' => 'Scoring for this category is finalized and disabled. Modifications are locked.',
            ], 403);
        }

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

        // Guard: Check if scoring for this category is already finalized & disabled
        $catKeyCheck = str_starts_with($validated['category'], 'custom:')
            ? substr($validated['category'], 7)
            : str_replace('-', '_', $validated['category']);
        $searchKeys = [$catKeyCheck, $validated['category'], str_replace('_', '-', $validated['category'])];
        if (in_array($validated['category'], ['qa', 'qanda'])) {
            $searchKeys[] = 'qa_score';
            $searchKeys[] = 'qa-score';
        }
        $catSetting = CriteriaSetting::whereIn('key', $searchKeys)->first();

        if (JudgeCategorySubmission::isFinalized($judgeId, $validated['category']) || ($catSetting && !$catSetting->is_enabled)) {
            return response()->json([
                'success' => false,
                'message' => 'Scoring for this category is finalized and disabled. Reset is locked.',
            ], 403);
        }

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

    /**
     * Finalize scoring for a category by the authenticated judge.
     *
     * This creates a per-judge JudgeCategorySubmission record that locks only THIS
     * judge's scoring inputs. It does NOT globally disable the category (is_enabled).
     * Global category locking is controlled exclusively by admin via the management page.
     */
    public function finalizeCategory(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
        ]);

        $judgeId = Auth::id();
        $category = $validated['category'];

        // If already finalized, return success state
        if (JudgeCategorySubmission::isFinalized($judgeId, $category)) {
            $submission = JudgeCategorySubmission::getSubmission($judgeId, $category);

            return response()->json([
                'success'      => true,
                'message'      => 'Category scoring is already finalized.',
                'is_finalized' => true,
                'finalized_at' => $submission?->finalized_at?->format('M d, Y h:i A'),
            ]);
        }

        // Create per-judge submission lock
        $submission = JudgeCategorySubmission::finalize($judgeId, $category);

        // Lock the category afterwards so that it is marked locked
        $catKeyCheck = str_starts_with($category, 'custom:')
            ? substr($category, 7)
            : str_replace('-', '_', $category);
        $searchKeys = [$catKeyCheck, $category, str_replace('_', '-', $category)];
        if (in_array($category, ['qa', 'qanda'])) {
            $searchKeys[] = 'qa_score';
            $searchKeys[] = 'qa-score';
        }
        $catSetting = CriteriaSetting::whereIn('key', $searchKeys)->first();
        if ($catSetting) {
            $catSetting->update(['is_enabled' => false]);
        }

        // Record security audit
        try {
            AuditRecord::create([
                'event_type'         => 'category_finalized',
                'category'           => 'scoring',
                'user_id'            => $judgeId,
                'user_name'          => Auth::user()?->name ?? "Judge #{$judgeId}",
                'user_role'          => 'judge',
                'action_description' => "Judge finalized & submitted their scores for '{$category}'. Their scoring inputs are now locked.",
                'ip_address'         => $request->ip(),
                'status'             => 'success',
                'risk_level'         => 'low',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Audit logging for category finalization failed: '.$e->getMessage());
        }

        return response()->json([
            'success'      => true,
            'message'      => 'Scores finalized and submitted successfully! Your scoring for this category is now locked.',
            'is_finalized' => true,
            'finalized_at' => $submission->finalized_at?->format('M d, Y h:i A'),
        ]);
    }
}
