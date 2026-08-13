<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pageant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'venue',
        'event_date',
        'status',
        'logo_url',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    /**
     * Get the categories for this pageant.
     */
    public function categories()
    {
        return $this->hasMany(Category::class)->orderBy('sort_order');
    }

    /**
     * Get the candidates for this pageant.
     */
    public function candidates()
    {
        return $this->hasMany(Candidate::class)->orderBy('candidate_number');
    }

    /**
     * Get the judge assignments for this pageant.
     */
    public function judgeAssignments()
    {
        return $this->hasMany(JudgeAssignment::class);
    }

    /**
     * Get the judges assigned to this pageant.
     */
    public function judges()
    {
        return $this->belongsToMany(User::class, 'judge_assignments');
    }

    /**
     * Check if the pageant is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the pageant is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get all scores for this pageant through judge assignments.
     */
    public function scores()
    {
        return Score::whereIn('judge_assignment_id', $this->judgeAssignments()->pluck('id'));
    }
}
