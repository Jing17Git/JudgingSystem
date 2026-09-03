<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'category',
        'user_id',
        'user_name',
        'user_role',
        'candidate_id',
        'candidate_name',
        'candidate_number',
        'old_score',
        'new_score',
        'action_description',
        'details',
        'ip_address',
        'user_agent',
        'status',
        'risk_level',
        'is_suspicious',
        'suspicious_reason',
        'reviewer_id',
        'reviewer_name',
        'review_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'old_score' => 'float',
        'new_score' => 'float',
        'is_suspicious' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Helper to quickly log score & audit events with automated anomaly detection.
     */
    public static function logScoreEvent(
        string $eventType, // 'score_submitted' or 'score_reset'
        string $category,
        int $judgeId,
        string $judgeName,
        int $candidateId,
        string $candidateName,
        int $candidateNumber,
        ?float $oldScore,
        ?float $newScore,
        string $description,
        ?array $details = null
    ): self {
        $ipAddress = request()->ip() ?? '127.0.0.1';
        $userAgent = request()->header('User-Agent') ?? 'System/Browser';

        // Perform automated security anomaly evaluation
        $anomaly = static::evaluateAnomaly(
            $eventType,
            $category,
            $judgeId,
            $candidateId,
            $oldScore,
            $newScore
        );

        return static::create([
            'event_type' => $eventType,
            'category' => $category,
            'user_id' => $judgeId,
            'user_name' => $judgeName,
            'user_role' => 'judge',
            'candidate_id' => $candidateId,
            'candidate_name' => $candidateName,
            'candidate_number' => $candidateNumber,
            'old_score' => $oldScore,
            'new_score' => $newScore,
            'action_description' => $description,
            'details' => json_encode($details ?? ['score' => $newScore, 'category' => $category]),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'status' => $anomaly['status'],
            'risk_level' => $anomaly['risk_level'],
            'is_suspicious' => $anomaly['is_suspicious'],
            'suspicious_reason' => $anomaly['suspicious_reason'],
        ]);
    }

    /**
     * Security Anomaly Evaluation Rules Engine.
     */
    public static function evaluateAnomaly(
        string $eventType,
        string $category,
        int $judgeId,
        int $candidateId,
        ?float $oldScore,
        ?float $newScore
    ): array {
        $riskLevel = 'normal';
        $isSuspicious = false;
        $reasons = [];
        $status = 'success';

        // Rule 1: Excessive Score Manipulation (Red Critical Alert)
        // If > 3 resets for the same candidate within 1 hour
        if ($eventType === 'score_reset') {
            $recentResetsCount = static::where('user_id', $judgeId)
                ->where('candidate_id', $candidateId)
                ->where('event_type', 'score_reset')
                ->where('created_at', '>=', now()->subHour())
                ->count();

            if ($recentResetsCount >= 3) {
                $riskLevel = 'critical';
                $isSuspicious = true;
                $reasons[] = 'Excessive Score Manipulation (>3 score resets for Candidate in 1h)';
                $status = 'error';
            } else {
                // Rule 2: Unusual Score Modification (Yellow Warning)
                // Score reset occurs within 5 minutes of previous submission
                $lastSubmission = static::where('user_id', $judgeId)
                    ->where('candidate_id', $candidateId)
                    ->where('event_type', 'score_submitted')
                    ->latest()
                    ->first();

                if ($lastSubmission && $lastSubmission->created_at->diffInMinutes(now()) <= 5) {
                    if ($riskLevel !== 'critical') {
                        $riskLevel = 'warning';
                        $status = 'warning';
                    }
                    $isSuspicious = true;
                    $reasons[] = 'Unusual Score Modification (Score reset within 5 mins of submission)';
                }
            }
        }

        // Rule 3: Anomalous Score (Yellow Warning)
        // Score deviates by > 2.0 points from overall category average for that candidate
        if ($eventType === 'score_submitted' && $newScore !== null) {
            $catAvg = static::where('category', $category)
                ->where('event_type', 'score_submitted')
                ->whereNotNull('new_score')
                ->avg('new_score');

            if ($catAvg && abs($newScore - $catAvg) >= 2.0) {
                if ($riskLevel !== 'critical') {
                    $riskLevel = 'warning';
                    $status = 'warning';
                }
                $isSuspicious = true;
                $diff = number_format(abs($newScore - $catAvg), 1);
                $reasons[] = "Anomalous Score (Deviates by {$diff} points from category average of ".number_format($catAvg, 1).')';
            }

            // Rule 4: No Score Variance (Yellow Warning)
            // Judge gives identical/narrow range scores (<0.5 variance) across 4+ candidates in category
            $judgeCatScores = static::where('user_id', $judgeId)
                ->where('category', $category)
                ->where('event_type', 'score_submitted')
                ->whereNotNull('new_score')
                ->latest()
                ->take(5)
                ->pluck('new_score');

            if ($judgeCatScores->count() >= 4) {
                $maxS = $judgeCatScores->max();
                $minS = $judgeCatScores->min();
                if (($maxS - $minS) <= 0.25) {
                    if ($riskLevel !== 'critical') {
                        $riskLevel = 'warning';
                        $status = 'warning';
                    }
                    $isSuspicious = true;
                    $reasons[] = 'No Score Variance (Judge gave nearly identical scores across candidates)';
                }
            }
        }

        return [
            'status' => $status,
            'risk_level' => $riskLevel,
            'is_suspicious' => $isSuspicious,
            'suspicious_reason' => ! empty($reasons) ? implode('; ', $reasons) : null,
        ];
    }
}
