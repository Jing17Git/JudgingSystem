<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CriteriaSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display Preliminary criteria percentage settings.
     */
    public function preliminary()
    {
        $settings = CriteriaSetting::where('stage', 'preliminary')->orderBy('sort_order')->get();
        $totalPercentage = $settings->sum('percentage');

        return view('admin.settings.preliminary', compact('settings', 'totalPercentage'));
    }

    /**
     * Alias for backward compatibility.
     */
    public function index()
    {
        return $this->preliminary();
    }

    /**
     * Display Final criteria percentage settings (Preliminary 30% vs Q&A 70%).
     */
    public function final()
    {
        $settings = CriteriaSetting::where('stage', 'final')->orderBy('sort_order')->get();
        $totalPercentage = $settings->sum('percentage');

        return view('admin.settings.final', compact('settings', 'totalPercentage'));
    }

    /**
     * Display individual judge score sheets for all categories and candidates with print signature.
     */
    public function judgeScores()
    {
        $judges = \App\Models\User::where('role', 'judge')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $candidates = \App\Models\Candidate::orderBy('gender')
            ->orderBy('candidate_number')
            ->get();

        $maleCandidates = $candidates->filter(fn($c) => $c->gender === 'Male');
        $femaleCandidates = $candidates->filter(fn($c) => $c->gender === 'Female');

        $prodScores  = \App\Models\ProductionScore::all()->keyBy(fn($s) => $s->judge_id . '_' . $s->candidate_id);
        $fitScores   = \App\Models\FitnessScore::all()->keyBy(fn($s) => $s->judge_id . '_' . $s->candidate_id);
        $tradScores  = \App\Models\TraditionalAttireScore::all()->keyBy(fn($s) => $s->judge_id . '_' . $s->candidate_id);
        $indigScores = \App\Models\IndigenousAttireScore::all()->keyBy(fn($s) => $s->judge_id . '_' . $s->candidate_id);
        $qaScores    = \App\Models\QaScore::all()->keyBy(fn($s) => $s->judge_id . '_' . $s->candidate_id);

        return view('admin.settings.judge_scores', compact(
            'judges',
            'candidates',
            'maleCandidates',
            'femaleCandidates',
            'prodScores',
            'fitScores',
            'tradScores',
            'indigScores',
            'qaScores'
        ));
    }

    /**
     * Update criteria percentage settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'percentages' => 'required|array',
            'percentages.*' => 'required|integer|min:0|max:100',
            'stage' => 'nullable|string|in:preliminary,final',
        ], [
            'percentages.*.integer' => 'Each criteria percentage must be a whole number.',
            'percentages.*.min' => 'Percentage cannot be less than 0%.',
            'percentages.*.max' => 'Percentage cannot exceed 100%.',
        ]);

        $total = array_sum($validated['percentages']);

        if ($total !== 100) {
            return redirect()->back()
                ->withInput()
                ->with('error', "The total percentage must equal exactly 100%. Current total is {$total}%.");
        }

        foreach ($validated['percentages'] as $key => $percentage) {
            CriteriaSetting::where('key', $key)->update([
                'percentage' => (int) $percentage,
            ]);
        }

        $stage = $request->input('stage', 'preliminary');
        $redirectRoute = $stage === 'final' ? 'admin.settings.final' : 'admin.settings.preliminary';

        return redirect()->route($redirectRoute)
            ->with('success', ucfirst($stage) . ' criteria percentage settings updated successfully.');
    }
}
