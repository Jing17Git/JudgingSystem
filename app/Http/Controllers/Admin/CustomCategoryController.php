<?php

namespace App\Http\Controllers\Admin;

use App\Events\ScoreSubmitted;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CriteriaSetting;
use App\Models\CustomCategoryScore;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomCategoryController extends Controller
{
    /**
     * Display the scoring table for a dynamic (custom) category.
     */
    public function index(Request $request, string $key)
    {
        // Validate the category key exists in CriteriaSetting as preliminary
        $categorySetting = CriteriaSetting::where('key', $key)
            ->where('stage', 'preliminary')
            ->firstOrFail();

        $judges = User::where('role', 'judge')
            ->where('is_active', true)
            ->orderByRaw('judge_number IS NULL, judge_number ASC')
            ->orderBy('name')
            ->get();
        $judgeCount = $judges->count();

        $candidates = Candidate::orderBy('candidate_number')->get();

        // Load all scores for this category
        $scores = CustomCategoryScore::forCategory($key);

        // Compute per-candidate averages across all judges
        $candidateTotals = [];
        foreach ($candidates as $candidate) {
            $total = 0;
            foreach ($judges as $judge) {
                $k = $candidate->id . '_' . $judge->id;
                $total += isset($scores[$k]) ? (float) $scores[$k]->score : 0;
            }
            $candidateTotals[$candidate->id] = $judgeCount > 0 ? $total / $judgeCount : 0;
        }

        // Build per-group ranks
        $groupRanks = [];
        foreach (['Male', 'Female', 'Unset'] as $g) {
            $gSet = $candidates->filter(fn($c) => $g === 'Unset'
                ? !in_array($c->gender, ['Male', 'Female'])
                : $c->gender === $g);
            if ($gSet->isEmpty()) continue;
            $gTotals = [];
            foreach ($gSet as $c) {
                $gTotals[$c->id] = $candidateTotals[$c->id] ?? 0;
            }
            arsort($gTotals);
            $r = 0; $prev = null;
            foreach ($gTotals as $cid => $tot) {
                if ($tot <= 0) { $groupRanks[$cid] = null; continue; }
                if ($prev === null || abs((float) $tot - (float) $prev) > 0.0001) { $r++; }
                $groupRanks[$cid] = $r;
                $prev = $tot;
            }
        }

        $candidatesJson = $candidates->map(fn($c) => [
            'id'               => $c->id,
            'candidate_number' => (int) $c->candidate_number,
            'display_name'     => $c->display_name,
            'gender'           => $c->gender,
        ])->values();

        $judgesJson = $judges->map(fn($j) => [
            'id'           => $j->id,
            'name'         => $j->name,
            'judge_number' => $j->judge_number ?? $j->id,
        ])->values();

        $scoresJson = $scores->map(fn($s) => (float) $s->score);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'        => true,
                'scores'         => $scoresJson,
                'candidateTotals'=> $candidateTotals,
            ]);
        }

        return view('admin.category.index', compact(
            'categorySetting',
            'judges',
            'candidates',
            'scores',
            'candidateTotals',
            'groupRanks',
            'candidatesJson',
            'judgesJson',
            'scoresJson',
            'key'
        ));
    }

    /**
     * Save a score (AJAX) for a dynamic category.
     */
    public function saveScore(Request $request)
    {
        $validated = $request->validate([
            'category_key' => 'required|string|max:100',
            'candidate_id' => 'required|exists:candidates,id',
            'judge_id'     => 'required|exists:users,id',
            'score'        => 'required|numeric|min:1|max:10',
        ]);

        // Verify category exists
        $setting = CriteriaSetting::where('key', $validated['category_key'])
            ->where('stage', 'preliminary')
            ->firstOrFail();

        $scoreObj = CustomCategoryScore::updateOrCreate(
            [
                'candidate_id' => $validated['candidate_id'],
                'judge_id'     => $validated['judge_id'],
                'category_key' => $validated['category_key'],
            ],
            ['score' => $validated['score']]
        );

        // Broadcast real-time event
        try {
            broadcast(new ScoreSubmitted(
                'custom:' . $validated['category_key'],
                (int) $validated['candidate_id'],
                (int) $validated['judge_id'],
                (float) $scoreObj->score,
                'saved'
            ));
        } catch (\Throwable $e) {
            Log::warning('Broadcast failed in CustomCategoryController: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'score'   => $scoreObj->score,
        ]);
    }

    /**
     * Reset a score (AJAX) for a dynamic category.
     */
    public function resetScore(Request $request)
    {
        $validated = $request->validate([
            'category_key' => 'required|string|max:100',
            'candidate_id' => 'required|exists:candidates,id',
            'judge_id'     => 'required|exists:users,id',
        ]);

        CustomCategoryScore::where('candidate_id', $validated['candidate_id'])
            ->where('judge_id', $validated['judge_id'])
            ->where('category_key', $validated['category_key'])
            ->delete();

        try {
            broadcast(new ScoreSubmitted(
                'custom:' . $validated['category_key'],
                (int) $validated['candidate_id'],
                (int) $validated['judge_id'],
                null,
                'reset'
            ));
        } catch (\Throwable $e) {
            Log::warning('Broadcast failed in CustomCategoryController reset: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }
}
