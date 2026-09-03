<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditRecord;
use App\Models\Candidate;
use App\Models\CriteriaSetting;
use App\Models\FitnessScore;
use App\Models\IndigenousAttireScore;
use App\Models\ProductionScore;
use App\Models\QaScore;
use App\Models\TraditionalAttireScore;
use App\Models\User;
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
        $judges = User::where('role', 'judge')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $candidates = Candidate::orderBy('gender')
            ->orderBy('candidate_number')
            ->get();

        $maleCandidates = $candidates->filter(fn ($c) => $c->gender === 'Male');
        $femaleCandidates = $candidates->filter(fn ($c) => $c->gender === 'Female');

        $prodScores = ProductionScore::all()->keyBy(fn ($s) => $s->judge_id.'_'.$s->candidate_id);
        $fitScores = FitnessScore::all()->keyBy(fn ($s) => $s->judge_id.'_'.$s->candidate_id);
        $tradScores = TraditionalAttireScore::all()->keyBy(fn ($s) => $s->judge_id.'_'.$s->candidate_id);
        $indigScores = IndigenousAttireScore::all()->keyBy(fn ($s) => $s->judge_id.'_'.$s->candidate_id);
        $qaScores = QaScore::all()->keyBy(fn ($s) => $s->judge_id.'_'.$s->candidate_id);

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

        // Log audit record for settings update
        try {
            AuditRecord::create([
                'event_type' => 'settings_updated',
                'category' => 'system',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Admin',
                'user_role' => 'admin',
                'action_description' => "Updated {$stage} criteria percentage settings (Total: 100%)",
                'details' => json_encode($validated['percentages']),
                'ip_address' => $request->ip(),
                'status' => 'success',
            ]);
        } catch (\Throwable $e) {
        }

        return redirect()->route($redirectRoute)
            ->with('success', ucfirst($stage).' criteria percentage settings updated successfully.');
    }

    /**
     * Display Comprehensive Audit Management Table & Anomaly Trail.
     */
    public function auditRecord(Request $request)
    {
        $search = trim($request->input('search', ''));
        $categoryFilter = trim($request->input('category', ''));
        $eventFilter = trim($request->input('event_type', ''));
        $riskFilter = trim($request->input('risk_level', ''));
        $suspiciousOnly = $request->boolean('suspicious_only', false);
        $datePreset = trim($request->input('date_preset', 'all'));

        // Seed initial audit records from scores if table is empty
        if (AuditRecord::count() === 0) {
            $this->seedHistoricalAuditRecords();
        }

        $query = AuditRecord::with(['user', 'candidate', 'reviewer'])->orderBy('created_at', 'desc');

        if ($categoryFilter) {
            $query->where('category', $categoryFilter);
        }

        if ($eventFilter) {
            $query->where('event_type', $eventFilter);
        }

        if ($riskFilter) {
            $query->where('risk_level', $riskFilter);
        }

        if ($suspiciousOnly) {
            $query->where('is_suspicious', true);
        }

        if ($datePreset === 'today') {
            $query->whereDate('created_at', now()->today());
        } elseif ($datePreset === '7days') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($datePreset === '30days') {
            $query->where('created_at', '>=', now()->subDays(30));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('action_description', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('candidate_name', 'like', "%{$search}%")
                    ->orWhere('candidate_number', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('suspicious_reason', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $auditRecords = $query->paginate(30)->withQueryString();

        // Calculate comprehensive statistics
        $stats = [
            'total_records' => AuditRecord::count(),
            'score_submissions' => AuditRecord::where('event_type', 'score_submitted')->count(),
            'score_resets' => AuditRecord::where('event_type', 'score_reset')->count(),
            'flagged_suspicious' => AuditRecord::where('is_suspicious', true)->count(),
            'critical_alerts' => AuditRecord::where('risk_level', 'critical')->count(),
            'warning_alerts' => AuditRecord::where('risk_level', 'warning')->count(),
            'unique_judges' => AuditRecord::whereNotNull('user_id')->distinct('user_id')->count('user_id'),
        ];

        return view('admin.settings.audit_record', compact(
            'auditRecords',
            'stats',
            'search',
            'categoryFilter',
            'eventFilter',
            'riskFilter',
            'suspiciousOnly',
            'datePreset'
        ));
    }

    /**
     * Add admin review notes to an audit record entry.
     */
    public function reviewAuditRecord(Request $request, AuditRecord $record)
    {
        $validated = $request->validate([
            'review_notes' => 'required|string|max:1000',
        ]);

        $record->update([
            'reviewer_id' => auth()->id(),
            'reviewer_name' => auth()->user()?->name ?? 'Admin',
            'review_notes' => $validated['review_notes'],
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Audit review note saved successfully.');
    }

    /**
     * Toggle suspicious flag on an audit record entry.
     */
    public function flagAuditRecord(Request $request, AuditRecord $record)
    {
        $record->update([
            'is_suspicious' => ! $record->is_suspicious,
            'suspicious_reason' => $record->is_suspicious ? null : 'Manually flagged for review by Admin',
            'risk_level' => $record->is_suspicious ? 'normal' : 'warning',
        ]);

        $statusMsg = $record->is_suspicious ? 'Record flagged for review.' : 'Flag removed from record.';

        return redirect()->back()->with('success', $statusMsg);
    }

    /**
     * Get candidate score modification history in JSON for modal.
     */
    public function candidateHistory(Request $request)
    {
        $candidateId = $request->input('candidate_id');
        if (! $candidateId) {
            return response()->json(['error' => 'Candidate ID required'], 400);
        }

        $records = AuditRecord::where('candidate_id', $candidateId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['records' => $records]);
    }

    /**
     * Clear all Audit Records from system.
     */
    public function clearAuditRecord(Request $request)
    {
        AuditRecord::truncate();

        try {
            AuditRecord::create([
                'event_type' => 'audit_cleared',
                'category' => 'system',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Admin',
                'user_role' => 'admin',
                'action_description' => 'System audit logs cleared by Administrator',
                'ip_address' => $request->ip(),
                'status' => 'warning',
                'risk_level' => 'warning',
            ]);
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.settings.audit_record')
            ->with('success', 'Audit records have been cleared successfully.');
    }

    /**
     * Export Audit Records as comprehensive CSV report.
     */
    public function exportAuditRecord()
    {
        $records = AuditRecord::orderBy('created_at', 'desc')->get();
        $filename = 'audit_records_report_'.now()->format('Y_m_d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Timestamp',
                'Event Type',
                'Category',
                'Judge Name',
                'Judge Role',
                'Candidate #',
                'Candidate Name',
                'Old Score',
                'New Score',
                'Action Description',
                'IP Address',
                'Threat Level',
                'Suspicious Flag',
                'Suspicious Reason',
                'Reviewed By',
                'Review Notes',
            ]);

            foreach ($records as $r) {
                fputcsv($file, [
                    $r->id,
                    $r->created_at->format('Y-m-d H:i:s'),
                    $r->event_type,
                    $r->category ?? 'N/A',
                    $r->user_name ?? 'System',
                    $r->user_role ?? 'system',
                    $r->candidate_number ? "#{$r->candidate_number}" : 'N/A',
                    $r->candidate_name ?? 'N/A',
                    $r->old_score ?? 'N/A',
                    $r->new_score ?? 'N/A',
                    $r->action_description,
                    $r->ip_address ?? 'N/A',
                    strtoupper($r->risk_level ?? 'normal'),
                    $r->is_suspicious ? 'YES' : 'NO',
                    $r->suspicious_reason ?? 'None',
                    $r->reviewer_name ?? 'Unreviewed',
                    $r->review_notes ?? 'None',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper to seed historical score entries into audit table if empty.
     */
    private function seedHistoricalAuditRecords()
    {
        $judges = User::where('role', 'judge')->get()->keyBy('id');
        $candidates = Candidate::all()->keyBy('id');

        $categories = [
            'production' => ['model' => ProductionScore::class, 'name' => 'Production'],
            'fitness' => ['model' => FitnessScore::class, 'name' => 'Fitness'],
            'traditional-attire' => ['model' => TraditionalAttireScore::class, 'name' => 'Traditional Attire'],
            'indigenous-attire' => ['model' => IndigenousAttireScore::class, 'name' => 'Indigenous Attire'],
            'qa' => ['model' => QaScore::class, 'name' => 'Final Q & A'],
        ];

        foreach ($categories as $catKey => $catInfo) {
            $scores = $catInfo['model']::orderBy('updated_at', 'asc')->get();
            foreach ($scores as $s) {
                $j = $judges[$s->judge_id] ?? null;
                $c = $candidates[$s->candidate_id] ?? null;

                AuditRecord::create([
                    'event_type' => 'score_submitted',
                    'category' => $catKey,
                    'user_id' => $s->judge_id,
                    'user_name' => $j?->name ?? "Judge #{$s->judge_id}",
                    'user_role' => 'judge',
                    'candidate_id' => $s->candidate_id,
                    'candidate_name' => $c?->display_name ?? "Candidate #{$s->candidate_id}",
                    'candidate_number' => $c?->candidate_number ?? 0,
                    'action_description' => "Recorded score of {$s->score} for Candidate #".($c?->candidate_number ?? $s->candidate_id)." in {$catInfo['name']}",
                    'details' => json_encode(['score' => (float) $s->score, 'category' => $catKey]),
                    'ip_address' => '127.0.0.1',
                    'status' => 'success',
                    'created_at' => $s->updated_at ?? $s->created_at ?? now(),
                    'updated_at' => $s->updated_at ?? $s->created_at ?? now(),
                ]);
            }
        }
    }
}
