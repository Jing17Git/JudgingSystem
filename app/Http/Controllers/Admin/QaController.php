<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\ProductionScore;
use App\Models\FitnessScore;
use App\Models\TraditionalAttireScore;
use App\Models\IndigenousAttireScore;
use App\Models\QaScore;
use App\Models\QaQuestion;
use App\Models\User;
use Illuminate\Http\Request;

class QaController extends Controller
{
    /**
     * Display Q&A Final Judging Table (Top 3 Candidates per division) and Questions CRUD.
     */
    public function index()
    {
        // Get active judges
        $judges = User::where('role', 'judge')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $judgeCount = $judges->count();

        $candidates = Candidate::all();

        // Calculate Pre-Judging weighted totals based on criteria percentages to determine Top 5 finalists
        $weights = \App\Models\CriteriaSetting::getPercentageMap();
        $prodWeight  = (float) ($weights['production'] ?? 25.0);
        $fitWeight   = (float) ($weights['fitness'] ?? 25.0);
        $tradWeight  = (float) ($weights['traditional_attire'] ?? 25.0);
        $indigWeight = (float) ($weights['indigenous_attire'] ?? 25.0);

        $prodScores  = ProductionScore::all()->groupBy('candidate_id');
        $fitScores   = FitnessScore::all()->groupBy('candidate_id');
        $tradScores  = TraditionalAttireScore::all()->groupBy('candidate_id');
        $indigScores = IndigenousAttireScore::all()->groupBy('candidate_id');

        $preJudgingTotals = [];
        foreach ($candidates as $c) {
            $pSum = isset($prodScores[$c->id])  ? (float) $prodScores[$c->id]->sum('score')  : 0;
            $fSum = isset($fitScores[$c->id])   ? (float) $fitScores[$c->id]->sum('score')   : 0;
            $tSum = isset($tradScores[$c->id])  ? (float) $tradScores[$c->id]->sum('score')  : 0;
            $iSum = isset($indigScores[$c->id]) ? (float) $indigScores[$c->id]->sum('score') : 0;

            $pAvg = $judgeCount > 0 ? ($pSum / $judgeCount) : 0;
            $fAvg = $judgeCount > 0 ? ($fSum / $judgeCount) : 0;
            $tAvg = $judgeCount > 0 ? ($tSum / $judgeCount) : 0;
            $iAvg = $judgeCount > 0 ? ($iSum / $judgeCount) : 0;

            $preJudgingTotals[$c->id] = ($pAvg * $prodWeight / 100.0)
                                      + ($fAvg * $fitWeight / 100.0)
                                      + ($tAvg * $tradWeight / 100.0)
                                      + ($iAvg * $indigWeight / 100.0);
        }

        // Pull Top 5 Male Candidates based on combined pre-judging scores
        $top5Male = $candidates->filter(fn($c) => $c->gender === 'Male')
            ->sort(function($a, $b) use ($preJudgingTotals) {
                $totA = $preJudgingTotals[$a->id] ?? 0;
                $totB = $preJudgingTotals[$b->id] ?? 0;
                if ($totA == $totB) return $a->candidate_number <=> $b->candidate_number;
                return $totB <=> $totA;
            })
            ->take(5)
            ->values();

        // Pull Top 5 Female Candidates based on combined pre-judging scores
        $top5Female = $candidates->filter(fn($c) => $c->gender === 'Female')
            ->sort(function($a, $b) use ($preJudgingTotals) {
                $totA = $preJudgingTotals[$a->id] ?? 0;
                $totB = $preJudgingTotals[$b->id] ?? 0;
                if ($totA == $totB) return $a->candidate_number <=> $b->candidate_number;
                return $totB <=> $totA;
            })
            ->take(5)
            ->values();

        // Combine Top 5 finalists
        $finalists = $top5Male->concat($top5Female);

        // Load Q&A scores
        $scores = QaScore::all()->keyBy(function ($s) {
            return $s->candidate_id . '_' . $s->judge_id;
        });

        // Build candidate totals for Q&A (average = sum ÷ judge count)
        $candidateTotals = [];
        foreach ($finalists as $candidate) {
            $total = 0;
            foreach ($judges as $judge) {
                $key = $candidate->id . '_' . $judge->id;
                $total += isset($scores[$key]) ? (float) $scores[$key]->score : 0;
            }
            $candidateTotals[$candidate->id] = $judgeCount > 0 ? ($total / $judgeCount) : 0;
        }

        // Questions list
        $questions = QaQuestion::orderBy('created_at', 'desc')->get();

        return view('admin.qa.index', [
            'judges'          => $judges,
            'candidates'      => $candidates,
            'top3Male'        => $top5Male, // backward compatibility variable name
            'top3Female'      => $top5Female,
            'top5Male'        => $top5Male,
            'top5Female'      => $top5Female,
            'finalists'       => $finalists,
            'scores'          => $scores,
            'candidateTotals' => $candidateTotals,
            'questions'       => $questions,
        ]);
    }

    /**
     * Save/Update Q&A score (AJAX / Form).
     */
    public function saveScore(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'judge_id'     => 'required|exists:users,id',
            'score'        => 'required|numeric|min:1|max:10',
        ]);

        $qaScore = QaScore::updateOrCreate(
            [
                'candidate_id' => $validated['candidate_id'],
                'judge_id'     => $validated['judge_id'],
            ],
            ['score' => $validated['score']]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'score'   => number_format((float) $qaScore->score, 2),
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
            'gender'   => 'required|in:Male,Female,All',
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
            'gender'   => 'required|in:Male,Female,All',
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
