<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CriteriaSetting;
use App\Models\CustomCategoryScore;
use App\Models\FitnessScore;
use App\Models\IndigenousAttireScore;
use App\Models\ProductionScore;
use App\Models\QaScore;
use App\Models\TraditionalAttireScore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntramuralsProductionScenarioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // 6 Required Preliminary Categories totaling 100%
        CriteriaSetting::updateOrCreate(['key' => 'production'], ['name' => 'Production', 'stage' => 'preliminary', 'percentage' => 20.0, 'sort_order' => 1]);
        CriteriaSetting::updateOrCreate(['key' => 'fitness'], ['name' => 'Fitness', 'stage' => 'preliminary', 'percentage' => 20.0, 'sort_order' => 2]);
        CriteriaSetting::updateOrCreate(['key' => 'indigenous_attire'], ['name' => 'Indigenous Attire', 'stage' => 'preliminary', 'percentage' => 15.0, 'sort_order' => 3]);
        CriteriaSetting::updateOrCreate(['key' => 'traditional_attire'], ['name' => 'Traditional Attire', 'stage' => 'preliminary', 'percentage' => 15.0, 'sort_order' => 4]);
        CriteriaSetting::updateOrCreate(['key' => 'talent_portion'], ['name' => 'Talent Portion', 'stage' => 'preliminary', 'percentage' => 15.0, 'sort_order' => 5]);
        CriteriaSetting::updateOrCreate(['key' => 'photogenic'], ['name' => 'Photogenic', 'stage' => 'preliminary', 'percentage' => 15.0, 'sort_order' => 6]);
    }

    /**
     * Create the realistic Intramurals candidate pool:
     * BSIT: 3 Male, 3 Female (6 total)
     * BSAB: 3 Male, 3 Female (6 total)
     * BEED: 2 Male, 2 Female (4 total)
     * Total: 8 Male, 8 Female = 16 Candidates.
     */
    private function createIntramuralsCandidates(): array
    {
        $candidates = ['male' => [], 'female' => []];

        // BSIT (Pairs 1 to 3)
        for ($i = 1; $i <= 3; $i++) {
            $candidates['male'][] = Candidate::create([
                'candidate_number' => $i,
                'full_name' => "BSIT Male Candidate {$i}",
                'first_name' => 'BSIT',
                'last_name' => "Male {$i}",
                'gender' => 'Male',
                'origin' => 'BSIT',
            ]);
            $candidates['female'][] = Candidate::create([
                'candidate_number' => $i,
                'full_name' => "BSIT Female Candidate {$i}",
                'first_name' => 'BSIT',
                'last_name' => "Female {$i}",
                'gender' => 'Female',
                'origin' => 'BSIT',
            ]);
        }

        // BSAB (Pairs 4 to 6)
        for ($i = 4; $i <= 6; $i++) {
            $candidates['male'][] = Candidate::create([
                'candidate_number' => $i,
                'full_name' => "BSAB Male Candidate {$i}",
                'first_name' => 'BSAB',
                'last_name' => "Male {$i}",
                'gender' => 'Male',
                'origin' => 'BSAB',
            ]);
            $candidates['female'][] = Candidate::create([
                'candidate_number' => $i,
                'full_name' => "BSAB Female Candidate {$i}",
                'first_name' => 'BSAB',
                'last_name' => "Female {$i}",
                'gender' => 'Female',
                'origin' => 'BSAB',
            ]);
        }

        // BEED (Pairs 7 to 8)
        for ($i = 7; $i <= 8; $i++) {
            $candidates['male'][] = Candidate::create([
                'candidate_number' => $i,
                'full_name' => "BEED Male Candidate {$i}",
                'first_name' => 'BEED',
                'last_name' => "Male {$i}",
                'gender' => 'Male',
                'origin' => 'BEED',
            ]);
            $candidates['female'][] = Candidate::create([
                'candidate_number' => $i,
                'full_name' => "BEED Female Candidate {$i}",
                'first_name' => 'BEED',
                'last_name' => "Female {$i}",
                'gender' => 'Female',
                'origin' => 'BEED',
            ]);
        }

        return $candidates;
    }

    /**
     * Create 6 Active Judges.
     */
    private function createJudges(int $count = 6): array
    {
        $judges = [];
        for ($j = 1; $j <= $count; $j++) {
            $judges[] = User::create([
                'name' => "Judge {$j}",
                'username' => "judge{$j}",
                'email' => "judge{$j}@intramurals.cpsu.edu.ph",
                'password' => bcrypt('password123'),
                'role' => 'judge',
                'judge_number' => $j,
                'is_active' => true,
            ]);
        }

        return $judges;
    }

    /**
     * Helper to score a candidate across all 6 categories by a given judge.
     */
    private function scoreCandidateCategory(Candidate $candidate, User $judge, float $baseScore): void
    {
        ProductionScore::updateOrCreate(['candidate_id' => $candidate->id, 'judge_id' => $judge->id], ['score' => $baseScore]);
        FitnessScore::updateOrCreate(['candidate_id' => $candidate->id, 'judge_id' => $judge->id], ['score' => $baseScore]);
        IndigenousAttireScore::updateOrCreate(['candidate_id' => $candidate->id, 'judge_id' => $judge->id], ['score' => $baseScore]);
        TraditionalAttireScore::updateOrCreate(['candidate_id' => $candidate->id, 'judge_id' => $judge->id], ['score' => $baseScore]);
        CustomCategoryScore::updateOrCreate(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'category_key' => 'talent_portion'], ['score' => $baseScore]);
        CustomCategoryScore::updateOrCreate(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'category_key' => 'photogenic'], ['score' => $baseScore]);
    }

    /**
     * 1. VERIFY DEPARTMENT & GENDER STRUCTURE (16 candidates: 8 Male, 8 Female, BSIT 6, BSAB 6, BEED 4)
     */
    public function test_intramurals_population_structure_and_departments(): void
    {
        $candidates = $this->createIntramuralsCandidates();

        $this->assertCount(8, $candidates['male']);
        $this->assertCount(8, $candidates['female']);
        $this->assertEquals(16, Candidate::count());

        $this->assertEquals(6, Candidate::where('origin', 'BSIT')->count());
        $this->assertEquals(6, Candidate::where('origin', 'BSAB')->count());
        $this->assertEquals(4, Candidate::where('origin', 'BEED')->count());
    }

    /**
     * 2. FULL SCORING VOLUME & TOP 5 QUALIFICATION (576 preliminary score records)
     */
    public function test_full_scoring_volume_and_top_5_qualification_with_6_judges_and_6_categories(): void
    {
        $pool = $this->createIntramuralsCandidates();
        $judges = $this->createJudges(6);

        // Assign descending scores so candidate 1 is highest, candidate 8 is lowest in both divisions
        // Male: candidate 1 = 9.8, ..., candidate 8 = 7.0
        // Female: candidate 1 = 9.9, ..., candidate 8 = 7.1
        foreach ($pool['male'] as $idx => $cand) {
            $score = 9.8 - ($idx * 0.4);
            foreach ($judges as $judge) {
                $this->scoreCandidateCategory($cand, $judge, $score);
            }
        }

        foreach ($pool['female'] as $idx => $cand) {
            $score = 9.9 - ($idx * 0.4);
            foreach ($judges as $judge) {
                $this->scoreCandidateCategory($cand, $judge, $score);
            }
        }

        // Verify total score records: 16 candidates * 6 judges = 96 records per category
        $this->assertEquals(96, ProductionScore::count());
        $this->assertEquals(96, FitnessScore::count());
        $this->assertEquals(96, IndigenousAttireScore::count());
        $this->assertEquals(96, TraditionalAttireScore::count());
        // Custom categories: 2 * 96 = 192 records
        $this->assertEquals(192, CustomCategoryScore::count());

        // Total preliminary records: 96 * 4 + 192 = 576 records
        $totalScores = ProductionScore::count() + FitnessScore::count() + IndigenousAttireScore::count()
                     + TraditionalAttireScore::count() + CustomCategoryScore::count();
        $this->assertEquals(576, $totalScores);

        // Compute Top 5
        $top5Ids = Candidate::getTop5QualifiedIds();

        // Must qualify exactly 5 Males + 5 Females = 10 Total Finalists
        $this->assertCount(10, $top5Ids);

        // Verify Males: Rank 1-5 (indices 0-4) IN, Rank 6-8 (indices 5-7) ELIMINATED
        for ($i = 0; $i < 5; $i++) {
            $this->assertContains($pool['male'][$i]->id, $top5Ids, 'Male Rank '.($i + 1).' must be in Top 5');
            $this->assertContains($pool['female'][$i]->id, $top5Ids, 'Female Rank '.($i + 1).' must be in Top 5');
        }

        for ($i = 5; $i < 8; $i++) {
            $this->assertNotContains($pool['male'][$i]->id, $top5Ids, 'Male Rank '.($i + 1).' must be ELIMINATED');
            $this->assertNotContains($pool['female'][$i]->id, $top5Ids, 'Female Rank '.($i + 1).' must be ELIMINATED');
        }
    }

    /**
     * 3. MISSING JUDGE 6 SCORE TEST
     */
    public function test_missing_judge_6_score_prevents_qualification_until_submitted(): void
    {
        $pool = $this->createIntramuralsCandidates();
        $judges = $this->createJudges(6);

        $testMale = $pool['male'][0];

        // Judges 1 to 5 score all 6 categories
        for ($j = 0; $j < 5; $j++) {
            $this->scoreCandidateCategory($testMale, $judges[$j], 9.5);
        }

        // Judge 6 scores only 5 categories (missing Photogenic)
        ProductionScore::create(['candidate_id' => $testMale->id, 'judge_id' => $judges[5]->id, 'score' => 9.5]);
        FitnessScore::create(['candidate_id' => $testMale->id, 'judge_id' => $judges[5]->id, 'score' => 9.5]);
        IndigenousAttireScore::create(['candidate_id' => $testMale->id, 'judge_id' => $judges[5]->id, 'score' => 9.5]);
        TraditionalAttireScore::create(['candidate_id' => $testMale->id, 'judge_id' => $judges[5]->id, 'score' => 9.5]);
        CustomCategoryScore::create(['candidate_id' => $testMale->id, 'judge_id' => $judges[5]->id, 'category_key' => 'talent_portion', 'score' => 9.5]);

        // Candidate must be INCOMPLETE and NOT in Top 5
        $top5Ids = Candidate::getTop5QualifiedIds();
        $this->assertNotContains($testMale->id, $top5Ids);

        // Now Judge 6 submits the missing Photogenic score
        CustomCategoryScore::create(['candidate_id' => $testMale->id, 'judge_id' => $judges[5]->id, 'category_key' => 'photogenic', 'score' => 9.5]);

        // Candidate is now COMPLETE and qualifies for Top 5
        $top5IdsUpdated = Candidate::getTop5QualifiedIds();
        $this->assertContains($testMale->id, $top5IdsUpdated);
    }

    /**
     * 4. ACTIVE / INACTIVE JUDGE TEST (Judge 6 status toggle)
     */
    public function test_judge_deactivation_and_reactivation_lifecycle(): void
    {
        $pool = $this->createIntramuralsCandidates();
        $judges = $this->createJudges(6);

        $candidate = $pool['male'][0];

        // Judges 1 to 5 score candidate completely
        for ($j = 0; $j < 5; $j++) {
            $this->scoreCandidateCategory($candidate, $judges[$j], 9.5);
        }

        // Candidate has NO scores from Judge 6 yet
        // Since Judge 6 is active, candidate is incomplete
        $this->assertNotContains($candidate->id, Candidate::getTop5QualifiedIds());

        // Deactivate Judge 6 via existing attribute toggle
        $judges[5]->is_active = false;
        $judges[5]->save();

        // Now active judges = 5. Candidate has scores from all 5 active judges -> COMPLETE!
        $this->assertContains($candidate->id, Candidate::getTop5QualifiedIds());

        // Reactivate Judge 6
        $judges[5]->is_active = true;
        $judges[5]->save();

        // Candidate is now INCOMPLETE again until Judge 6 scores
        $this->assertNotContains($candidate->id, Candidate::getTop5QualifiedIds());
    }

    /**
     * 5. ADMIN AND JUDGE Q&A ROUTES RETURN ONLY 10 FINALISTS (5 MALE, 5 FEMALE)
     */
    public function test_admin_and_judge_qa_routes_return_only_10_finalists(): void
    {
        $admin = User::create(['name' => 'Admin', 'username' => 'admin', 'email' => 'admin@test.local', 'password' => bcrypt('p'), 'role' => 'admin', 'is_active' => true]);
        $pool = $this->createIntramuralsCandidates();
        $judges = $this->createJudges(6);

        // Score all 16 candidates
        foreach ($pool['male'] as $idx => $cand) {
            foreach ($judges as $judge) {
                $this->scoreCandidateCategory($cand, $judge, 10.0 - $idx);
            }
        }
        foreach ($pool['female'] as $idx => $cand) {
            foreach ($judges as $judge) {
                $this->scoreCandidateCategory($cand, $judge, 10.0 - $idx);
            }
        }

        // 1. Admin Q&A endpoint
        $responseAdmin = $this->actingAs($admin)->get(route('admin.qa.index'));
        $responseAdmin->assertOk();
        $finalistsAdmin = $responseAdmin->viewData('finalists');
        $finalistIdsAdmin = $finalistsAdmin->pluck('id')->toArray();

        $this->assertCount(10, $finalistIdsAdmin);
        // Exclude eliminated candidates 6, 7, 8 in both divisions
        for ($i = 5; $i < 8; $i++) {
            $this->assertNotContains($pool['male'][$i]->id, $finalistIdsAdmin);
            $this->assertNotContains($pool['female'][$i]->id, $finalistIdsAdmin);
        }

        // 2. Judge Q&A endpoint
        $responseJudge = $this->actingAs($judges[0])->get(route('judge.qa.index'));
        $responseJudge->assertOk();
        $maleJudge = $responseJudge->viewData('maleCandidates');
        $femaleJudge = $responseJudge->viewData('femaleCandidates');

        $this->assertCount(5, $maleJudge->pluck('id'));
        $this->assertCount(5, $femaleJudge->pluck('id'));

        for ($i = 5; $i < 8; $i++) {
            $this->assertNotContains($pool['male'][$i]->id, $maleJudge->pluck('id')->toArray());
            $this->assertNotContains($pool['female'][$i]->id, $femaleJudge->pluck('id')->toArray());
        }
    }

    /**
     * 6. BACKEND PROTECTION: REJECT Q&A SCORES FOR RANKS 6 TO 8
     */
    public function test_backend_protection_rejects_qa_scores_for_ranks_6_to_8(): void
    {
        $admin = User::create(['name' => 'Admin', 'username' => 'admin', 'email' => 'admin@test.local', 'password' => bcrypt('p'), 'role' => 'admin', 'is_active' => true]);
        $pool = $this->createIntramuralsCandidates();
        $judges = $this->createJudges(6);

        foreach ($pool['male'] as $idx => $cand) {
            foreach ($judges as $judge) {
                $this->scoreCandidateCategory($cand, $judge, 10.0 - $idx);
            }
        }
        foreach ($pool['female'] as $idx => $cand) {
            foreach ($judges as $judge) {
                $this->scoreCandidateCategory($cand, $judge, 10.0 - $idx);
            }
        }

        // Candidates at indices 5, 6, 7 are Ranks 6, 7, 8
        foreach ([5, 6, 7] as $eliminatedIdx) {
            $eliminatedMale = $pool['male'][$eliminatedIdx];

            // Attempt via Admin Q&A endpoint
            $resAdmin = $this->actingAs($admin)->postJson(route('admin.qa.save-score'), [
                'candidate_id' => $eliminatedMale->id,
                'judge_id' => $judges[0]->id,
                'score' => 9.5,
            ]);
            $resAdmin->assertStatus(422);

            // Attempt via Judge Q&A endpoint
            $resJudge = $this->actingAs($judges[0])->postJson(route('judge.save-score'), [
                'category' => 'qa',
                'candidate_id' => $eliminatedMale->id,
                'score' => 9.5,
            ]);
            $resJudge->assertStatus(422);
        }
    }

    /**
     * 7. DUPLICATE SUBMISSION PROTECTION
     */
    public function test_duplicate_submission_protection_does_not_double_count(): void
    {
        $pool = $this->createIntramuralsCandidates();
        $judges = $this->createJudges(6);

        $cand = $pool['male'][0];
        $judge = $judges[0];

        // First submission
        ProductionScore::updateOrCreate(['candidate_id' => $cand->id, 'judge_id' => $judge->id], ['score' => 8.5]);
        $this->assertEquals(1, ProductionScore::where('candidate_id', $cand->id)->where('judge_id', $judge->id)->count());

        // Repeated/Accidental double submission with updated score
        ProductionScore::updateOrCreate(['candidate_id' => $cand->id, 'judge_id' => $judge->id], ['score' => 9.0]);
        $this->assertEquals(1, ProductionScore::where('candidate_id', $cand->id)->where('judge_id', $judge->id)->count());

        // Score must be updated, not doubled
        $this->assertEquals(9.0, (float) ProductionScore::where('candidate_id', $cand->id)->where('judge_id', $judge->id)->value('score'));
    }

    /**
     * 8. CONCURRENT JUDGE ISOLATION (Judge 1 score does not replace Judge 2 score)
     */
    public function test_concurrent_judges_scores_remain_completely_isolated(): void
    {
        $pool = $this->createIntramuralsCandidates();
        $judges = $this->createJudges(6);

        $cand = $pool['male'][0];

        // Judge 1 scores
        ProductionScore::updateOrCreate(['candidate_id' => $cand->id, 'judge_id' => $judges[0]->id], ['score' => 8.0]);

        // Judge 2 scores
        ProductionScore::updateOrCreate(['candidate_id' => $cand->id, 'judge_id' => $judges[1]->id], ['score' => 9.5]);

        // Both scores must coexist independently
        $this->assertEquals(2, ProductionScore::where('candidate_id', $cand->id)->count());
        $this->assertEquals(8.0, (float) ProductionScore::where('candidate_id', $cand->id)->where('judge_id', $judges[0]->id)->value('score'));
        $this->assertEquals(9.5, (float) ProductionScore::where('candidate_id', $cand->id)->where('judge_id', $judges[1]->id)->value('score'));
    }

    /**
     * 9. DEPARTMENT INDEPENDENCE (Merit-based Top 5 regardless of department)
     */
    public function test_department_independence_top_5_is_purely_merit_based(): void
    {
        $pool = $this->createIntramuralsCandidates();
        $judges = $this->createJudges(6);

        // All 3 BSIT males score 9.8, 9.7, 9.6
        // 2 BSAB males score 9.5, 9.4
        // 1 BEED male scores 8.0 (eliminated)
        // 1 BSAB male scores 7.5 (eliminated)
        // 1 BEED male scores 7.0 (eliminated)
        $scores = [9.8, 9.7, 9.6, 9.5, 9.4, 8.0, 7.5, 7.0];
        foreach ($pool['male'] as $idx => $cand) {
            foreach ($judges as $judge) {
                $this->scoreCandidateCategory($cand, $judge, $scores[$idx]);
            }
        }
        foreach ($pool['female'] as $idx => $cand) {
            foreach ($judges as $judge) {
                $this->scoreCandidateCategory($cand, $judge, $scores[$idx]);
            }
        }

        $top5Ids = Candidate::getTop5QualifiedIds();

        // Top 5 Male contains 3 BSIT candidates and 2 BSAB candidates
        $this->assertContains($pool['male'][0]->id, $top5Ids); // BSIT
        $this->assertContains($pool['male'][1]->id, $top5Ids); // BSIT
        $this->assertContains($pool['male'][2]->id, $top5Ids); // BSIT
        $this->assertContains($pool['male'][3]->id, $top5Ids); // BSAB
        $this->assertContains($pool['male'][4]->id, $top5Ids); // BSAB

        // BEED candidates did not qualify based on score merit
        $this->assertNotContains($pool['male'][5]->id, $top5Ids); // BEED
        $this->assertNotContains($pool['male'][6]->id, $top5Ids); // BSAB
        $this->assertNotContains($pool['male'][7]->id, $top5Ids); // BEED
    }

    /**
     * 10. Q&A LOAD (60 submissions across 10 finalists & 6 judges) & PRELIMINARY INDEPENDENCE
     */
    public function test_qa_scoring_load_and_independence_from_preliminary(): void
    {
        $pool = $this->createIntramuralsCandidates();
        $judges = $this->createJudges(6);

        foreach ($pool['male'] as $idx => $cand) {
            foreach ($judges as $judge) {
                $this->scoreCandidateCategory($cand, $judge, 10.0 - $idx);
            }
        }
        foreach ($pool['female'] as $idx => $cand) {
            foreach ($judges as $judge) {
                $this->scoreCandidateCategory($cand, $judge, 10.0 - $idx);
            }
        }

        $preliminaryTop5 = Candidate::getTop5QualifiedIds();
        $this->assertCount(10, $preliminaryTop5);

        // 10 finalists * 6 judges = 60 Q&A score records
        foreach ($preliminaryTop5 as $finalistId) {
            foreach ($judges as $judge) {
                QaScore::create([
                    'candidate_id' => $finalistId,
                    'judge_id' => $judge->id,
                    'score' => rand(80, 100) / 10.0,
                ]);
            }
        }

        $this->assertEquals(60, QaScore::count());

        // Re-evaluating Preliminary qualification after Q&A submissions must produce identical result
        $postQaTop5 = Candidate::getTop5QualifiedIds();
        $this->assertEquals($preliminaryTop5, $postQaTop5);
    }
}
