<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\ProductionScore;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductionScore>
 */
class ProductionScoreFactory extends Factory
{
    protected $model = ProductionScore::class;

    public function definition(): array
    {
        return [
            'candidate_id' => Candidate::factory(),
            'judge_id' => User::factory(),
            'score' => $this->faker->numberBetween(0, 10),
        ];
    }
}
