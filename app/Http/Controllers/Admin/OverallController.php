<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CriteriaSetting;
use App\Models\ProductionScore;
use App\Models\FitnessScore;
use App\Models\TraditionalAttireScore;
use App\Models\IndigenousAttireScore;
use App\Models\QaScore;
use App\Models\User;
use Illuminate\Http\Request;

class OverallController extends Controller
{
    /**
     * Display overall tabulation tables with criteria-weighted results and Judge Breakdown details.
     * Category Result = (Sum of Judge Scores ÷ Total Judges) × (Percentage Weight ÷ 100).
     * Grand Total = Sum of all weighted category results.
     */
    public function index()
    {
        $candidates = Candidate::orderBy('candidate_number')->get();
        $judges = User::where('role', 'judge')->where('is_active', true)->orderBy('name')->get();
        $judgeCount = $judges->count();

        // Fetch criteria percentage weights from settings
        $weights = CriteriaSetting::getPercentageMap();
        $prodWeight  = (float) ($weights['production'] ?? 25.0);
        $fitWeight   = (float) ($weights['fitness'] ?? 25.0);
        $tradWeight  = (float) ($weights['traditional_attire'] ?? 25.0);
        $indigWeight = (float) ($weights['indigenous_attire'] ?? 25.0);

        // Fetch scores per candidate grouped by candidate_id
        $prodScores   = ProductionScore::all()->groupBy('candidate_id');
        $fitScores    = FitnessScore::all()->groupBy('candidate_id');
        $tradScores   = TraditionalAttireScore::all()->groupBy('candidate_id');
        $indigScores  = IndigenousAttireScore::all()->groupBy('candidate_id');
        $qaScores     = QaScore::all()->groupBy('candidate_id');

        // All raw scores keyBy [candidate_id_judge_id]
        $rawProd  = ProductionScore::all()->keyBy(fn($s) => $s->candidate_id . '_' . $s->judge_id);
        $rawFit   = FitnessScore::all()->keyBy(fn($s) => $s->candidate_id . '_' . $s->judge_id);
        $rawTrad  = TraditionalAttireScore::all()->keyBy(fn($s) => $s->candidate_id . '_' . $s->judge_id);
        $rawIndig = IndigenousAttireScore::all()->keyBy(fn($s) => $s->candidate_id . '_' . $s->judge_id);
        $rawQa    = QaScore::all()->keyBy(fn($s) => $s->candidate_id . '_' . $s->judge_id);

        $breakdown = [];
        $judgeBreakdown = [];

        foreach ($candidates as $c) {
            $pSum = isset($prodScores[$c->id])  ? (float) $prodScores[$c->id]->sum('score')  : 0;
            $fSum = isset($fitScores[$c->id])   ? (float) $fitScores[$c->id]->sum('score')   : 0;
            $tSum = isset($tradScores[$c->id])  ? (float) $tradScores[$c->id]->sum('score')  : 0;
            $iSum = isset($indigScores[$c->id]) ? (float) $indigScores[$c->id]->sum('score') : 0;

            // Average score per category (sum ÷ total judges)
            $pAvg = $judgeCount > 0 ? ($pSum / $judgeCount) : 0;
            $fAvg = $judgeCount > 0 ? ($fSum / $judgeCount) : 0;
            $tAvg = $judgeCount > 0 ? ($tSum / $judgeCount) : 0;
            $iAvg = $judgeCount > 0 ? ($iSum / $judgeCount) : 0;

            // Weighted score per category (Average × Percentage ÷ 100)
            $pWeighted = $pAvg * ($prodWeight / 100.0);
            $fWeighted = $fAvg * ($fitWeight / 100.0);
            $tWeighted = $tAvg * ($tradWeight / 100.0);
            $iWeighted = $iAvg * ($indigWeight / 100.0);

            $grandTotal = $pWeighted + $fWeighted + $tWeighted + $iWeighted;

            $breakdown[$c->id] = [
                'production_avg'      => $pAvg,
                'fitness_avg'         => $fAvg,
                'traditional_avg'     => $tAvg,
                'indigenous_avg'      => $iAvg,
                'production'          => $pWeighted,
                'fitness'             => $fWeighted,
                'traditional'         => $tWeighted,
                'indigenous'          => $iWeighted,
                'total'               => $grandTotal,
            ];

            // Build detailed per-judge breakdown for this candidate
            $cJudgeScores = [];
            foreach ($judges as $j) {
                $key = $c->id . '_' . $j->id;

                $pScore = isset($rawProd[$key])  ? (float) $rawProd[$key]->score  : null;
                $fScore = isset($rawFit[$key])   ? (float) $rawFit[$key]->score   : null;
                $tScore = isset($rawTrad[$key])  ? (float) $rawTrad[$key]->score  : null;
                $iScore = isset($rawIndig[$key]) ? (float) $rawIndig[$key]->score : null;
                $qScore = isset($rawQa[$key])    ? (float) $rawQa[$key]->score    : null;

                $sum = ($pScore ?? 0) + ($fScore ?? 0) + ($tScore ?? 0) + ($iScore ?? 0) + ($qScore ?? 0);

                $cJudgeScores[] = [
                    'judge_id'    => $j->id,
                    'judge_name'  => $j->name,
                    'production'  => $pScore,
                    'fitness'     => $fScore,
                    'traditional' => $tScore,
                    'indigenous'  => $iScore,
                    'qa'          => $qScore,
                    'total'       => $sum,
                ];
            }

            $judgeBreakdown[$c->id] = $cJudgeScores;
        }

        return view('admin.overall.index', compact('candidates', 'breakdown', 'judges', 'judgeBreakdown', 'weights'));
    }

