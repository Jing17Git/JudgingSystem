<?php

namespace App\Http\Controllers\Admin;

use App\Events\ScoreSubmitted;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\ProductionScore;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductionController extends Controller
{
    /**
     * Display the production scoring table.
     *
     * Only the top 5 candidates per gender who have been fully scored
     * by ALL active judges in ALL four pre-judging categories are shown.
     */
    public function index(Request $request)
    {
        // Get all active judges
        $judges = User::where('role', 'judge')
            ->where('is_active', true)
            ->orderByRaw('judge_number IS NULL, judge_number ASC')
            ->orderBy('name')
            ->get();
        $judgeCount = $judges->count();

        // Get all candidates
        $candidates = Candidate::orderBy('candidate_number')->get();

        // Load all production scores
        $prodScores = ProductionScore::all()->keyBy(fn ($s) => $s->candidate_id.'_'.$s->judge_id);

        // Production-score averages (per-candidate) used for the view's Total column
        $candidateTotals = [];
        foreach ($candidates as $candidate) {
            $total = 0;
            foreach ($judges as $judge) {
                $key = $candidate->id.'_'.$judge->id;
                $total += isset($prodScores[$key]) ? (float) $prodScores[$key]->score : 0;
            }
            $candidateTotals[$candidate->id] = $judgeCount > 0 ? $total / $judgeCount : 0;
        }

        // Build per-group ranks based on production average
        $ranks = [];
        foreach (['Male', 'Female', 'Unset'] as $g) {
            $gSet = $candidates->filter(fn ($c) => $g === 'Unset'
                ? ! in_array($c->gender, ['Male', 'Female'])
                : $c->gender === $g);
            if ($gSet->isEmpty()) {
                continue;
            }
            $gTotals = [];
            foreach ($gSet as $c) {
                $gTotals[$c->id] = $candidateTotals[$c->id] ?? 0;
            }
            arsort($gTotals);
            $r = 1;
            $prev = null;
            $same = 1;
            foreach ($gTotals as $cid => $tot) {
                if ($tot <= 0) {
                    $ranks[$cid] = null;

                    continue;
                }
                if ($prev !== null && $tot === $prev) {
                    $ranks[$cid] = $r - $same;
                    $same++;
                } else {
                    $ranks[$cid] = $r;
                    $same = 1;
                }
                $prev = $tot;
                $r++;
            }
        }

        $candidatesJson = $candidates->map(function ($c) {
            return [
                'id' => $c->id,
                'candidate_number' => (int) $c->candidate_number,
                'display_name' => $c->display_name,
                'gender' => $c->gender,
            ];
        })->values();

        $judgesJson = $judges->map(function ($j) {
            return [
                'id' => $j->id,
                'name' => $j->name,
                'judge_number' => $j->judge_number ?? $j->id,
            ];
        })->values();

        $scores = $prodScores;

        $scoresJson = $scores->map(function ($s) {
            return (float) $s->score;
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'scores' => $scoresJson,
                'candidateTotals' => $candidateTotals,
            ]);
        }

        return view('admin.production.index', compact(
            'judges',
            'candidates',
            'scores',
            'candidateTotals',
            'ranks',
            'candidatesJson',
            'judgesJson',
            'scoresJson'
        ));
    }

    /**
     * Save a score for a candidate-judge pair (AJAX).
     */
    public function saveScore(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'judge_id' => 'required|exists:users,id',
            'score' => 'required|numeric|min:1|max:10',
        ]);

        $productionScore = ProductionScore::updateOrCreate(
            [
                'candidate_id' => $validated['candidate_id'],
                'judge_id' => $validated['judge_id'],
            ],
            ['score' => $validated['score']]
        );

        // Broadcast real-time event
        try {
            broadcast(new ScoreSubmitted(
                'production',
                (int) $validated['candidate_id'],
                (int) $validated['judge_id'],
                (float) $productionScore->score,
                'saved'
            ));
        } catch (\Throwable $e) {
            Log::warning('Broadcast failed in ProductionController: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'score' => $productionScore->score,
        ]);
    }
}
