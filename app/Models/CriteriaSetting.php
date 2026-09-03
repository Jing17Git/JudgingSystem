<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CriteriaSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'stage',
        'percentage',
        'sort_order',
    ];

    protected $casts = [
        'percentage' => 'float',
        'sort_order' => 'integer',
    ];

    /**
     * Get criteria settings as a key-value associative array: [ 'key' => percentage ]
     */
    public static function getPercentageMap(?string $stage = null): array
    {
        $query = static::query();
        if ($stage) {
            $query->where('stage', $stage);
        }

        return $query->pluck('percentage', 'key')->toArray();
    }

    /**
     * Get Preliminary stage percentage map
     */
    public static function getPreliminaryMap(): array
    {
        return static::where('stage', 'preliminary')->pluck('percentage', 'key')->toArray();
    }

    /**
     * Get Final stage percentage map
     */
    public static function getFinalMap(): array
    {
        return static::where('stage', 'final')->pluck('percentage', 'key')->toArray();
    }
}
