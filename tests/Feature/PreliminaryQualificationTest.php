<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CriteriaSetting;
use App\Models\CustomCategoryScore;
use App\Models\FitnessScore;
use App\Models\IndigenousAttireScore;
use App\Models\ProductionScore;
use App\Models\TraditionalAttireScore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreliminaryQualificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed 6 required preliminary criteria with custom percentage weights totaling 100%
        CriteriaSetting::updateOrCreate(['key' => 'production'], ['name' => 'Production', 'stage' => 'preliminary', 'percentage' => 20.0, 'sort_order' => 1]);
        CriteriaSetting::updateOrCreate(['key' => 'fitness'], ['name' => 'Fitness', 'stage' => 'preliminary', 'percentage' => 20.0, 'sort_order' => 2]);
        CriteriaSetting::updateOrCreate(['key' => 'indigenous_attire'], ['name' => 'Indigenous Attire', 'stage' => 'preliminary', 'percentage' => 15.0, 'sort_order' => 3]);
        CriteriaSetting::updateOrCreate(['key' => 'traditional_attire'], ['name' => 'Traditional Attire', 'stage' => 'preliminary', 'percentage' => 15.0, 'sort_order' => 4]);
        CriteriaSetting::updateOrCreate(['key' => 'talent_portion'], ['name' => 'Talent Portion', 'stage' => 'preliminary', 'percentage' => 15.0, 'sort_order' => 5]);
        CriteriaSetting::updateOrCreate(['key' => 'photogenic'], ['name' => 'Photogenic', 'stage' => 'preliminary', 'percentage' => 15.0, 'sort_order' => 6]);
    }

    /**
     * TEST 1 — Candidate With All Categories
     */
    public function test_candidate_with_all_categories_is_fully_scored_and_qualified(): void
    {
        $judge = User::create([
            'name' => 'Judge 1',
            'username' => 'judge1',
            'email' => 'judge1@example.com',
            'password' => bcrypt('password'),
            'role' => 'judge',
            'is_active' => true,
        ]);

        $candidate = Candidate::create([
            'candidate_number' => 1,
            'full_name' => 'Jane Doe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'Female',
        ]);

        ProductionScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => 9.5]);
        FitnessScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => 9.0]);
        IndigenousAttireScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => 8.5]);
        TraditionalAttireScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => 9.0]);
        CustomCategoryScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'category_key' => 'talent_portion', 'score' => 9.2]);
        CustomCategoryScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'category_key' => 'photogenic', 'score' => 8.8]);

        $qualifiedIds = Candidate::getTop5QualifiedIds();

        $this->assertContains($candidate->id, $qualifiedIds);
    }

    /**
     * TEST 2 — Missing Photogenic
     */
    public function test_candidate_missing_photogenic_is_not_fully_scored_and_cannot_qualify(): void
    {
        $judge = User::create([
            'name' => 'Judge 1',
            'username' => 'judge1',
            'email' => 'judge1@example.com',
            'password' => bcrypt('password'),
            'role' => 'judge',
            'is_active' => true,
        ]);

        $candidate = Candidate::create([
            'candidate_number' => 1,
            'full_name' => 'Jane Doe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'Female',
        ]);

        ProductionScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => 9.5]);
        FitnessScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => 9.0]);
        IndigenousAttireScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => 8.5]);
        TraditionalAttireScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => 9.0]);
        CustomCategoryScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'category_key' => 'talent_portion', 'score' => 9.2]);
        // Missing Photogenic!

        $qualifiedIds = Candidate::getTop5QualifiedIds();

        $this->assertNotContains($candidate->id, $qualifiedIds);
    }

    /**
     * TEST 3 — Missing Any Required Category Individually
     */
    public function test_missing_any_required_category_prevents_qualification(): void
    {
        $judge = User::create([
            'name' => 'Judge 1',
            'username' => 'judge1',
            'email' => 'judge1@example.com',
            'password' => bcrypt('password'),
            'role' => 'judge',
            'is_active' => true,
        ]);

        $categories = ['production', 'fitness', 'indigenous_attire', 'traditional_attire', 'talent_portion', 'photogenic'];

        foreach ($categories as $missingCategory) {
            $candidate = Candidate::create([
                'candidate_number' => rand(10, 999),
                'full_name' => "Test Candidate {$missingCategory}",
                'first_name' => 'Test',
                'last_name' => $missingCategory,
                'gender' => 'Female',
            ]);

            if ($missingCategory !== 'production') {
                ProductionScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => 9.0]);
            }
            if ($missingCategory !== 'fitness') {
                FitnessScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => 9.0]);
            }
            if ($missingCategory !== 'indigenous_attire') {
                IndigenousAttireScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => 9.0]);
            }
            if ($missingCategory !== 'traditional_attire') {
                TraditionalAttireScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => 9.0]);
            }
            if ($missingCategory !== 'talent_portion') {
                CustomCategoryScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'category_key' => 'talent_portion', 'score' => 9.0]);
            }
            if ($missingCategory !== 'photogenic') {
                CustomCategoryScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'category_key' => 'photogenic', 'score' => 9.0]);
            }

            $qualifiedIds = Candidate::getTop5QualifiedIds();
            $this->assertNotContains($candidate->id, $qualifiedIds, "Candidate missing {$missingCategory} should not qualify!");
        }
    }

    /**
     * TEST 4 — Missing One Judge's Score
     */
    public function test_candidate_missing_score_from_one_active_judge_is_incomplete(): void
    {
        $judge1 = User::create(['name' => 'Judge 1', 'username' => 'j1', 'email' => 'j1@example.com', 'password' => bcrypt('p'), 'role' => 'judge', 'is_active' => true]);
        $judge2 = User::create(['name' => 'Judge 2', 'username' => 'j2', 'email' => 'j2@example.com', 'password' => bcrypt('p'), 'role' => 'judge', 'is_active' => true]);

        $candidate = Candidate::create(['candidate_number' => 1, 'full_name' => 'Alice Smith', 'first_name' => 'Alice', 'last_name' => 'Smith', 'gender' => 'Female']);

        // Judge 1 scores all 6 categories
        ProductionScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge1->id, 'score' => 9.0]);
        FitnessScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge1->id, 'score' => 9.0]);
        IndigenousAttireScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge1->id, 'score' => 9.0]);
        TraditionalAttireScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge1->id, 'score' => 9.0]);
        CustomCategoryScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge1->id, 'category_key' => 'talent_portion', 'score' => 9.0]);
        CustomCategoryScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge1->id, 'category_key' => 'photogenic', 'score' => 9.0]);

        // Judge 2 scores only 5 categories (missing Photogenic)
        ProductionScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge2->id, 'score' => 9.0]);
        FitnessScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge2->id, 'score' => 9.0]);
        IndigenousAttireScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge2->id, 'score' => 9.0]);
        TraditionalAttireScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge2->id, 'score' => 9.0]);
        CustomCategoryScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge2->id, 'category_key' => 'talent_portion', 'score' => 9.0]);

        $qualifiedIds = Candidate::getTop5QualifiedIds();
        $this->assertNotContains($candidate->id, $qualifiedIds);
    }

    /**
     * TEST 5 — Correct Ranking Calculation
     */
    public function test_correct_ranking_derived_from_calculated_preliminary_overall_score(): void
    {
        $judge = User::create(['name' => 'Judge 1', 'username' => 'j1', 'email' => 'j1@example.com', 'password' => bcrypt('p'), 'role' => 'judge', 'is_active' => true]);

        // Create 6 candidates with descending overall scores
        $scores = [10.0, 9.0, 8.0, 7.0, 6.0, 5.0];
        $candidates = [];

        foreach ($scores as $idx => $scoreVal) {
            $c = Candidate::create(['candidate_number' => $idx + 1, 'full_name' => "Cand {$idx}", 'first_name' => 'Cand', 'last_name' => (string) $idx, 'gender' => 'Female']);
            ProductionScore::create(['candidate_id' => $c->id, 'judge_id' => $judge->id, 'score' => $scoreVal]);
            FitnessScore::create(['candidate_id' => $c->id, 'judge_id' => $judge->id, 'score' => $scoreVal]);
            IndigenousAttireScore::create(['candidate_id' => $c->id, 'judge_id' => $judge->id, 'score' => $scoreVal]);
            TraditionalAttireScore::create(['candidate_id' => $c->id, 'judge_id' => $judge->id, 'score' => $scoreVal]);
            CustomCategoryScore::create(['candidate_id' => $c->id, 'judge_id' => $judge->id, 'category_key' => 'talent_portion', 'score' => $scoreVal]);
            CustomCategoryScore::create(['candidate_id' => $c->id, 'judge_id' => $judge->id, 'category_key' => 'photogenic', 'score' => $scoreVal]);
            $candidates[] = $c;
        }

        $top5Ids = Candidate::getTop5QualifiedIds();

        // Top 5 must contain candidates 0 to 4 (Rank 1 to 5)
        for ($i = 0; $i < 5; $i++) {
            $this->assertContains($candidates[$i]->id, $top5Ids);
        }
        // Candidate 5 (Rank 6) must NOT be in top 5
        $this->assertNotContains($candidates[5]->id, $top5Ids);
    }

    /**
     * TEST 10 — Weighted Preliminary Calculation with Decimals
     */
    public function test_weighted_preliminary_calculation_applies_exact_criteria_percentages(): void
    {
        $judge = User::create(['name' => 'Judge 1', 'username' => 'j1', 'email' => 'j1@example.com', 'password' => bcrypt('p'), 'role' => 'judge', 'is_active' => true]);
        $candidate = Candidate::create(['candidate_number' => 1, 'full_name' => 'Eva Green', 'first_name' => 'Eva', 'last_name' => 'Green', 'gender' => 'Female']);

        // Production (20%): 8.5 avg -> 1.70
        // Fitness (20%): 9.0 avg -> 1.80
        // Indigenous (15%): 8.0 avg -> 1.20
        // Traditional (15%): 9.2 avg -> 1.38
        // Talent (15%): 8.4 avg -> 1.26
        // Photogenic (15%): 9.6 avg -> 1.44
        // Expected Grand Total = 1.70 + 1.80 + 1.20 + 1.38 + 1.26 + 1.44 = 8.78

        ProductionScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => 8.5]);
        FitnessScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => 9.0]);
        IndigenousAttireScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => 8.0]);
        TraditionalAttireScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => 9.2]);
        CustomCategoryScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'category_key' => 'talent_portion', 'score' => 8.4]);
        CustomCategoryScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'category_key' => 'photogenic', 'score' => 9.6]);

        $weights = CriteriaSetting::getPercentageMap();
        $pW = $weights['production'] / 100.0;
        $fW = $weights['fitness'] / 100.0;
        $iW = $weights['indigenous_attire'] / 100.0;
        $tW = $weights['traditional_attire'] / 100.0;
        $talW = $weights['talent_portion'] / 100.0;
        $phW = $weights['photogenic'] / 100.0;

        $calculatedTotal = (8.5 * $pW) + (9.0 * $fW) + (8.0 * $iW) + (9.2 * $tW) + (8.4 * $talW) + (9.6 * $phW);

        $this->assertEqualsWithDelta(8.78, $calculatedTotal, 0.001);
    }

    /**
     * TEST 13 — Ties
     */
    public function test_ties_are_broken_deterministically_using_candidate_number_order(): void
    {
        $judge = User::create(['name' => 'Judge 1', 'username' => 'j1', 'email' => 'j1@example.com', 'password' => bcrypt('p'), 'role' => 'judge', 'is_active' => true]);

        // Create 2 candidates with exact same scores but different candidate numbers
        $candA = Candidate::create(['candidate_number' => 2, 'full_name' => 'Cand B (Num 2)', 'first_name' => 'Cand', 'last_name' => 'B', 'gender' => 'Female']);
        $candB = Candidate::create(['candidate_number' => 1, 'full_name' => 'Cand A (Num 1)', 'first_name' => 'Cand', 'last_name' => 'A', 'gender' => 'Female']);

        foreach ([$candA, $candB] as $c) {
            ProductionScore::create(['candidate_id' => $c->id, 'judge_id' => $judge->id, 'score' => 9.0]);
            FitnessScore::create(['candidate_id' => $c->id, 'judge_id' => $judge->id, 'score' => 9.0]);
            IndigenousAttireScore::create(['candidate_id' => $c->id, 'judge_id' => $judge->id, 'score' => 9.0]);
            TraditionalAttireScore::create(['candidate_id' => $c->id, 'judge_id' => $judge->id, 'score' => 9.0]);
            CustomCategoryScore::create(['candidate_id' => $c->id, 'judge_id' => $judge->id, 'category_key' => 'talent_portion', 'score' => 9.0]);
            CustomCategoryScore::create(['candidate_id' => $c->id, 'judge_id' => $judge->id, 'category_key' => 'photogenic', 'score' => 9.0]);
        }

        $top5Ids = Candidate::getTop5QualifiedIds();

        // Cand B (cand_num 1) must be prioritized over Cand A (cand_num 2) due to lower candidate_number tie-breaker
        $this->assertEquals($candB->id, $top5Ids[0]);
        $this->assertEquals($candA->id, $top5Ids[1]);
    }
}
