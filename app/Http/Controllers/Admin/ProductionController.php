<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\ProductionScore;
use App\Models\User;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    /**
     * Display the production scoring table.
     */
    public function index()
    {
        // Get all active judges
        $judges = User::where('role', 'judge')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get all candidates ordered by candidate number
        $candidates = Candidate::orderBy('candidate_number')->get();

        // Load all production scores indexed by [candidate_id][judge_id]
        $scores = ProductionScore::all()->keyBy(function ($s) {
            return $s->candidate_id . '_' . $s->judge_id;
        });

        // Build ranked data: candidate totals
        $candidateTotals = [];
        foreach ($candidates as $candidate) {
            $total = 0;
            foreach ($judges as $judge) {
                $key = $candidate->id . '_' . $judge->id;
                $total += isset($scores[$key]) ? (float) $scores[$key]->score : 0;
            }
            $candidateTotals[$candidate->id] = $total;
        }

        // Rank candidates (highest total = rank 1)
        arsort($candidateTotals);
        $ranks = [];
        $rank = 1;
        $prev = null;
        $sameCount = 1;
        foreach ($candidateTotals as $cid => $total) {
            if ($prev !== null && $total === $prev) {
                $ranks[$cid] = $rank - $sameCount;
                $sameCount++;
            } else {
                $ranks[$cid] = $rank;
                $sameCount = 1;
            }
            $prev = $total;
            $rank++;
        }

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
