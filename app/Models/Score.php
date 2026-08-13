<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'judge_assignment_id',
        'candidate_id',
        'criterion_id',
        'score',
        'is_locked',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'is_locked' => 'boolean',
        ];
    }

    /**
     * Get the judge assignment that submitted this score.
     */
    public function judgeAssignment()
    {
        return $this->belongsTo(JudgeAssignment::class);
    }

    /**
     * Get the candidate being scored.
     */
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Get the criterion being scored.
     */
    public function criterion()
    {
        return $this->belongsTo(Criterion::class);
    }
}
