<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JudgeAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pageant_id',
        'user_id',
    ];

    /**
     * Get the pageant for this assignment.
     */
    public function pageant()
    {
        return $this->belongsTo(Pageant::class);
    }

    /**
     * Get the judge (user) for this assignment.
     */
    public function judge()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all scores submitted through this assignment.
     */
    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
