<?php

namespace App\Http\Controllers\Judge;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\ProductionScore;
use App\Models\FitnessScore;
use App\Models\TraditionalAttireScore;
use App\Models\IndigenousAttireScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JudgeScoringController extends Controller
{
    /**
     * Get model class by category slug.
     */
    protected function getScoreModel(string $category)
    {
        return match ($category) {
            'production'        => ProductionScore::class,
            'fitness'           => FitnessScore::class,
            'traditional-attire'=> TraditionalAttireScore::class,
            'indigenous-attire' => IndigenousAttireScore::class,
            default             => null,
        };
    }

    /**
     * Display scoring pad view for category.
     */
    protected function renderScoringView(string $categoryName, string $categorySlug, string $iconPath)
    {
        $judgeId = Auth::id();
        $candidates = Candidate::orderBy('candidate_number')->get();

        $modelClass = $this->getScoreModel($categorySlug);
        $rawScores = $modelClass::where('judge_id', $judgeId)->get()->keyBy('candidate_id');

        $scores = [];
        foreach ($rawScores as $candId => $sObj) {
            $scores[$candId] = (float) $sObj->score;
        }

        $maleCandidates = $candidates->filter(fn($c) => $c->gender === 'Male');
        $femaleCandidates = $candidates->filter(fn($c) => $c->gender === 'Female');

        return view('judge.scoring.index', compact(
            'categoryName',
            'categorySlug',
            'iconPath',
            'maleCandidates',
            'femaleCandidates',
            'scores'
        ));
    }

    public function production()
    {
        return $this->renderScoringView(
            'Production',
            'production',
            'M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5'
        );
    }

    public function fitness()
    {
        return $this->renderScoringView(
            'Fitness',
            'fitness',
            'M3.75 13.5l10.5-10.5m0 0L18 6.75M14.25 3l3.75 3.75M3 14.25l3.75 3.75m0 0l10.5-10.5M6.75 18L3 14.25'
        );
    }

    public function traditionalAttire()
    {
        return $this->renderScoringView(
            'Traditional Attire',
            'traditional-attire',
            'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09z'
        );
    }

    public function indigenousAttire()
    {
        return $this->renderScoringView(
            'Indigenous Attire',
            'indigenous-attire',
            'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 21v-2.25m-6.364-.386l1.591-1.591M3 12h2.25m.386-6.364l1.591 1.591M12 18.75a6.75 6.75 0 100-13.5 6.75 6.75 0 000 13.5z'
        );
    }

    /**
     * Submit score for a candidate.
     */
    public function saveScore(Request $request)
    {
        $validated = $request->validate([
            'category'     => 'required|string',
            'candidate_id' => 'required|exists:candidates,id',
            'score'        => 'required|numeric|min:1|max:10',
        ]);

        $modelClass = $this->getScoreModel($validated['category']);
        if (!$modelClass) {
            return response()->json(['success' => false, 'message' => 'Invalid category'], 400);
        }

        $judgeId = Auth::id();

        $scoreObj = $modelClass::updateOrCreate(
            [
                'candidate_id' => $validated['candidate_id'],
                'judge_id'     => $judgeId,
            ],
            [
                'score' => $validated['score'],
            ]
        );

        return response()->json([
            'success'      => true,
            'message'      => 'Score submitted successfully!',
            'score'        => number_format((float) $scoreObj->score, 2),
            'candidate_id' => $validated['candidate_id'],
        ]);
    }

    /**
     * Reset score for a candidate.
     */
    public function resetScore(Request $request)
    {
        $validated = $request->validate([
            'category'     => 'required|string',
            'candidate_id' => 'required|exists:candidates,id',
        ]);

        $modelClass = $this->getScoreModel($validated['category']);
        if (!$modelClass) {
            return response()->json(['success' => false, 'message' => 'Invalid category'], 400);
        }

        $judgeId = Auth::id();

        $modelClass::where('candidate_id', $validated['candidate_id'])
            ->where('judge_id', $judgeId)
            ->delete();

        return response()->json([
            'success'      => true,
            'message'      => 'Score reset successfully!',
            'candidate_id' => $validated['candidate_id'],
        ]);
    }
}
