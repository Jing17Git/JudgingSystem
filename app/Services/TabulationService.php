<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Category;
use App\Models\Pageant;
use App\Models\Score;
use Illuminate\Support\Facades\DB;

class TabulationService
{
    /**
     * Get dashboard statistics for all pageants or a specific pageant.
     */
    public function getDashboardStats(?Pageant $pageant = null): array
    {
        if ($pageant) {
            $assignmentIds = $pageant->judgeAssignments()->pluck('id');
            $totalScores = Score::whereIn('judge_assignment_id', $assignmentIds)->count();
            $totalCandidates = $pageant->candidates()->count();
            $totalJudges = $pageant->judgeAssignments()->count();

            // Calculate total expected scores
            $totalCriteria = 0;
            foreach ($pageant->categories as $category) {
                $totalCriteria += $category->criteria()->count();
            }
            $expectedScores = $totalCandidates * $totalJudges * $totalCriteria;

            return [
                'total_candidates' => $totalCandidates,
                'total_judges' => $totalJudges,
                'total_scores' => $totalScores,
                'expected_scores' => $expectedScores,
                'submission_progress' => $expectedScores > 0 ? round(($totalScores / $expectedScores) * 100, 1) : 0,
                'leading_candidate' => $this->getLeadingCandidate($pageant),
            ];
        }

        // Global stats
        return [
            'total_candidates' => Candidate::count(),
            'total_judges' => \App\Models\User::where('role', 'judge')->count(),
            'total_scores' => Score::count(),
            'total_pageants' => Pageant::count(),
            'active_pageants' => Pageant::where('status', 'active')->count(),
            'leading_candidate' => null,
        ];
    }

    /**
     * Get the leading candidate for a pageant.
     */
    public function getLeadingCandidate(Pageant $pageant): ?array
    {
        $rankings = $this->calculateOverallRankings($pageant);
        if (empty($rankings)) {
            return null;
        }

        $leader = $rankings[0];
        return [
            'id' => $leader['candidate_id'],
            'name' => $leader['candidate_name'],
            'candidate_number' => $leader['candidate_number'],
            'total_score' => $leader['total_score'],
        ];
    }

    /**
     * Calculate category rankings for a specific category.
     */
    public function calculateCategoryRankings(Pageant $pageant, Category $category): array
    {
        $criteria = $category->criteria;
        $candidates = $pageant->candidates;
        $assignmentIds = $pageant->judgeAssignments()->pluck('id');
        $judgeCount = $assignmentIds->count();

        if ($judgeCount === 0 || $criteria->isEmpty() || $candidates->isEmpty()) {
            return [];
        }

        $rankings = [];

        foreach ($candidates as $candidate) {
            $categoryScore = 0;

            foreach ($criteria as $criterion) {
                $scores = Score::where('candidate_id', $candidate->id)
                    ->where('criterion_id', $criterion->id)
                    ->whereIn('judge_assignment_id', $assignmentIds)
                    ->pluck('score');

                if ($scores->isNotEmpty()) {
                    $avgScore = $scores->avg();
                    // Normalize score to percentage of max, then apply weight
                    $normalizedScore = ($criterion->max_score > 0)
                        ? ($avgScore / $criterion->max_score) * $criterion->weight_percentage
                        : 0;
                    $categoryScore += $normalizedScore;
                }
            }

            $rankings[] = [
                'candidate_id' => $candidate->id,
                'candidate_number' => $candidate->candidate_number,
                'candidate_name' => $candidate->full_name,
                'category_score' => round($categoryScore, 2),
                'scores_received' => Score::where('candidate_id', $candidate->id)
                    ->whereIn('criterion_id', $criteria->pluck('id'))
                    ->whereIn('judge_assignment_id', $assignmentIds)
                    ->count(),
            ];
        }

        // Sort by score descending
        usort($rankings, fn($a, $b) => $b['category_score'] <=> $a['category_score']);

        // Add rank
        foreach ($rankings as $i => &$ranking) {
            $ranking['rank'] = $i + 1;
        }

        return $rankings;
    }

    /**
     * Calculate overall rankings across all categories.
     */
    public function calculateOverallRankings(Pageant $pageant): array
    {
        $categories = $pageant->categories;
        $candidates = $pageant->candidates;

        if ($categories->isEmpty() || $candidates->isEmpty()) {
            return [];
        }

        $rankings = [];

        foreach ($candidates as $candidate) {
            $totalScore = 0;

            foreach ($categories as $category) {
                $categoryRankings = $this->calculateCategoryRankings($pageant, $category);
                $candidateRanking = collect($categoryRankings)->firstWhere('candidate_id', $candidate->id);

                if ($candidateRanking) {
                    // Apply category weight to the category score
                    $weightedScore = $candidateRanking['category_score'] * ($category->weight_percentage / 100);
                    $totalScore += $weightedScore;
                }
            }

            $rankings[] = [
                'candidate_id' => $candidate->id,
                'candidate_number' => $candidate->candidate_number,
                'candidate_name' => $candidate->full_name,
                'total_score' => round($totalScore, 2),
                'photo_url' => $candidate->photo_url,
            ];
        }

        // Sort by score descending
        usort($rankings, fn($a, $b) => $b['total_score'] <=> $a['total_score']);

        // Add rank
        foreach ($rankings as $i => &$ranking) {
            $ranking['rank'] = $i + 1;
        }

        return $rankings;
    }

    /**
     * Get judge progress for a pageant.
     */
    public function getJudgeProgress(Pageant $pageant): array
    {
        $assignments = $pageant->judgeAssignments()->with('judge')->get();
        $totalCandidates = $pageant->candidates()->count();
        $totalCriteria = 0;

        foreach ($pageant->categories as $category) {
            $totalCriteria += $category->criteria()->count();
        }

        $expectedPerJudge = $totalCandidates * $totalCriteria;

        $progress = [];

        foreach ($assignments as $assignment) {
            $submitted = $assignment->scores()->count();
            $progress[] = [
                'judge_id' => $assignment->judge->id,
                'judge_name' => $assignment->judge->name,
                'submitted' => $submitted,
                'expected' => $expectedPerJudge,
                'percentage' => $expectedPerJudge > 0 ? round(($submitted / $expectedPerJudge) * 100, 1) : 0,
            ];
        }

        return $progress;
    }

    /**
     * Get recent score submissions for a pageant.
     */
    public function getRecentScores(Pageant $pageant, int $limit = 10): array
    {
        $assignmentIds = $pageant->judgeAssignments()->pluck('id');

        return Score::whereIn('judge_assignment_id', $assignmentIds)
            ->with(['judgeAssignment.judge', 'candidate', 'criterion.category'])
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get()
            ->map(function ($score) {
                return [
                    'judge_name' => $score->judgeAssignment->judge->name,
                    'candidate_name' => $score->candidate->full_name,
                    'candidate_number' => $score->candidate->candidate_number,
                    'criterion_name' => $score->criterion->name,
                    'category_name' => $score->criterion->category->name,
                    'score' => $score->score,
                    'max_score' => $score->criterion->max_score,
                    'submitted_at' => $score->updated_at->diffForHumans(),
                ];
            })
            ->toArray();
    }
}
