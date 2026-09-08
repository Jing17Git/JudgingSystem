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

class LargeEventScalabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        CriteriaSetting::updateOrCreate(['key' => 'production'], ['name' => 'Production', 'percentage' => 20, 'stage' => 'preliminary', 'sort_order' => 1]);
        CriteriaSetting::updateOrCreate(['key' => 'fitness'], ['name' => 'Fitness', 'percentage' => 20, 'stage' => 'preliminary', 'sort_order' => 2]);
        CriteriaSetting::updateOrCreate(['key' => 'indigenous_attire'], ['name' => 'Indigenous Attire', 'percentage' => 15, 'stage' => 'preliminary', 'sort_order' => 3]);
        CriteriaSetting::updateOrCreate(['key' => 'traditional_attire'], ['name' => 'Traditional Attire', 'percentage' => 15, 'stage' => 'preliminary', 'sort_order' => 4]);
        CriteriaSetting::updateOrCreate(['key' => 'talent_portion'], ['name' => 'Talent Portion', 'percentage' => 15, 'stage' => 'preliminary', 'sort_order' => 5]);
        CriteriaSetting::updateOrCreate(['key' => 'photogenic'], ['name' => 'Photogenic', 'percentage' => 15, 'stage' => 'preliminary', 'sort_order' => 6]);
    }

    /**
     * Helper to bulk-create candidates.
     */
    protected function createCandidates(int $maleCount, int $femaleCount, array $departments): array
    {
        $candidates = [];
        $deptCount = count($departments);

        for ($i = 1; $i <= $maleCount; $i++) {
            $dept = $departments[($i - 1) % $deptCount];
            $candidates[] = Candidate::create([
                'candidate_number' => $i,
                'full_name' => "Male Candidate {$i}",
                'first_name' => 'Male',
                'last_name' => "Cand{$i}",
                'gender' => 'Male',
                'origin' => $dept,
            ]);
        }

        for ($i = 1; $i <= $femaleCount; $i++) {
            $dept = $departments[($i - 1) % $deptCount];
            $candidates[] = Candidate::create([
                'candidate_number' => $i,
                'full_name' => "Female Candidate {$i}",
                'first_name' => 'Female',
                'last_name' => "Cand{$i}",
                'gender' => 'Female',
                'origin' => $dept,
            ]);
        }

        return $candidates;
    }

    /**
     * Helper to bulk-create active judges.
     */
    protected function createJudges(int $count): array
    {
        $judges = [];
        for ($i = 1; $i <= $count; $i++) {
            $judges[] = User::create([
                'name' => "Judge {$i}",
                'username' => "judge_{$i}_".uniqid(),
                'email' => "judge{$i}_".uniqid().'@test.com',
                'password' => bcrypt('password'),
                'role' => 'judge',
                'is_active' => true,
                'judge_number' => $i,
            ]);
        }

        return $judges;
    }

    /**
     * Helper to batch-populate scores for all candidates and judges.
     */
    protected function batchScoreAll(array $candidates, array $judges): void
    {
        $prodData = [];
        $fitData = [];
        $indigData = [];
        $tradData = [];
        $talentData = [];
        $photoData = [];

        $now = now();

        foreach ($candidates as $cand) {
            // Give higher scores to candidate number 1, 2, 3, 4, 5
            $baseScore = max(5.0, 10.0 - ($cand->candidate_number * 0.1));

            foreach ($judges as $judge) {
                $scoreVal = round($baseScore + (($judge->judge_number % 3) * 0.05), 2);
                $scoreVal = min(10.0, max(1.0, $scoreVal));

                $prodData[] = ['candidate_id' => $cand->id, 'judge_id' => $judge->id, 'score' => $scoreVal, 'created_at' => $now, 'updated_at' => $now];
                $fitData[] = ['candidate_id' => $cand->id, 'judge_id' => $judge->id, 'score' => $scoreVal, 'created_at' => $now, 'updated_at' => $now];
                $indigData[] = ['candidate_id' => $cand->id, 'judge_id' => $judge->id, 'score' => $scoreVal, 'created_at' => $now, 'updated_at' => $now];
                $tradData[] = ['candidate_id' => $cand->id, 'judge_id' => $judge->id, 'score' => $scoreVal, 'created_at' => $now, 'updated_at' => $now];
                $talentData[] = ['candidate_id' => $cand->id, 'judge_id' => $judge->id, 'category_key' => 'talent_portion', 'score' => $scoreVal, 'created_at' => $now, 'updated_at' => $now];
                $photoData[] = ['candidate_id' => $cand->id, 'judge_id' => $judge->id, 'category_key' => 'photogenic', 'score' => $scoreVal, 'created_at' => $now, 'updated_at' => $now];
            }
        }

        // Chunked insert for scalability
        foreach (array_chunk($prodData, 500) as $chunk) {
            ProductionScore::insert($chunk);
        }
        foreach (array_chunk($fitData, 500) as $chunk) {
            FitnessScore::insert($chunk);
        }
        foreach (array_chunk($indigData, 500) as $chunk) {
            IndigenousAttireScore::insert($chunk);
        }
        foreach (array_chunk($tradData, 500) as $chunk) {
            TraditionalAttireScore::insert($chunk);
        }
        foreach (array_chunk($talentData, 500) as $chunk) {
            CustomCategoryScore::insert($chunk);
        }
        foreach (array_chunk($photoData, 500) as $chunk) {
            CustomCategoryScore::insert($chunk);
        }
    }

    /**
     * Scenario A: 20 candidates (10 M, 10 F) / 5 judges / 4 departments.
     * Total score records: 20 * 5 * 6 = 600 records.
     */
    public function test_scenario_a_20_candidates_5_judges(): void
    {
        $departments = ['BSIT', 'BSAB', 'BEED', 'BSHM'];
        $candidates = $this->createCandidates(10, 10, $departments);
        $judges = $this->createJudges(5);

        $this->batchScoreAll($candidates, $judges);

        $this->assertEquals(20, Candidate::count());
        $this->assertEquals(5, User::where('role', 'judge')->where('is_active', true)->count());
        $this->assertEquals(600, ProductionScore::count() + FitnessScore::count() + IndigenousAttireScore::count() + TraditionalAttireScore::count() + CustomCategoryScore::count());

        $finalistsData = Candidate::getTopQualifiedFinalists();

        $this->assertCount(5, $finalistsData['male']);
        $this->assertCount(5, $finalistsData['female']);
        $this->assertCount(10, $finalistsData['all']);

        // Candidates 1 to 5 qualify, candidates 6 to 10 do not
        $qualifiedIds = $finalistsData['all']->pluck('id')->toArray();
        foreach ($candidates as $cand) {
            if ($cand->candidate_number <= 5) {
                $this->assertContains($cand->id, $qualifiedIds);
            } else {
                $this->assertNotContains($cand->id, $qualifiedIds);
            }
        }
    }

    /**
     * Scenario B: 40 candidates (20 M, 20 F) / 10 judges / 5 departments.
     * Total score records: 40 * 10 * 6 = 2,400 records.
     */
    public function test_scenario_b_40_candidates_10_judges(): void
    {
        $departments = ['BSIT', 'BSAB', 'BEED', 'BSHM', 'BSBA'];
        $candidates = $this->createCandidates(20, 20, $departments);
        $judges = $this->createJudges(10);

        $this->batchScoreAll($candidates, $judges);

        $this->assertEquals(40, Candidate::count());
        $this->assertEquals(10, User::where('role', 'judge')->where('is_active', true)->count());

        $finalistsData = Candidate::getTopQualifiedFinalists();

        $this->assertCount(5, $finalistsData['male']);
        $this->assertCount(5, $finalistsData['female']);
        $this->assertCount(10, $finalistsData['all']);

        // Verify Admin Q&A route receives exactly the 10 finalists
        $admin = User::create([
            'name' => 'Admin User',
            'username' => 'admin_sc_b',
            'email' => 'admin_b@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.qa.index'));
        $response->assertStatus(200);
        $response->assertViewHas('finalists', function ($finalists) {
            return $finalists->count() === 10;
        });
    }

    /**
     * Scenario C: 60 candidates (30 M, 30 F) / 15 judges / 6 departments.
     * Total score records: 60 * 15 * 6 = 5,400 records.
     * Tests dynamic judge completion requirements.
     */
    public function test_scenario_c_60_candidates_15_judges_completion_demand(): void
    {
        $departments = ['BSIT', 'BSAB', 'BEED', 'BSHM', 'BSBA', 'BSED'];
        $candidates = $this->createCandidates(30, 30, $departments);
        $judges = $this->createJudges(15);

        $this->batchScoreAll($candidates, $judges);

        $this->assertEquals(60, Candidate::count());
        $this->assertEquals(15, User::where('role', 'judge')->where('is_active', true)->count());

        // Top candidate #1 is complete with all 15 judges
        $topCand = $candidates[0];

        // Now remove Judge 15's Photogenic score for Candidate #1
        $judge15 = $judges[14];
        CustomCategoryScore::where('candidate_id', $topCand->id)
            ->where('judge_id', $judge15->id)
            ->where('category_key', 'photogenic')
            ->delete();

        // Candidate #1 is missing 1 of 90 required scores -> must NOT qualify!
        $finalists = Candidate::getTopQualifiedFinalists();
        $this->assertFalse($finalists['all']->contains('id', $topCand->id), 'Candidate with 89/90 scores must be excluded');

        // Now submit the missing score
        CustomCategoryScore::create([
            'candidate_id' => $topCand->id,
            'judge_id' => $judge15->id,
            'category_key' => 'photogenic',
            'score' => 9.9,
        ]);

        // Candidate #1 has 90/90 scores -> immediately qualifies
        $finalistsAfter = Candidate::getTopQualifiedFinalists();
        $this->assertTrue($finalistsAfter['all']->contains('id', $topCand->id), 'Candidate with 90/90 scores must qualify');
    }

    /**
     * Scenario D: 100 candidates (50 M, 50 F) / 20 active judges / 8 departments.
     * Total score records: 100 * 20 * 6 = 12,000 preliminary scores.
     * Stress-test volume, execution time, Top 5 filtering, and backend API protection.
     */
    public function test_scenario_d_100_candidates_20_judges_stress_scale(): void
    {
        $startTime = microtime(true);

        $departments = ['BSIT', 'BSAB', 'BEED', 'BSHM', 'BSBA', 'BSED', 'BSCRIM', 'BSN'];
        $candidates = $this->createCandidates(50, 50, $departments);
        $judges = $this->createJudges(20);

        $this->batchScoreAll($candidates, $judges);

        $totalPreliminaryScores = ProductionScore::count()
            + FitnessScore::count()
            + IndigenousAttireScore::count()
            + TraditionalAttireScore::count()
            + CustomCategoryScore::count();

        $this->assertEquals(12000, $totalPreliminaryScores, 'Expected 12,000 preliminary score combinations');

        // Execute dynamic Top 5 qualification
        $calcStart = microtime(true);
        $finalistsData = Candidate::getTopQualifiedFinalists();
        $calcDuration = microtime(true) - $calcStart;

        // Verify Top 5 boundary
        $this->assertCount(5, $finalistsData['male'], 'Expected exactly 5 Male finalists');
        $this->assertCount(5, $finalistsData['female'], 'Expected exactly 5 Female finalists');
        $this->assertCount(10, $finalistsData['all'], 'Expected exactly 10 total finalists');

        // Check that Male candidate #6 and Female candidate #6 are excluded
        $male6 = Candidate::where('gender', 'Male')->where('candidate_number', 6)->first();
        $female6 = Candidate::where('gender', 'Female')->where('candidate_number', 6)->first();

        $this->assertFalse($finalistsData['all']->contains('id', $male6->id));
        $this->assertFalse($finalistsData['all']->contains('id', $female6->id));

        // Test backend rejection for excluded candidates (Rank 6+)
        $judge1 = $judges[0];
        $response = $this->actingAs($judge1)->postJson(route('judge.save-score'), [
            'category' => 'qa',
            'candidate_id' => $male6->id,
            'score' => 9.0,
        ]);
        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Candidate is not qualified for Q&A (Top 5 only).']);

        // Test duplicate submission protection at scale: re-submitting updates without duplicating
        $existingCount = ProductionScore::count();
        $cand1 = $candidates[0];
        $responseUpdate = $this->actingAs($judge1)->postJson(route('judge.save-score'), [
            'category' => 'production',
            'candidate_id' => $cand1->id,
            'score' => 9.95,
        ]);
        $responseUpdate->assertStatus(200);
        $this->assertEquals($existingCount, ProductionScore::count(), 'Re-scoring must not create duplicate records');
        $this->assertEquals(9.95, (float) ProductionScore::where('candidate_id', $cand1->id)->where('judge_id', $judge1->id)->value('score'));

        $totalDuration = microtime(true) - $startTime;

        // Confirm calculation completed efficiently (less than 2.0s even for 12,000 records)
        $this->assertLessThan(2.0, $calcDuration, "Top 5 calculation took {$calcDuration}s which exceeds threshold");
    }

    /**
     * Test Candidate Management search and filter at scale.
     */
    public function test_candidate_management_search_and_filter_at_scale(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'username' => 'admin_search',
            'email' => 'admin_search@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $departments = ['BSIT', 'BSAB', 'BEED', 'BSHM'];
        $this->createCandidates(20, 20, $departments); // 40 candidates total

        // 1. Search by department
        $resDept = $this->actingAs($admin)->get(route('admin.candidates.index', ['origin' => 'BSIT']));
        $resDept->assertStatus(200);
        $resDept->assertViewHas('candidates', function ($candidates) {
            return $candidates->total() === 10; // 5 Male + 5 Female in BSIT
        });

        // 2. Filter by Gender
        $resGender = $this->actingAs($admin)->get(route('admin.candidates.index', ['gender' => 'Male']));
        $resGender->assertStatus(200);
        $resGender->assertViewHas('candidates', function ($candidates) {
            return $candidates->total() === 20;
        });

        // 3. Search by Candidate Number
        $resNum = $this->actingAs($admin)->get(route('admin.candidates.index', ['search' => '7']));
        $resNum->assertStatus(200);
        $resNum->assertSee('Candidate 7');
    }

    /**
     * Test arbitrary dynamic judge counts (e.g., 3, 7, 12 judges) without code changes.
     */
    public function test_dynamic_judge_counts_lifecycle(): void
    {
        // Start with 3 judges
        $candidates = $this->createCandidates(5, 5, ['BSIT']);
        $judges = $this->createJudges(3);

        $this->batchScoreAll($candidates, $judges);

        $finalists3 = Candidate::getTopQualifiedFinalists();
        $this->assertCount(10, $finalists3['all']);

        // Now add a 4th judge who hasn't scored yet
        $judge4 = User::create([
            'name' => 'Judge 4',
            'username' => 'judge_4_new',
            'email' => 'judge4_new@test.com',
            'password' => bcrypt('password'),
            'role' => 'judge',
            'is_active' => true,
            'judge_number' => 4,
        ]);

        // Immediately, because active judges = 4 and Judge 4 has not scored, all candidates are incomplete!
        $finalists4Pending = Candidate::getTopQualifiedFinalists();
        $this->assertCount(0, $finalists4Pending['all'], 'Candidates must be incomplete when active judge count increased');

        // Deactivating Judge 4 dynamically restores completeness among the 3 active judges
        $judge4->update(['is_active' => false]);
        $finalists4Deactivated = Candidate::getTopQualifiedFinalists();
        $this->assertCount(10, $finalists4Deactivated['all'], 'Deactivated judge must be dynamically ignored');
    }
}
