<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Pageant;
use App\Models\Score;
use App\Models\User;
use App\Services\TabulationService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected TabulationService $tabulationService;

    public function __construct(TabulationService $tabulationService)
    {
        $this->tabulationService = $tabulationService;
    }

    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        // Global stats
        $totalCandidates = Candidate::count();
        $totalJudges = User::where('role', 'judge')->count();
        $totalScores = Score::count();
        $totalPageants = Pageant::count();
        $activePageants = Pageant::where('status', 'active')->with(['categories.criteria', 'candidates', 'judgeAssignments.judge'])->get();

        // Get leading candidate from the first active pageant
        $leadingCandidate = null;
        $activePageant = $activePageants->first();
        if ($activePageant) {
            $leadingCandidate = $this->tabulationService->getLeadingCandidate($activePageant);
        }

        // Category leaders for active pageant
        $categoryLeaders = [];
        $overallRankings = [];
        $recentScores = [];
        $judgeProgress = [];
        $submissionProgress = 0;

        if ($activePageant) {
            foreach ($activePageant->categories as $category) {
                $categoryRankings = $this->tabulationService->calculateCategoryRankings($activePageant, $category);
                if (!empty($categoryRankings)) {
                    $categoryLeaders[] = [
                        'category_name' => $category->name,
                        'leader' => $categoryRankings[0],
                    ];
                }
            }

            $overallRankings = $this->tabulationService->calculateOverallRankings($activePageant);
            $recentScores = $this->tabulationService->getRecentScores($activePageant, 8);
            $judgeProgress = $this->tabulationService->getJudgeProgress($activePageant);

            $stats = $this->tabulationService->getDashboardStats($activePageant);
            $submissionProgress = $stats['submission_progress'];
        }

        return view('admin.dashboard', compact(
            'totalCandidates',
            'totalJudges',
            'totalScores',
            'totalPageants',
            'activePageants',
            'activePageant',
            'leadingCandidate',
            'categoryLeaders',
            'overallRankings',
            'recentScores',
            'judgeProgress',
            'submissionProgress',
        ));
    }

    /**
     * API endpoint for real-time dashboard data.
     */
    public function liveStats(Request $request)
    {
        $pageantId = $request->input('pageant_id');
        $pageant = $pageantId ? Pageant::find($pageantId) : Pageant::where('status', 'active')->first();

        if (!$pageant) {
            return response()->json([
                'total_candidates' => Candidate::count(),
                'total_judges' => User::where('role', 'judge')->count(),
                'total_scores' => Score::count(),
                'leading_candidate' => null,
                'category_leaders' => [],
                'overall_rankings' => [],
                'recent_scores' => [],
                'judge_progress' => [],
                'submission_progress' => 0,
            ]);
        }

        $stats = $this->tabulationService->getDashboardStats($pageant);
        $categoryLeaders = [];

        foreach ($pageant->categories as $category) {
            $categoryRankings = $this->tabulationService->calculateCategoryRankings($pageant, $category);
            if (!empty($categoryRankings)) {
                $categoryLeaders[] = [
                    'category_name' => $category->name,
                    'leader' => $categoryRankings[0],
                ];
            }
        }

        return response()->json([
            'total_candidates' => $stats['total_candidates'],
            'total_judges' => $stats['total_judges'],
            'total_scores' => $stats['total_scores'],
            'submission_progress' => $stats['submission_progress'],
            'leading_candidate' => $stats['leading_candidate'],
            'category_leaders' => $categoryLeaders,
            'overall_rankings' => $this->tabulationService->calculateOverallRankings($pageant),
            'recent_scores' => $this->tabulationService->getRecentScores($pageant, 8),
            'judge_progress' => $this->tabulationService->getJudgeProgress($pageant),
        ]);
    }
}
