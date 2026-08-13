<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criterion extends Model
{
    use HasFactory;

    protected $table = 'criteria';

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'max_score',
        'weight_percentage',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'max_score' => 'decimal:2',
            'weight_percentage' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the category that owns this criterion.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the scores for this criterion.
     */
    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