    /**
     * Display a dedicated full page for an individual candidate's judge votes breakdown.
     */
    public function candidateVotes(Request $request, Candidate $candidate)
    {
        $judges = User::where('role', 'judge')->where('is_active', true)->orderBy('name')->get();
        $judgeCount = $judges->count();

        // Fetch criteria percentage weights from settings
        $weights = CriteriaSetting::getPercentageMap();
        $prodWeight  = (float) ($weights['production'] ?? 25.0);
        $fitWeight   = (float) ($weights['fitness'] ?? 25.0);
        $tradWeight  = (float) ($weights['traditional_attire'] ?? 25.0);
        $indigWeight = (float) ($weights['indigenous_attire'] ?? 25.0);

        // Final weights
        $finalWeights = CriteriaSetting::getFinalMap();
        $prelimWeight = (float) ($finalWeights['preliminary_total'] ?? 50.0);
        $qaWeight     = (float) ($finalWeights['qa'] ?? 50.0);

        // Raw scores for this candidate
        $prodScores   = ProductionScore::where('candidate_id', $candidate->id)->get()->keyBy('judge_id');
        $fitScores    = FitnessScore::where('candidate_id', $candidate->id)->get()->keyBy('judge_id');
        $tradScores   = TraditionalAttireScore::where('candidate_id', $candidate->id)->get()->keyBy('judge_id');
        $indigScores  = IndigenousAttireScore::where('candidate_id', $candidate->id)->get()->keyBy('judge_id');
        $qaScores     = QaScore::where('candidate_id', $candidate->id)->get()->keyBy('judge_id');

        $judgeScores = [];
        $prodSum = 0; $prodCount = 0;
        $fitSum = 0;  $fitCount = 0;
        $tradSum = 0; $tradCount = 0;
        $indigSum = 0; $indigCount = 0;
        $qaSum = 0;   $qaCount = 0;

        foreach ($judges as $j) {
            $p = isset($prodScores[$j->id])  ? (float) $prodScores[$j->id]->score  : null;
            $f = isset($fitScores[$j->id])   ? (float) $fitScores[$j->id]->score   : null;
            $t = isset($tradScores[$j->id])  ? (float) $tradScores[$j->id]->score  : null;
            $i = isset($indigScores[$j->id]) ? (float) $indigScores[$j->id]->score : null;
            $q = isset($qaScores[$j->id])    ? (float) $qaScores[$j->id]->score    : null;

            if ($p !== null) { $prodSum += $p; $prodCount++; }
            if ($f !== null) { $fitSum += $f; $fitCount++; }
            if ($t !== null) { $tradSum += $t; $tradCount++; }
            if ($i !== null) { $indigSum += $i; $indigCount++; }
            if ($q !== null) { $qaSum += $q; $qaCount++; }

            $totalPrelim = ($p ?? 0) + ($f ?? 0) + ($t ?? 0) + ($i ?? 0);
            $totalAll    = $totalPrelim + ($q ?? 0);

            $judgeScores[] = [
                'judge'       => $j,
                'production'  => $p,
                'fitness'     => $f,
                'traditional' => $t,
                'indigenous'  => $i,
                'qa'          => $q,
                'total_prelim'=> $totalPrelim,
                'total'       => $totalAll,
            ];
        }

        $prodAvg  = $prodCount > 0  ? ($prodSum / $prodCount) : 0;
        $fitAvg   = $fitCount > 0   ? ($fitSum / $fitCount) : 0;
        $tradAvg  = $tradCount > 0  ? ($tradSum / $tradCount) : 0;
        $indigAvg = $indigCount > 0 ? ($indigSum / $indigCount) : 0;
        $qaAvg    = $qaCount > 0    ? ($qaSum / $qaCount) : 0;

        $prodWeighted  = $prodAvg * ($prodWeight / 100.0);
        $fitWeighted   = $fitAvg * ($fitWeight / 100.0);
        $tradWeighted  = $tradAvg * ($tradWeight / 100.0);
        $indigWeighted = $indigAvg * ($indigWeight / 100.0);

        $prelimGrandTotal = $prodWeighted + $fitWeighted + $tradWeighted + $indigWeighted;
        $finalGrandTotal  = ($prelimGrandTotal * ($prelimWeight / 100.0)) + ($qaAvg * ($qaWeight / 100.0));

        $summary = [
            'prod_avg'       => $prodAvg,
            'prod_weighted'  => $prodWeighted,
            'fit_avg'        => $fitAvg,
            'fit_weighted'   => $fitWeighted,
            'trad_avg'       => $tradAvg,
            'trad_weighted'  => $tradWeighted,
            'indig_avg'      => $indigAvg,
            'indig_weighted' => $indigWeighted,
            'prelim_total'   => $prelimGrandTotal,
            'qa_avg'         => $qaAvg,
            'final_total'    => $finalGrandTotal,
            'weights'        => $weights,
            'final_weights'  => $finalWeights,
        ];

        $from = $request->query('from', 'overall');

        return view('admin.overall.candidate_votes', compact('candidate', 'judges', 'judgeScores', 'summary', 'from'));
    }
}

