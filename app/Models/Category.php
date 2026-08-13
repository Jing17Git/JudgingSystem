<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'pageant_id',
        'name',
        'description',
        'weight_percentage',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'weight_percentage' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the pageant that owns this category.
     */
    public function pageant()
    {
        return $this->belongsTo(Pageant::class);
    }

    /**
     * Get the criteria for this category.
     */
    public function criteria()
    {
        return $this->hasMany(Criterion::class)->orderBy('sort_order');
    }
}
