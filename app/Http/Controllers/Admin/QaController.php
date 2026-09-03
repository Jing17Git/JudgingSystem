<?php

namespace App\Http\Controllers\Admin;

use App\Events\ScoreSubmitted;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CriteriaSetting;
use App\Models\FitnessScore;
use App\Models\IndigenousAttireScore;
use App\Models\ProductionScore;
use App\Models\QaQuestion;
use App\Models\QaScore;
use App\Models\TraditionalAttireScore;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QaController extends Controller
{
    /**
     * Display Q&A Final Judging Table (Top 5 Candidates per division) and Questions CRUD.
     */
    public function index(Request $request)
    {
        // Get active judges
        $judges = User::where('role', 'judge')
            ->where('is_active', true)
            ->orderByRaw('judge_number IS NULL, judge_number ASC')
            ->orderBy('name')
            ->get();
        $judgeCount = $judges->count();
        $judgeIds = $judges->pluck('id')->toArray();

        $allCandidates = Candidate::all();

        // Load all pre-judging scores keyed by candidate_id_judge_id
        $prodScoresKeyed = ProductionScore::all()->keyBy(fn ($s) => $s->candidate_id.'_'.$s->judge_id);
        $fitScoresKeyed = FitnessScore::all()->keyBy(fn ($s) => $s->candidate_id.'_'.$s->judge_id);
        $tradScoresKeyed = TraditionalAttireScore::all()->keyBy(fn ($s) => $s->candidate_id.'_'.$s->judge_id);
        $indigScoresKeyed = IndigenousAttireScore::all()->keyBy(fn ($s) => $s->candidate_id.'_'.$s->judge_id);

        // Only keep candidates fully scored by ALL judges in ALL 4 categories
        $fullyScored = $allCandidates->filter(function ($c) use ($judgeIds, $prodScoresKeyed, $fitScoresKeyed, $tradScoresKeyed, $indigScoresKeyed) {
            if (empty($judgeIds)) {
                return false;
            }
            foreach ($judgeIds as $jid) {
                $key = $c->id.'_'.$jid;
                if (
                    ! isset($prodScoresKeyed[$key]) ||
                    ! isset($fitScoresKeyed[$key]) ||
                    ! isset($tradScoresKeyed[$key]) ||
                    ! isset($indigScoresKeyed[$key])
                ) {
                    return false;
                }
            }

            return true;
        });

        // Calculate Pre-Judging weighted totals based on criteria percentages
        $weights = CriteriaSetting::getPercentageMap();
        $prodWeight = (float) ($weights['production'] ?? 25.0);
        $fitWeight = (float) ($weights['fitness'] ?? 25.0);
        $tradWeight = (float) ($weights['traditional_attire'] ?? 25.0);
        $indigWeight = (float) ($weights['indigenous_attire'] ?? 25.0);

        $preJudgingTotals = [];
        foreach ($fullyScored as $c) {
            $pSum = 0;
            $fSum = 0;
            $tSum = 0;
            $iSum = 0;
            foreach ($judgeIds as $jid) {
                $key = $c->id.'_'.$jid;
                $pSum += (float) $prodScoresKeyed[$key]->score;
                $fSum += (float) $fitScoresKeyed[$key]->score;
                $tSum += (float) $tradScoresKeyed[$key]->score;
                $iSum += (float) $indigScoresKeyed[$key]->score;
            }
            $pAvg = $judgeCount > 0 ? $pSum / $judgeCount : 0;
            $fAvg = $judgeCount > 0 ? $fSum / $judgeCount : 0;
            $tAvg = $judgeCount > 0 ? $tSum / $judgeCount : 0;
            $iAvg = $judgeCount > 0 ? $iSum / $judgeCount : 0;

            $preJudgingTotals[$c->id] = ($pAvg * $prodWeight / 100.0)
                                      + ($fAvg * $fitWeight / 100.0)
                                      + ($tAvg * $tradWeight / 100.0)
                                      + ($iAvg * $indigWeight / 100.0);
        }

        $sortFn = function ($a, $b) use ($preJudgingTotals) {
            $totA = $preJudgingTotals[$a->id] ?? 0;
            $totB = $preJudgingTotals[$b->id] ?? 0;
            if ($totA == $totB) {
                return $a->candidate_number <=> $b->candidate_number;
            }

            return $totB <=> $totA;
        };

        // Pull Top 5 Male & Female Finalists — only from fully-scored candidates
        $top5Male = $fullyScored->filter(fn ($c) => $c->gender === 'Male')
            ->sort($sortFn)
            ->take(5)
            ->values();

        $top5Female = $fullyScored->filter(fn ($c) => $c->gender === 'Female')
            ->sort($sortFn)
            ->take(5)
            ->values();

        // Combine Top 5 finalists
        $finalists = $top5Male->concat($top5Female);

        // Load Q&A scores
        $scores = QaScore::all()->keyBy(function ($s) {
            return $s->candidate_id.'_'.$s->judge_id;
        });

        // Build candidate totals for Q&A (average = sum ÷ judge count)
        $candidateTotals = [];
        foreach ($finalists as $candidate) {
            $total = 0;
            foreach ($judges as $judge) {
                $key = $candidate->id.'_'.$judge->id;
                $total += isset($scores[$key]) ? (float) $scores[$key]->score : 0;
            }
            $candidateTotals[$candidate->id] = $judgeCount > 0 ? ($total / $judgeCount) : 0;
        }

        // Questions list
        $questions = QaQuestion::orderBy('created_at', 'desc')->get();

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

        return view('admin.qa.index', [
            'judges' => $judges,
            'candidates' => $allCandidates,
            'top3Male' => $top5Male, // backward compatibility variable name
            'top3Female' => $top5Female,
            'top5Male' => $top5Male,
            'top5Female' => $top5Female,
            'finalists' => $finalists,
            'scores' => $scores,
            'candidateTotals' => $candidateTotals,
            'questions' => $questions,
            'candidatesJson' => $candidatesJson,
            'judgesJson' => $judgesJson,
            'scoresJson' => $scoresJson,
        ]);
    }

    /**
     * Save/Update Q&A score (AJAX / Form).
     */
    public function saveScore(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'judge_id' => 'required|exists:users,id',
            'score' => 'required|numeric|min:1|max:10',
        ]);

        $qaScore = QaScore::updateOrCreate(
            [
                'candidate_id' => $validated['candidate_id'],
                'judge_id' => $validated['judge_id'],
            ],
            ['score' => $validated['score']]
        );

        // Broadcast real-time event
        try {
            broadcast(new ScoreSubmitted(
                'qa',
                (int) $validated['candidate_id'],
                (int) $validated['judge_id'],
                (float) $qaScore->score,
                'saved'
            ));
        } catch (\Throwable $e) {
            Log::warning('Broadcast failed in QaController: '.$e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'score' => number_format((float) $qaScore->score, 2),
            ]);
        }

        return redirect()->back()->with('success', 'Q&A Score saved successfully.');
    }

    /**
     * Store a new Q&A Question (CRUD).
     */
    public function storeQuestion(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|min:5|max:500',
            'gender' => 'required|in:Male,Female,All',
        ]);

        QaQuestion::create($validated);

        return redirect()->route('admin.qa.index')
            ->with('success', 'Q&A question added successfully.');
    }

    /**
     * Update a Q&A Question (CRUD).
     */
    public function updateQuestion(Request $request, QaQuestion $question)
    {
        $validated = $request->validate([
            'question' => 'required|string|min:5|max:500',
            'gender' => 'required|in:Male,Female,All',
        ]);

        $question->update($validated);

        return redirect()->route('admin.qa.index')
            ->with('success', 'Q&A question updated successfully.');
    }

    /**
     * Delete a Q&A Question (CRUD).
     */
    public function destroyQuestion(QaQuestion $question)
    {
        $question->delete();

        return redirect()->route('admin.qa.index')
            ->with('success', 'Q&A question deleted successfully.');
    }
}
