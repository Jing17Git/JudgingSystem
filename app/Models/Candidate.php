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
        'full_name',
        'gender',
        'first_name',
        'last_name',
        'photo_url',
        'bio',
        'origin',
    ];

    /**
     * Mutator to automatically capitalize candidate names (Title Case).
     */
    public function setFullNameAttribute($value): void
    {
        $this->attributes['full_name'] = !empty($value) ? mb_convert_case(trim($value), MB_CASE_TITLE, 'UTF-8') : $value;
        $this->attributes['first_name'] = $this->attributes['full_name'];
    }

    /**
     * Get the display name — prefers full_name, falls back to first+last.
     */
    public function getDisplayNameAttribute(): string
    {
        if (!empty($this->full_name)) {
            return $this->full_name;
        }
        return trim("{$this->first_name} {$this->last_name}");
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
