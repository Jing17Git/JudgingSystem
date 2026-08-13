<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\ProductionScore;
use App\Models\FitnessScore;
use App\Models\TraditionalAttireScore;
use App\Models\IndigenousAttireScore;
use Illuminate\Http\Request;

class OverallController extends Controller
{
    /**
     * Display overall tabulation tables.
     */
    public function index()
    {
        $candidates = Candidate::orderBy('candidate_number')->get();

        // Fetch scores per candidate
        $prodScores   = ProductionScore::all()->groupBy('candidate_id');
        $fitScores    = FitnessScore::all()->groupBy('candidate_id');
        $tradScores   = TraditionalAttireScore::all()->groupBy('candidate_id');
        $indigScores  = IndigenousAttireScore::all()->groupBy('candidate_id');

        $breakdown = [];
        foreach ($candidates as $c) {
            $pTot = isset($prodScores[$c->id])  ? (float) $prodScores[$c->id]->sum('score')  : 0;
            $fTot = isset($fitScores[$c->id])   ? (float) $fitScores[$c->id]->sum('score')   : 0;
            $tTot = isset($tradScores[$c->id])  ? (float) $tradScores[$c->id]->sum('score')  : 0;
            $iTot = isset($indigScores[$c->id]) ? (float) $indigScores[$c->id]->sum('score') : 0;

            $grandTotal = $pTot + $fTot + $tTot + $iTot;

            $breakdown[$c->id] = [
                'production' => $pTot,
                'fitness'    => $fTot,
                'traditional'=> $tTot,
                'indigenous' => $iTot,
                'total'      => $grandTotal,
            ];
        }

        return view('admin.overall.index', compact('candidates', 'breakdown'));
    }
}
