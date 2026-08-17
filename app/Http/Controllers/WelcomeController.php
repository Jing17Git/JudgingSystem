<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\ProductionScore;
use App\Models\FitnessScore;
use App\Models\TraditionalAttireScore;
use App\Models\IndigenousAttireScore;
use App\Models\QaScore;
use App\Models\User;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $candidates = Candidate::orderBy('candidate_number')->get();
        $judges = User::where('role', 'judge')->where('is_active', true)->get();

        // Score collections grouped by candidate_id
        $prodScores  = ProductionScore::all()->groupBy('candidate_id');
        $fitScores   = FitnessScore::all()->groupBy('candidate_id');
        $tradScores  = TraditionalAttireScore::all()->groupBy('candidate_id');
        $indigScores = IndigenousAttireScore::all()->groupBy('candidate_id');
        $qaScores    = QaScore::all()->groupBy('candidate_id');

        // Build leaderboard data per candidate
        $leaderboard = [];

        foreach ($candidates as $c) {
            $pTot = isset($prodScores[$c->id])  ? (float) $prodScores[$c->id]->sum('score')  : 0;
            $fTot = isset($fitScores[$c->id])   ? (float) $fitScores[$c->id]->sum('score')   : 0;
            $tTot = isset($tradScores[$c->id])  ? (float) $tradScores[$c->id]->sum('score')  : 0;
            $iTot = isset($indigScores[$c->id]) ? (float) $indigScores[$c->id]->sum('score') : 0;
            $qTot = isset($qaScores[$c->id])    ? (float) $qaScores[$c->id]->sum('score')    : 0;

            $grandTotal = $pTot + $fTot + $tTot + $iTot + $qTot;

            $leaderboard[] = [
                'id'          => $c->id,
                'number'      => $c->candidate_number,
                'name'        => $c->display_name,
                'gender'      => $c->gender,
                'photo_url'   => $c->photo_url,
                'production'  => $pTot,
                'fitness'     => $fTot,
                'traditional' => $tTot,
                'indigenous'  => $iTot,
                'qa'          => $qTot,
                'total'       => $grandTotal,
            ];
        }

        // Sort by total descending
        usort($leaderboard, fn($a, $b) => $b['total'] <=> $a['total']);

        // Split leaderboard by gender
        $maleLB = array_values(array_filter($leaderboard, fn($r) => strtolower($r['gender']) === 'male'));
        $femaleLB = array_values(array_filter($leaderboard, fn($r) => strtolower($r['gender']) === 'female'));

        // Stats
        $totalCandidates = $candidates->count();
        $totalJudges = $judges->count();
        $totalCategories = 5;

        return view('welcome', compact(
            'leaderboard',
            'maleLB',
            'femaleLB',
            'totalCandidates',
            'totalJudges',
            'totalCategories'
        ));
    }
}
