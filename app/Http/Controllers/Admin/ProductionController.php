<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CriteriaSetting;
use App\Models\ProductionScore;
use App\Models\FitnessScore;
use App\Models\TraditionalAttireScore;
use App\Models\IndigenousAttireScore;
use App\Models\User;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    /**
     * Display the production scoring table.
     *
     * Only the top 5 candidates per gender who have been fully scored
     * by ALL active judges in ALL four pre-judging categories are shown.
     */
    public function index()
    {
        // Get all active judges
        $judges = User::where('role', 'judge')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $judgeCount = $judges->count();
        $judgeIds   = $judges->pluck('id')->toArray();

        // Get all candidates
        $allCandidates = Candidate::orderBy('candidate_number')->get();

        // Load all scores for every pre-judging category
        $prodScores  = ProductionScore::all()->keyBy(fn($s) => $s->candidate_id . '_' . $s->judge_id);
        $fitScores   = FitnessScore::all()->keyBy(fn($s) => $s->candidate_id . '_' . $s->judge_id);
        $tradScores  = TraditionalAttireScore::all()->keyBy(fn($s) => $s->candidate_id . '_' . $s->judge_id);
        $indigScores = IndigenousAttireScore::all()->keyBy(fn($s) => $s->candidate_id . '_' . $s->judge_id);

        // Determine which candidates have been fully scored by ALL judges in ALL 4 categories
        $fullyScored = $allCandidates->filter(function ($c) use ($judgeIds, $prodScores, $fitScores, $tradScores, $indigScores) {
            foreach ($judgeIds as $jid) {
                $key = $c->id . '_' . $jid;
                if (
                    !isset($prodScores[$key])  ||
                    !isset($fitScores[$key])   ||
                    !isset($tradScores[$key])  ||
                    !isset($indigScores[$key])
                ) {
                    return false;
                }
            }
            return true;
        });

        // Get criteria weights for combined pre-judging score
        $weights     = CriteriaSetting::getPercentageMap();
        $prodWeight  = (float) ($weights['production']         ?? 25.0);
        $fitWeight   = (float) ($weights['fitness']            ?? 25.0);
        $tradWeight  = (float) ($weights['traditional_attire'] ?? 25.0);
        $indigWeight = (float) ($weights['indigenous_attire']  ?? 25.0);

        // Compute combined pre-judging weighted totals for fully-scored candidates
        $preJudgingTotals = [];
        foreach ($fullyScored as $c) {
            $pSum = 0; $fSum = 0; $tSum = 0; $iSum = 0;
            foreach ($judgeIds as $jid) {
                $key   = $c->id . '_' . $jid;
                $pSum += (float) $prodScores[$key]->score;
                $fSum += (float) $fitScores[$key]->score;
                $tSum += (float) $tradScores[$key]->score;
                $iSum += (float) $indigScores[$key]->score;
            }
            $pAvg = $judgeCount > 0 ? $pSum / $judgeCount : 0;
            $fAvg = $judgeCount > 0 ? $fSum / $judgeCount : 0;
            $tAvg = $judgeCount > 0 ? $tSum / $judgeCount : 0;
            $iAvg = $judgeCount > 0 ? $iSum / $judgeCount : 0;

            $preJudgingTotals[$c->id] = ($pAvg * $prodWeight  / 100.0)
                                      + ($fAvg * $fitWeight   / 100.0)
                                      + ($tAvg * $tradWeight  / 100.0)
                                      + ($iAvg * $indigWeight / 100.0);
        }

        // Pick top 5 per gender from fully-scored pool
        $sortFn = function ($a, $b) use ($preJudgingTotals) {
            $totA = $preJudgingTotals[$a->id] ?? 0;
            $totB = $preJudgingTotals[$b->id] ?? 0;
            if ($totA == $totB) return $a->candidate_number <=> $b->candidate_number;
            return $totB <=> $totA;
        };

        $top5Male   = $fullyScored->filter(fn($c) => $c->gender === 'Male')
                                  ->sort($sortFn)->take(5)->values();
        $top5Female = $fullyScored->filter(fn($c) => $c->gender === 'Female')
                                  ->sort($sortFn)->take(5)->values();
        $top5Other  = $fullyScored->filter(fn($c) => !in_array($c->gender, ['Male', 'Female']))
                                  ->sort($sortFn)->take(5)->values();

        // Final candidates collection used by the view (top 5 per gender)
        $candidates = $top5Male->concat($top5Female)->concat($top5Other);

        // Production-score averages (per-candidate) used for the view's Total column
        $candidateTotals = [];
        foreach ($candidates as $candidate) {
            $total = 0;
            foreach ($judges as $judge) {
                $key    = $candidate->id . '_' . $judge->id;
                $total += isset($prodScores[$key]) ? (float) $prodScores[$key]->score : 0;
            }
            $candidateTotals[$candidate->id] = $judgeCount > 0 ? $total / $judgeCount : 0;
        }

        // Build per-group ranks based on production average
        $ranks = [];
        foreach (['Male', 'Female', 'Other'] as $g) {
            $gSet = $candidates->filter(fn($c) => $g === 'Other'
                ? !in_array($c->gender, ['Male', 'Female'])
                : $c->gender === $g);
            if ($gSet->isEmpty()) continue;
            $gTotals = [];
            foreach ($gSet as $c) $gTotals[$c->id] = $candidateTotals[$c->id] ?? 0;
            arsort($gTotals);
            $r = 1; $prev = null; $same = 1;
            foreach ($gTotals as $cid => $tot) {
                if ($tot <= 0) { $ranks[$cid] = null; continue; }
                if ($prev !== null && $tot === $prev) { $ranks[$cid] = $r - $same; $same++; }
                else { $ranks[$cid] = $r; $same = 1; }
                $prev = $tot; $r++;
            }
        }

        // Pass the production scores (keyed) to the view as $scores
        $scores = $prodScores;

        return view('admin.production.index', compact('judges', 'candidates', 'scores', 'candidateTotals', 'ranks'));
    }

    /**
     * Save a score for a candidate-judge pair (AJAX).
     */
    public function saveScore(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'judge_id'     => 'required|exists:users,id',
            'score'        => 'required|numeric|min:1|max:10',
        ]);

        $productionScore = ProductionScore::updateOrCreate(
            [
                'candidate_id' => $validated['candidate_id'],
                'judge_id'     => $validated['judge_id'],
            ],
            ['score' => $validated['score']]
        );

        return response()->json([
            'success' => true,
            'score'   => $productionScore->score,
        ]);
    }
}
