<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\TraditionalAttireScore;
use App\Models\User;
use Illuminate\Http\Request;

class TraditionalAttireController extends Controller
{
    /**
     * Display the traditional attire scoring table.
     */
    public function index()
    {
        $judges = User::where('role', 'judge')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $candidates = Candidate::orderBy('candidate_number')->get();

        $scores = TraditionalAttireScore::all()->keyBy(function ($s) {
            return $s->candidate_id . '_' . $s->judge_id;
        });

        $candidateTotals = [];
        foreach ($candidates as $candidate) {
            $total = 0;
            foreach ($judges as $judge) {
                $key = $candidate->id . '_' . $judge->id;
                $total += isset($scores[$key]) ? (float) $scores[$key]->score : 0;
            }
            $candidateTotals[$candidate->id] = $total;
        }

        return view('admin.traditional_attire.index', compact('judges', 'candidates', 'scores', 'candidateTotals'));
    }

    /**
     * Save score (AJAX).
     */
    public function saveScore(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'judge_id'     => 'required|exists:users,id',
            'score'        => 'required|numeric|min:1|max:10',
        ]);

        $scoreRecord = TraditionalAttireScore::updateOrCreate(
            [
                'candidate_id' => $validated['candidate_id'],
                'judge_id'     => $validated['judge_id'],
            ],
            ['score' => $validated['score']]
        );

        return response()->json([
            'success' => true,
            'score'   => $scoreRecord->score,
        ]);
    }
}
