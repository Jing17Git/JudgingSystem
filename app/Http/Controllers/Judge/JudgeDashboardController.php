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

class JudgeDashboardController extends Controller
{
    /**
     * Display the judge dashboard.
     */
    public function index()
    {
        $judgeId = Auth::id();

        $totalCandidates = Candidate::count();
        $maleCandidatesCount = Candidate::where('gender', 'Male')->count();
        $femaleCandidatesCount = Candidate::where('gender', 'Female')->count();

        // 4 Categories
        $categories = [
            [
                'name' => 'Production',
                'slug' => 'production',
                'route' => 'judge.production.index',
                'icon' => 'M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5',
                'submitted' => ProductionScore::where('judge_id', $judgeId)->count(),
            ],
            [
                'name' => 'Fitness',
                'slug' => 'fitness',
                'route' => 'judge.fitness.index',
                'icon' => 'M3.75 13.5l10.5-10.5m0 0L18 6.75M14.25 3l3.75 3.75M3 14.25l3.75 3.75m0 0l10.5-10.5M6.75 18L3 14.25',
                'submitted' => FitnessScore::where('judge_id', $judgeId)->count(),
            ],
            [
                'name' => 'Traditional Attire',
                'slug' => 'traditional-attire',
                'route' => 'judge.traditional-attire.index',
                'icon' => 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09z',
                'submitted' => TraditionalAttireScore::where('judge_id', $judgeId)->count(),
            ],
            [
                'name' => 'Indigenous Attire',
                'slug' => 'indigenous-attire',
                'route' => 'judge.indigenous-attire.index',
                'icon' => 'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 21v-2.25m-6.364-.386l1.591-1.591M3 12h2.25m.386-6.364l1.591 1.591M12 18.75a6.75 6.75 0 100-13.5 6.75 6.75 0 000 13.5z',
                'submitted' => IndigenousAttireScore::where('judge_id', $judgeId)->count(),
            ],
        ];

        $totalAssignedCategories = count($categories);
        $totalRequiredScores = $totalCandidates * $totalAssignedCategories;
        $totalSubmittedScores = array_sum(array_column($categories, 'submitted'));
        $overallProgressPercent = $totalRequiredScores > 0 ? round(($totalSubmittedScores / $totalRequiredScores) * 100) : 0;

        return view('judge.dashboard', compact(
            'totalCandidates',
            'maleCandidatesCount',
            'femaleCandidatesCount',
            'categories',
            'totalAssignedCategories',
            'totalRequiredScores',
            'totalSubmittedScores',
            'overallProgressPercent'
        ));
    }
}
