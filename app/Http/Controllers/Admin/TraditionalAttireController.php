<?php

namespace App\Http\Controllers\Admin;

use App\Events\ScoreSubmitted;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\TraditionalAttireScore;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TraditionalAttireController extends Controller
{
    /**
     * Display the traditional attire scoring table.
     */
    public function index(Request $request)
    {
        $judges = User::where('role', 'judge')
            ->where('is_active', true)
            ->orderByRaw('judge_number IS NULL, judge_number ASC')
            ->orderBy('name')
            ->get();

        $candidates = Candidate::orderBy('candidate_number')->get();

        $scores = TraditionalAttireScore::all()->keyBy(function ($s) {
            return $s->candidate_id.'_'.$s->judge_id;
        });

        $judgeCount = $judges->count();
        $candidateTotals = [];
        foreach ($candidates as $candidate) {
            $total = 0;
            foreach ($judges as $judge) {
                $key = $candidate->id.'_'.$judge->id;
                $total += isset($scores[$key]) ? (float) $scores[$key]->score : 0;
            }
            $candidateTotals[$candidate->id] = $judgeCount > 0 ? $total / $judgeCount : 0;
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

        return view('admin.traditional_attire.index', compact(
            'judges',
            'candidates',
            'scores',
            'candidateTotals',
            'candidatesJson',
            'judgesJson',
            'scoresJson'
        ));
    }

    /**
     * Save score (AJAX).
     */
    public function saveScore(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'judge_id' => 'required|exists:users,id',
            'score' => 'required|numeric|min:1|max:10',
        ]);

        $scoreRecord = TraditionalAttireScore::updateOrCreate(
            [
                'candidate_id' => $validated['candidate_id'],
                'judge_id' => $validated['judge_id'],
            ],
            ['score' => $validated['score']]
        );

        // Broadcast real-time event
        try {
            broadcast(new ScoreSubmitted(
                'traditional-attire',
                (int) $validated['candidate_id'],
                (int) $validated['judge_id'],
                (float) $scoreRecord->score,
                'saved'
            ));
        } catch (\Throwable $e) {
            Log::warning('Broadcast failed in TraditionalAttireController: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'score' => $scoreRecord->score,
        ]);
    }
}
