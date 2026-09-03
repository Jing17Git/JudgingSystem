<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomCategoryScore extends Model
{
    use HasFactory;

    protected $table = 'custom_category_scores';

    protected $fillable = [
        'candidate_id',
        'judge_id',
        'category_key',
        'score',
    ];

    protected $casts = [
        'score' => 'float',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function judge()
    {
        return $this->belongsTo(User::class, 'judge_id');
    }

    /**
     * Get all scores for a given category key, keyed by "candidate_id_judge_id".
     */
    public static function forCategory(string $key)
    {
        return static::where('category_key', $key)
            ->get()
            ->keyBy(fn($s) => $s->candidate_id . '_' . $s->judge_id);
    }

    /**
     * Get all scores for a given category key, grouped by candidate_id.
     */
    public static function forCategoryGrouped(string $key)
    {
        return static::where('category_key', $key)
            ->get()
            ->groupBy('candidate_id');
    }
}
