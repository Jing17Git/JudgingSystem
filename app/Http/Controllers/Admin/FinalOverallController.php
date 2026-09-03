<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CriteriaSetting;
use App\Models\FitnessScore;
use App\Models\IndigenousAttireScore;
use App\Models\ProductionScore;
use App\Models\QaScore;
use App\Models\TraditionalAttireScore;
use App\Models\User;
use Illuminate\Http\Request;

class FinalOverallController extends Controller
{
    /**
     * Display Final Overall Tabulation Table for Top 5 Finalists based on Q&A Scores.
     */
    public function index(Request $request)
    {
        $judges = User::where('role', 'judge')->where('is_active', true)->orderByRaw('judge_number IS NULL, judge_number ASC')->orderBy('name')->get();
        $judgeCount = $judges->count();

        $candidates = Candidate::all();

        // 1. Calculate Preliminary weighted totals to determine Top 5 Finalists
        $prelimWeights = CriteriaSetting::getPreliminaryMap();
        $prodWeight = (float) ($prelimWeights['production'] ?? 25.0);
        $fitWeight = (float) ($prelimWeights['fitness'] ?? 25.0);
        $tradWeight = (float) ($prelimWeights['traditional_attire'] ?? 25.0);
        $indigWeight = (float) ($prelimWeights['indigenous_attire'] ?? 25.0);

        // Fetch Final Criteria Split Weights (e.g. 30% Prelim / 70% Q&A)
        $finalWeights = CriteriaSetting::getFinalMap();
        $prelimWeight = (float) ($finalWeights['preliminary_score'] ?? 30.0);
        $qaWeight = (float) ($finalWeights['qa_score'] ?? 70.0);

        $prodScores = ProductionScore::all()->groupBy('candidate_id');
        $fitScores = FitnessScore::all()->groupBy('candidate_id');
        $tradScores = TraditionalAttireScore::all()->groupBy('candidate_id');
        $indigScores = IndigenousAttireScore::all()->groupBy('candidate_id');

        $prelimTotals = [];
        foreach ($candidates as $c) {
            $pSum = isset($prodScores[$c->id]) ? (float) $prodScores[$c->id]->sum('score') : 0;
            $fSum = isset($fitScores[$c->id]) ? (float) $fitScores[$c->id]->sum('score') : 0;
            $tSum = isset($tradScores[$c->id]) ? (float) $tradScores[$c->id]->sum('score') : 0;
            $iSum = isset($indigScores[$c->id]) ? (float) $indigScores[$c->id]->sum('score') : 0;

            $pAvg = $judgeCount > 0 ? ($pSum / $judgeCount) : 0;
            $fAvg = $judgeCount > 0 ? ($fSum / $judgeCount) : 0;
            $tAvg = $judgeCount > 0 ? ($tSum / $judgeCount) : 0;
            $iAvg = $judgeCount > 0 ? ($iSum / $judgeCount) : 0;

            $prelimTotals[$c->id] = ($pAvg * $prodWeight / 100.0)
                                  + ($fAvg * $fitWeight / 100.0)
                                  + ($tAvg * $tradWeight / 100.0)
                                  + ($iAvg * $indigWeight / 100.0);
        }

        // Rank all candidates in Preliminary to display their prelim rank
        $prelimRanks = [];
        foreach (['Male', 'Female'] as $gender) {
            $gCands = $candidates->filter(fn ($c) => $c->gender === $gender);
            $gTotals = [];
            foreach ($gCands as $c) {
                $gTotals[$c->id] = $prelimTotals[$c->id] ?? 0;
            }
            arsort($gTotals);
            $r = 1;
            foreach ($gTotals as $cid => $tot) {
                $prelimRanks[$cid] = $tot > 0 ? $r : null;
                $r++;
            }
        }

        // Select Top 5 Finalists per division based on preliminary results
        $top5Male = $candidates->filter(fn ($c) => $c->gender === 'Male')
            ->sort(function ($a, $b) use ($prelimTotals) {
                $totA = $prelimTotals[$a->id] ?? 0;
                $totB = $prelimTotals[$b->id] ?? 0;
                if ($totA == $totB) {
                    return $a->candidate_number <=> $b->candidate_number;
                }

                return $totB <=> $totA;
            })
            ->take(5)
            ->values();

        $top5Female = $candidates->filter(fn ($c) => $c->gender === 'Female')
            ->sort(function ($a, $b) use ($prelimTotals) {
                $totA = $prelimTotals[$a->id] ?? 0;
                $totB = $prelimTotals[$b->id] ?? 0;
                if ($totA == $totB) {
                    return $a->candidate_number <=> $b->candidate_number;
                }

                return $totB <=> $totA;
            })
            ->take(5)
            ->values();

        $finalists = $top5Male->concat($top5Female);

        // 2. Fetch Q&A scores
        $qaScores = QaScore::all()->groupBy('candidate_id');
        $rawQa = QaScore::all()->keyBy(fn ($s) => $s->candidate_id.'_'.$s->judge_id);

        $finalBreakdown = [];
        $judgeBreakdown = [];

        foreach ($finalists as $c) {
            $pScore = $prelimTotals[$c->id] ?? 0;
            $pWeighted = $pScore * ($prelimWeight / 100.0);

            $qSum = isset($qaScores[$c->id]) ? (float) $qaScores[$c->id]->sum('score') : 0;
            $qAvg = $judgeCount > 0 ? ($qSum / $judgeCount) : 0;
            $qWeighted = $qAvg * ($qaWeight / 100.0);

            $finalGrandTotal = $pWeighted + $qWeighted;

            $finalBreakdown[$c->id] = [
                'prelim_score' => $pScore,
                'prelim_weighted' => $pWeighted,
                'prelim_rank' => $prelimRanks[$c->id] ?? null,
                'qa_sum' => $qSum,
                'qa_avg' => $qAvg,
                'qa_weighted' => $qWeighted,
                'total' => $finalGrandTotal,
            ];

            // Individual judge Q&A breakdown for this candidate
            $cJudgeScores = [];
            foreach ($judges as $j) {
                $key = $c->id.'_'.$j->id;
                $qScore = isset($rawQa[$key]) ? (float) $rawQa[$key]->score : null;

                $cJudgeScores[] = [
                    'judge_id' => $j->id,
                    'judge_name' => $j->name,
                    'qa_score' => $qScore,
                ];
            }

            $judgeBreakdown[$c->id] = $cJudgeScores;
        }

        $candidatesJson = $finalists->map(function ($c) {
            return [
                'id' => $c->id,
                'candidate_number' => (int) $c->candidate_number,
                'display_name' => $c->display_name,
                'gender' => $c->gender,
            ];
        })->values();

        $judgesJson = $judges->map(function ($j) {
            return [
                'id'           => $j->id,
                'name'         => $j->name,
                'judge_number' => $j->judge_number ?? $j->id,
            ];
        })->values();

        $rawQaMap = $rawQa->map(fn ($s) => (float) $s->score);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'rawQa' => $rawQaMap,
                'prelimTotals' => $prelimTotals,
                'finalBreakdown' => $finalBreakdown,
            ]);
        }

        return view('admin.overall.final', compact(
            'judges',
            'top5Male',
            'top5Female',
            'finalists',
            'finalBreakdown',
            'judgeBreakdown',
            'rawQa',
            'prelimWeight',
            'qaWeight',
            'candidatesJson',
            'judgesJson',
            'rawQaMap',
            'prelimTotals'
        ));
    }
}
