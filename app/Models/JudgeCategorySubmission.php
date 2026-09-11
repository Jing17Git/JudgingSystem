<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JudgeCategorySubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'judge_id',
        'category',
        'is_finalized',
        'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'is_finalized' => 'boolean',
            'finalized_at' => 'datetime',
        ];
    }

    public function judge(): BelongsTo
    {
        return $this->belongsTo(User::class, 'judge_id');
    }

    /**
     * Normalize category keys (e.g. qanda -> qa).
     */
    public static function normalizeCategory(string $category): string
    {
        $cat = trim($category);
        if ($cat === 'qanda') {
            return 'qa';
        }
        return $cat;
    }

    /**
     * Check if a category has been finalized by a judge.
     */
    public static function isFinalized(int $judgeId, string $category): bool
    {
        $normalized = self::normalizeCategory($category);

        return self::where('judge_id', $judgeId)
            ->where(function ($q) use ($normalized, $category) {
                $q->where('category', $normalized)
                  ->orWhere('category', $category);
            })
            ->where('is_finalized', true)
            ->exists();
    }

    /**
     * Get submission details if finalized.
     */
    public static function getSubmission(int $judgeId, string $category): ?self
    {
        $normalized = self::normalizeCategory($category);

        return self::where('judge_id', $judgeId)
            ->where(function ($q) use ($normalized, $category) {
                $q->where('category', $normalized)
                  ->orWhere('category', $category);
            })
            ->where('is_finalized', true)
            ->first();
    }

    /**
     * Finalize category scoring for a judge.
     */
    public static function finalize(int $judgeId, string $category): self
    {
        $normalized = self::normalizeCategory($category);

        return self::updateOrCreate(
            [
                'judge_id' => $judgeId,
                'category' => $normalized,
            ],
            [
                'is_finalized' => true,
                'finalized_at' => now(),
            ]
        );
    }

    /**
     * Unlock category scoring for a judge.
     */
    public static function unlock(int $judgeId, string $category): bool
    {
        $normalized = self::normalizeCategory($category);

        return (bool) self::where('judge_id', $judgeId)
            ->where(function ($q) use ($normalized, $category) {
                $q->where('category', $normalized)
                  ->orWhere('category', $category);
            })
            ->delete();
    }
}
