<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'pageant_id',
        'candidate_number',
        'first_name',
        'last_name',
        'photo_url',
        'bio',
        'origin',
    ];

    /**
     * Get the full name of the candidate.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the pageant that this candidate belongs to.
     */
    public function pageant()
    {
        return $this->belongsTo(Pageant::class);
    }

    /**
     * Get all scores for this candidate.
     */
    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
