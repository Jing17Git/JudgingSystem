<?php

namespace App\Events;

use App\Http\Controllers\Admin\DashboardController;
use App\Models\AuditRecord;
use App\Models\Candidate;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScoreSubmitted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $category;

    public int $candidateId;

    public int $judgeId;

    public ?float $score;

    public string $action; // 'saved' or 'reset'

    public string $judgeName;

    public int $candidateNumber;

    public string $candidateName;

    public string $candidateGender;

    public string $categoryName;

    public array $dashboardData;

    public string $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(string $category, int $candidateId, int $judgeId, ?float $score, string $action = 'saved')
    {
        $this->category = $category;
        $this->candidateId = $candidateId;
        $this->judgeId = $judgeId;
        $this->score = $score;
        $this->action = $action;
        $this->timestamp = now()->toIso8601String();

        $judge = User::find($judgeId);
        $this->judgeName = $judge ? $judge->name : 'Unknown Judge';

        $candidate = Candidate::find($candidateId);
        if ($candidate) {
            $this->candidateNumber = (int) $candidate->candidate_number;
            $this->candidateName = $candidate->display_name;
            $this->candidateGender = $candidate->gender;
        } else {
            $this->candidateNumber = 0;
            $this->candidateName = 'Unknown Candidate';
            $this->candidateGender = 'Unknown';
        }

        $categoryNames = [
            'production' => 'Production',
            'fitness' => 'Fitness',
            'traditional-attire' => 'Traditional Attire',
            'indigenous-attire' => 'Indigenous Attire',
            'qa' => 'Final Q & A',
            'qanda' => 'Final Q & A',
        ];
        $this->categoryName = $categoryNames[$category] ?? ucfirst(str_replace('-', ' ', $category));

        // Generate fresh dashboard snapshot for instant Admin UI updates
        $this->dashboardData = DashboardController::calculateDashboardData();

        // Write real-time audit record entry with anomaly detection
        try {
            // Find previous score if any
            $oldScore = AuditRecord::where('user_id', $judgeId)
                ->where('candidate_id', $candidateId)
                ->where('category', $category)
                ->where('event_type', 'score_submitted')
                ->latest()
                ->value('new_score');

            $desc = $action === 'reset'
                ? "Reset score for Candidate #{$this->candidateNumber} ({$this->candidateName}) in {$this->categoryName}"
                : "Recorded score of {$this->score} for Candidate #{$this->candidateNumber} ({$this->candidateName}) in {$this->categoryName}";

            AuditRecord::logScoreEvent(
                $action === 'reset' ? 'score_reset' : 'score_submitted',
                $category,
                $judgeId,
                $this->judgeName,
                $candidateId,
                $this->candidateName,
                $this->candidateNumber,
                $oldScore ? (float) $oldScore : null,
                $action === 'reset' ? null : ($this->score !== null ? (float) $this->score : null),
                $desc,
                ['score' => $this->score, 'category' => $category, 'action' => $action, 'old_score' => $oldScore]
            );
        } catch (\Throwable $e) {
            // Ignore insert exception
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.scores'),
            new PrivateChannel('judge.scores'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'score.submitted';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'category' => $this->category,
            'category_name' => $this->categoryName,
            'candidate_id' => $this->candidateId,
            'candidate_number' => $this->candidateNumber,
            'candidate_name' => $this->candidateName,
            'candidate_gender' => $this->candidateGender,
            'judge_id' => $this->judgeId,
            'judge_name' => $this->judgeName,
            'score' => $this->score !== null ? number_format($this->score, 2) : null,
            'action' => $this->action,
            'timestamp' => $this->timestamp,
            'dashboardData' => $this->dashboardData,
        ];
    }
}
