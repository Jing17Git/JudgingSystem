<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CriteriaSetting;
use App\Models\FitnessScore;
use App\Models\IndigenousAttireScore;
use App\Models\Pageant;
use App\Models\ProductionScore;
use App\Models\QaScore;
use App\Models\TraditionalAttireScore;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        $data = self::calculateDashboardData();

        return view('admin.dashboard', $data);
    }

    /**
     * API endpoint for real-time dashboard data updates.
     */
    public function liveStats(Request $request)
    {
        $data = self::calculateDashboardData();

        return response()->json($data);
    }

    /**
     * Core tabulation and analytics calculation for dashboard.
     */
    public static function calculateDashboardData(): array
    {
        $candidates = Candidate::orderBy('gender')->orderBy('candidate_number')->get();
        $maleCandidates = $candidates->filter(fn ($c) => $c->gender === 'Male');
        $femaleCandidates = $candidates->filter(fn ($c) => $c->gender === 'Female');
        $candCount = $candidates->count();

        $judges = User::where('role', 'judge')->where('is_active', true)->get();
        $judgeCount = $judges->count();

        $activePageant = Pageant::where('status', 'active')->first();

        // Criteria weights
        $weights = CriteriaSetting::getPercentageMap();
        $prodWeight = (float) ($weights['production'] ?? 25.0);
        $fitWeight = (float) ($weights['fitness'] ?? 25.0);
        $tradWeight = (float) ($weights['traditional_attire'] ?? 25.0);
        $indigWeight = (float) ($weights['indigenous_attire'] ?? 25.0);

        // Fetch scores
        $prodScores = ProductionScore::all();
        $fitScores = FitnessScore::all();
        $tradScores = TraditionalAttireScore::all();
        $indigScores = IndigenousAttireScore::all();
        $qaScores = QaScore::all();

        $prodGrouped = $prodScores->groupBy('candidate_id');
        $fitGrouped = $fitScores->groupBy('candidate_id');
        $tradGrouped = $tradScores->groupBy('candidate_id');
        $indigGrouped = $indigScores->groupBy('candidate_id');
        $qaGrouped = $qaScores->groupBy('candidate_id');

        // Progress calculations
        $expectedPerCategory = $candCount * $judgeCount;
        $prodCount = $prodScores->count();
        $fitCount = $fitScores->count();
        $tradCount = $tradScores->count();
        $indigCount = $indigScores->count();
        $qaCount = $qaScores->count();

        // Top 5 Finalists per division for QA
        $expectedQa = min(10, $candCount) * $judgeCount;
        $totalExpected = ($expectedPerCategory * 4) + ($expectedQa > 0 ? $expectedQa : 0);
        $totalScores = $prodCount + $fitCount + $tradCount + $indigCount + $qaCount;
        $submissionProgress = $totalExpected > 0 ? min(100, round(($totalScores / $totalExpected) * 100, 1)) : 0;

        $categoryProgress = [
            'production' => [
                'name' => 'Production',
                'count' => $prodCount,
                'expected' => $expectedPerCategory,
                'percentage' => $expectedPerCategory > 0 ? min(100, round(($prodCount / $expectedPerCategory) * 100, 1)) : 0,
                'color' => 'bg-blue-500',
                'weight' => (int) $prodWeight,
            ],
            'fitness' => [
                'name' => 'Fitness',
                'count' => $fitCount,
                'expected' => $expectedPerCategory,
                'percentage' => $expectedPerCategory > 0 ? min(100, round(($fitCount / $expectedPerCategory) * 100, 1)) : 0,
                'color' => 'bg-emerald-500',
                'weight' => (int) $fitWeight,
            ],
            'indigenous' => [
                'name' => 'Indigenous Attire',
                'count' => $indigCount,
                'expected' => $expectedPerCategory,
                'percentage' => $expectedPerCategory > 0 ? min(100, round(($indigCount / $expectedPerCategory) * 100, 1)) : 0,
                'color' => 'bg-purple-500',
                'weight' => (int) $indigWeight,
            ],
            'traditional' => [
                'name' => 'Traditional Attire',
                'count' => $tradCount,
                'expected' => $expectedPerCategory,
                'percentage' => $expectedPerCategory > 0 ? min(100, round(($tradCount / $expectedPerCategory) * 100, 1)) : 0,
                'color' => 'bg-amber-500',
                'weight' => (int) $tradWeight,
            ],
            'qa' => [
                'name' => 'Final Q & A',
                'count' => $qaCount,
                'expected' => $expectedQa > 0 ? $expectedQa : $expectedPerCategory,
                'percentage' => ($expectedQa > 0 ? $expectedQa : $expectedPerCategory) > 0 ? min(100, round(($qaCount / ($expectedQa > 0 ? $expectedQa : $expectedPerCategory)) * 100, 1)) : 0,
                'color' => 'bg-rose-500',
                'weight' => 70,
            ],
        ];

        // Overall candidate score calculations
        $candidateRankings = [];
        $catScoreTrack = [
            'production' => [],
            'fitness' => [],
            'indigenous' => [],
            'traditional' => [],
            'qa' => [],
        ];

        foreach ($candidates as $c) {
            $pSum = isset($prodGrouped[$c->id]) ? (float) $prodGrouped[$c->id]->sum('score') : 0;
            $fSum = isset($fitGrouped[$c->id]) ? (float) $fitGrouped[$c->id]->sum('score') : 0;
            $tSum = isset($tradGrouped[$c->id]) ? (float) $tradGrouped[$c->id]->sum('score') : 0;
            $iSum = isset($indigGrouped[$c->id]) ? (float) $indigGrouped[$c->id]->sum('score') : 0;
            $qSum = isset($qaGrouped[$c->id]) ? (float) $qaGrouped[$c->id]->sum('score') : 0;

            $pAvg = $judgeCount > 0 ? ($pSum / $judgeCount) : 0;
            $fAvg = $judgeCount > 0 ? ($fSum / $judgeCount) : 0;
            $tAvg = $judgeCount > 0 ? ($tSum / $judgeCount) : 0;
            $iAvg = $judgeCount > 0 ? ($iSum / $judgeCount) : 0;
            $qAvg = $judgeCount > 0 ? ($qSum / $judgeCount) : 0;

            $catScoreTrack['production'][$c->id] = ['cand' => $c, 'avg' => $pAvg];
            $catScoreTrack['fitness'][$c->id] = ['cand' => $c, 'avg' => $fAvg];
            $catScoreTrack['traditional'][$c->id] = ['cand' => $c, 'avg' => $tAvg];
            $catScoreTrack['indigenous'][$c->id] = ['cand' => $c, 'avg' => $iAvg];
            $catScoreTrack['qa'][$c->id] = ['cand' => $c, 'avg' => $qAvg];

            $pWeighted = $pAvg * ($prodWeight / 100.0);
            $fWeighted = $fAvg * ($fitWeight / 100.0);
            $tWeighted = $tAvg * ($tradWeight / 100.0);
            $iWeighted = $iAvg * ($indigWeight / 100.0);
            $grandTotal = $pWeighted + $fWeighted + $tWeighted + $iWeighted;

            $candidateRankings[] = [
                'candidate_id' => $c->id,
                'candidate_number' => $c->candidate_number,
                'candidate_name' => $c->display_name,
                'gender' => $c->gender,
                'origin' => $c->origin,
                'photo_url' => $c->photo_url,
                'production_avg' => round($pAvg, 2),
                'fitness_avg' => round($fAvg, 2),
                'traditional_avg' => round($tAvg, 2),
                'indigenous_avg' => round($iAvg, 2),
                'qa_avg' => round($qAvg, 2),
                'total_score' => round($grandTotal, 2),
            ];
        }

        // Sort overall rankings descending
        usort($candidateRankings, function ($a, $b) {
            if ($a['total_score'] == $b['total_score']) {
                return $a['candidate_number'] <=> $b['candidate_number'];
            }

            return $b['total_score'] <=> $a['total_score'];
        });

        // Assign overall ranks
        $r = 1;
        $prevTot = null;
        $same = 1;
        foreach ($candidateRankings as &$item) {
            if ($item['total_score'] <= 0) {
                $item['rank'] = '—';

                continue;
            }
            if ($prevTot !== null && $item['total_score'] == $prevTot) {
                $item['rank'] = $r - $same;
                $same++;
            } else {
                $item['rank'] = $r;
                $same = 1;
            }
            $prevTot = $item['total_score'];
            $r++;
        }
        unset($item);

        // Leading Candidate (Overall, Male, Female)
        $leadingCandidate = null;
        $leadingMale = null;
        $leadingFemale = null;

        foreach ($candidateRankings as $cr) {
            if ($cr['total_score'] > 0) {
                if (! $leadingCandidate) {
                    $leadingCandidate = $cr;
                }
                if (! $leadingMale && $cr['gender'] === 'Male') {
                    $leadingMale = $cr;
                }
                if (! $leadingFemale && $cr['gender'] === 'Female') {
                    $leadingFemale = $cr;
                }
            }
        }

        // Category Leaders
        $categoryLeaders = [];
        $catMeta = [
            'production' => ['name' => 'Production', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50 border-blue-200'],
            'fitness' => ['name' => 'Fitness', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50 border-emerald-200'],
            'indigenous' => ['name' => 'Indigenous Attire', 'color' => 'text-purple-600', 'bg' => 'bg-purple-50 border-purple-200'],
            'traditional' => ['name' => 'Traditional Attire', 'color' => 'text-amber-600', 'bg' => 'bg-amber-50 border-amber-200'],
            'qa' => ['name' => 'Final Q & A', 'color' => 'text-rose-600', 'bg' => 'bg-rose-50 border-rose-200'],
        ];

        foreach ($catMeta as $catKey => $meta) {
            $scores = $catScoreTrack[$catKey];
            uasort($scores, fn ($a, $b) => $b['avg'] <=> $a['avg']);
            $first = reset($scores);

            if ($first && $first['avg'] > 0) {
                $cand = $first['cand'];
                $categoryLeaders[] = [
                    'category_key' => $catKey,
                    'category_name' => $meta['name'],
                    'color' => $meta['color'],
                    'bg' => $meta['bg'],
                    'candidate_name' => $cand->display_name,
                    'candidate_number' => $cand->candidate_number,
                    'candidate_gender' => $cand->gender,
                    'photo_url' => $cand->photo_url,
                    'score' => round($first['avg'], 2),
                ];
            }
        }

        // Judge Submission Progress
        $judgeProgress = [];
        $expectedPerJudge = $candCount * 4 + (min(10, $candCount)); // 4 categories + finalists QA
        if ($expectedPerJudge === 0) {
            $expectedPerJudge = 1;
        }

        foreach ($judges as $j) {
            $jProd = $prodScores->where('judge_id', $j->id)->count();
            $jFit = $fitScores->where('judge_id', $j->id)->count();
            $jTrad = $tradScores->where('judge_id', $j->id)->count();
            $jInd = $indigScores->where('judge_id', $j->id)->count();
            $jQa = $qaScores->where('judge_id', $j->id)->count();

            $submitted = $jProd + $jFit + $jTrad + $jInd + $jQa;
            $pct = round(($submitted / $expectedPerJudge) * 100, 1);

            $judgeProgress[] = [
                'judge_id' => $j->id,
                'judge_name' => $j->name,
                'email' => $j->email,
                'submitted' => $submitted,
                'expected' => $expectedPerJudge,
                'percentage' => min(100, $pct),
                'breakdown' => [
                    'production' => $jProd,
                    'fitness' => $jFit,
                    'indigenous' => $jInd,
                    'traditional' => $jTrad,
                    'qa' => $jQa,
                ],
            ];
        }

        // Recent Scores Feed (merged and sorted from all 5 models)
        $candMap = $candidates->keyBy('id');
        $judgeMap = $judges->keyBy('id');

        $recentFeed = collect();

        foreach ([
            ['data' => $prodScores,  'cat' => 'Production',  'badge' => 'badge-blue',   'max' => 10],
            ['data' => $fitScores,   'cat' => 'Fitness',     'badge' => 'badge-emerald', 'max' => 10],
            ['data' => $tradScores,  'cat' => 'Traditional', 'badge' => 'badge-amber',  'max' => 10],
            ['data' => $indigScores, 'cat' => 'Indigenous',  'badge' => 'badge-purple', 'max' => 10],
            ['data' => $qaScores,    'cat' => 'Q & A',       'badge' => 'badge-rose',   'max' => 100],
        ] as $entry) {
            foreach ($entry['data'] as $s) {
                $j = $judgeMap[$s->judge_id] ?? null;
                $c = $candMap[$s->candidate_id] ?? null;
                if ($j && $c) {
                    $recentFeed->push([
                        'judge_name' => $j->name,
                        'candidate_number' => $c->candidate_number,
                        'candidate_name' => $c->display_name,
                        'candidate_gender' => $c->gender,
                        'category_name' => $entry['cat'],
                        'badge_class' => $entry['badge'],
                        'score' => number_format((float) $s->score, 1),
                        'max_score' => $entry['max'],
                        'submitted_at' => $s->updated_at ? $s->updated_at->diffForHumans() : 'just now',
                        'raw_time' => $s->updated_at ? $s->updated_at->timestamp : 0,
                    ]);
                }
            }
        }

        $recentScores = $recentFeed->sortByDesc('raw_time')->take(8)->values()->all();

        return [
            'totalCandidates' => $candCount,
            'totalMaleCandidates' => $maleCandidates->count(),
            'totalFemaleCandidates' => $femaleCandidates->count(),
            'totalJudges' => $judgeCount,
            'totalScores' => $totalScores,
            'totalExpectedScores' => $totalExpected,
            'submissionProgress' => $submissionProgress,
            'activePageant' => $activePageant,
            'leadingCandidate' => $leadingCandidate,
            'leadingMale' => $leadingMale,
            'leadingFemale' => $leadingFemale,
            'categoryProgress' => $categoryProgress,
            'overallRankings' => $candidateRankings,
            'categoryLeaders' => $categoryLeaders,
            'judgeProgress' => $judgeProgress,
            'recentScores' => $recentScores,
        ];
    }
}
