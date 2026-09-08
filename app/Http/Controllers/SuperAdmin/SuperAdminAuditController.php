<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditRecord;
use Illuminate\Http\Request;

class SuperAdminAuditController extends Controller
{
    /**
     * Display the Super-Admin Audit Trail & Security Ledger.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $categoryFilter = trim($request->input('category', ''));
        $eventFilter = trim($request->input('event_type', ''));
        $riskFilter = trim($request->input('risk_level', ''));
        $suspiciousOnly = $request->boolean('suspicious_only', false);
        $datePreset = trim($request->input('date_preset', 'all'));

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

        $auditRecords = $query->paginate(25)->withQueryString();

        // Key statistics
        $stats = [
            'total_records' => AuditRecord::count(),
            'score_submissions' => AuditRecord::where('event_type', 'score_submitted')->count(),
            'score_resets' => AuditRecord::where('event_type', 'score_reset')->count(),
            'flagged_suspicious' => AuditRecord::where('is_suspicious', true)->count(),
            'critical_alerts' => AuditRecord::where('risk_level', 'critical')->count(),
            'warning_alerts' => AuditRecord::where('risk_level', 'warning')->count(),
            'unique_judges' => AuditRecord::whereNotNull('user_id')->distinct('user_id')->count('user_id'),
        ];

        return view('super-admin.settings.audit_record', compact(
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
     * Add Super-Admin review notes to an audit record entry.
     */
    public function review(Request $request, AuditRecord $record)
    {
        $validated = $request->validate([
            'review_notes' => 'required|string|max:1000',
        ]);

        $record->update([
            'reviewer_id' => auth()->id(),
            'reviewer_name' => auth()->user()?->name ?? 'Super Admin',
            'review_notes' => $validated['review_notes'],
            'reviewed_at' => now(),
            'status' => 'reviewed',
        ]);

        return redirect()->back()->with('success', 'Audit entry reviewed and notes saved successfully.');
    }

    /**
     * Toggle manual security flag on an audit record.
     */
    public function flag(Request $request, AuditRecord $record)
    {
        $record->update([
            'is_suspicious' => ! $record->is_suspicious,
            'suspicious_reason' => $record->is_suspicious ? null : 'Manually flagged for review by Super Admin',
            'risk_level' => $record->is_suspicious ? 'normal' : 'warning',
        ]);

        $statusMsg = $record->is_suspicious ? 'Record flagged for security review.' : 'Flag removed from audit record.';

        return redirect()->back()->with('success', $statusMsg);
    }

    /**
     * Clear all Audit Records from system.
     */
    public function clear(Request $request)
    {
        AuditRecord::truncate();

        try {
            AuditRecord::create([
                'event_type' => 'audit_cleared',
                'category' => 'system',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Super Admin',
                'user_role' => 'super-admin',
                'action_description' => 'System audit logs cleared by Super Administrator',
                'ip_address' => $request->ip(),
                'status' => 'warning',
                'risk_level' => 'warning',
            ]);
        } catch (\Throwable $e) {
        }

        return redirect()->route('super-admin.settings.audit_record')
            ->with('success', 'Audit records have been cleared successfully.');
    }

    /**
     * Export Audit Records as CSV.
     */
    public function export()
    {
        $records = AuditRecord::orderBy('created_at', 'desc')->get();
        $filename = 'super_admin_audit_records_'.now()->format('Y_m_d_His').'.csv';

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
                'User Name',
                'User Role',
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
                    $r->action_description ?? 'N/A',
                    $r->ip_address ?? 'N/A',
                    $r->risk_level ?? 'normal',
                    $r->is_suspicious ? 'YES' : 'NO',
                    $r->suspicious_reason ?? 'None',
                    $r->reviewer_name ?? 'Pending',
                    $r->review_notes ?? '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
