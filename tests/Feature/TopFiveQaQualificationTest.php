<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CriteriaSetting;
use App\Models\FitnessScore;
use App\Models\IndigenousAttireScore;
use App\Models\ProductionScore;
use App\Models\QaScore;
use App\Models\TraditionalAttireScore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopFiveQaQualificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        CriteriaSetting::updateOrCreate(['key' => 'production'], ['name' => 'Production', 'stage' => 'preliminary', 'percentage' => 25.0, 'sort_order' => 1]);
        CriteriaSetting::updateOrCreate(['key' => 'fitness'], ['name' => 'Fitness', 'stage' => 'preliminary', 'percentage' => 25.0, 'sort_order' => 2]);
        CriteriaSetting::updateOrCreate(['key' => 'indigenous_attire'], ['name' => 'Indigenous Attire', 'stage' => 'preliminary', 'percentage' => 25.0, 'sort_order' => 3]);
        CriteriaSetting::updateOrCreate(['key' => 'traditional_attire'], ['name' => 'Traditional Attire', 'stage' => 'preliminary', 'percentage' => 25.0, 'sort_order' => 4]);
    }

    /**
     * Helper to fully score a candidate across all 4 built-in preliminary categories.
     */
    private function scoreCandidate(Candidate $candidate, User $judge, float $score): void
    {
        ProductionScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => $score]);
        FitnessScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => $score]);
        IndigenousAttireScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => $score]);
        TraditionalAttireScore::create(['candidate_id' => $candidate->id, 'judge_id' => $judge->id, 'score' => $score]);
    }

    /**
     * TEST 6 — Top 5 Boundary
     */
    public function test_top_5_boundary_ranks_1_to_5_qualified_and_rank_6_excluded(): void
    {
        $judge = User::create(['name' => 'J1', 'username' => 'j1', 'email' => 'j1@example.com', 'password' => bcrypt('p'), 'role' => 'judge', 'is_active' => true]);

        $candidates = [];
        $scores = [10.0, 9.0, 8.0, 7.0, 6.0, 5.0];

        foreach ($scores as $idx => $score) {
            $c = Candidate::create(['candidate_number' => $idx + 1, 'full_name' => "Cand {$idx}", 'first_name' => 'Cand', 'last_name' => (string) $idx, 'gender' => 'Female']);
            $this->scoreCandidate($c, $judge, $score);
            $candidates[] = $c;
        }

        $top5Ids = Candidate::getTop5QualifiedIds();

        // Ranks 1 to 5 (indices 0 to 4) are qualified
        for ($i = 0; $i < 5; $i++) {
            $this->assertContains($candidates[$i]->id, $top5Ids);
        }

        // Rank 6 (index 5) is EXCLUDED
        $this->assertNotContains($candidates[5]->id, $top5Ids);
    }

    /**
     * TEST 7 — Q&A Candidate Fetch (Admin & Judge Endpoints)
     */
    public function test_admin_and_judge_qa_routes_fetch_only_top_5_candidates(): void
    {
        $admin = User::create(['name' => 'Admin', 'username' => 'admin', 'email' => 'admin@example.com', 'password' => bcrypt('p'), 'role' => 'admin', 'is_active' => true]);
        $judge = User::create(['name' => 'Judge 1', 'username' => 'j1', 'email' => 'j1@example.com', 'password' => bcrypt('p'), 'role' => 'judge', 'is_active' => true]);

        $candidates = [];
        for ($i = 1; $i <= 6; $i++) {
            $c = Candidate::create(['candidate_number' => $i, 'full_name' => "Female Cand {$i}", 'first_name' => 'Female', 'last_name' => "Cand {$i}", 'gender' => 'Female']);
            $this->scoreCandidate($c, $judge, 10.0 - $i);
            $candidates[] = $c;
        }

        // 1. Admin Q&A endpoint
        $responseAdmin = $this->actingAs($admin)->get(route('admin.qa.index'));
        $responseAdmin->assertOk();
        $finalistsAdmin = $responseAdmin->viewData('finalists');
        $finalistIdsAdmin = $finalistsAdmin->pluck('id')->toArray();

        $this->assertCount(5, $finalistIdsAdmin);
        $this->assertNotContains($candidates[5]->id, $finalistIdsAdmin); // Rank 6 excluded

        // 2. Judge Q&A endpoint
        $responseJudge = $this->actingAs($judge)->get(route('judge.qa.index'));
        $responseJudge->assertOk();
        $femaleJudge = $responseJudge->viewData('femaleCandidates');
        $femaleJudgeIds = $femaleJudge->pluck('id')->toArray();

        $this->assertCount(5, $femaleJudgeIds);
        $this->assertNotContains($candidates[5]->id, $femaleJudgeIds); // Rank 6 excluded
    }

    /**
     * TEST 8 — Ranking Changes After Score Update
     */
    public function test_ranking_changes_dynamically_update_top_5_qualification(): void
    {
        $judge = User::create(['name' => 'J1', 'username' => 'j1', 'email' => 'j1@example.com', 'password' => bcrypt('p'), 'role' => 'judge', 'is_active' => true]);

        // Create 6 candidates: Cand 5 has Rank 5 (score 6.0), Cand 6 has Rank 6 (score 5.0)
        $candidates = [];
        $scores = [10.0, 9.0, 8.0, 7.0, 6.0, 5.0];

        foreach ($scores as $idx => $score) {
            $c = Candidate::create(['candidate_number' => $idx + 1, 'full_name' => "Cand {$idx}", 'first_name' => 'Cand', 'last_name' => (string) $idx, 'gender' => 'Female']);
            $this->scoreCandidate($c, $judge, $score);
            $candidates[] = $c;
        }

        $top5Initial = Candidate::getTop5QualifiedIds();
        $this->assertContains($candidates[4]->id, $top5Initial);     // Candidate A (Rank 5) IN
        $this->assertNotContains($candidates[5]->id, $top5Initial);  // Candidate B (Rank 6) OUT

        // Update scores so Candidate B (cand 5) gets score 9.5 (moves to Rank 2), pushing Candidate A to Rank 6
        ProductionScore::where('candidate_id', $candidates[5]->id)->update(['score' => 9.5]);
        FitnessScore::where('candidate_id', $candidates[5]->id)->update(['score' => 9.5]);
        IndigenousAttireScore::where('candidate_id', $candidates[5]->id)->update(['score' => 9.5]);
        TraditionalAttireScore::where('candidate_id', $candidates[5]->id)->update(['score' => 9.5]);

        $top5Updated = Candidate::getTop5QualifiedIds();
        $this->assertContains($candidates[5]->id, $top5Updated);     // Candidate B IN
        $this->assertNotContains($candidates[4]->id, $top5Updated);  // Candidate A OUT
    }

    /**
     * TEST 9 — Male/Female Divisions Separated
     */
    public function test_male_and_female_rankings_and_top_5_are_separated_by_gender_division(): void
    {
        $judge = User::create(['name' => 'J1', 'username' => 'j1', 'email' => 'j1@example.com', 'password' => bcrypt('p'), 'role' => 'judge', 'is_active' => true]);

        $males = [];
        $females = [];

        for ($i = 1; $i <= 6; $i++) {
            $m = Candidate::create(['candidate_number' => $i, 'full_name' => "Male Cand {$i}", 'first_name' => 'Male', 'last_name' => "Cand {$i}", 'gender' => 'Male']);
            $f = Candidate::create(['candidate_number' => $i + 10, 'full_name' => "Female Cand {$i}", 'first_name' => 'Female', 'last_name' => "Cand {$i}", 'gender' => 'Female']);
            $this->scoreCandidate($m, $judge, 10.0 - $i);
            $this->scoreCandidate($f, $judge, 10.0 - $i);
            $males[] = $m;
            $females[] = $f;
        }

        $top5Ids = Candidate::getTop5QualifiedIds();

        // 5 Males + 5 Females = 10 total qualified candidates
        $this->assertCount(10, $top5Ids);

        // Male Rank 1-5 IN, Male Rank 6 OUT
        for ($i = 0; $i < 5; $i++) {
            $this->assertContains($males[$i]->id, $top5Ids);
            $this->assertContains($females[$i]->id, $top5Ids);
        }
        $this->assertNotContains($males[5]->id, $top5Ids);
        $this->assertNotContains($females[5]->id, $top5Ids);
    }

    /**
     * TEST 11 — Q&A Must Not Influence Preliminary Qualification
     */
    public function test_qa_scores_do_not_influence_preliminary_top_5_qualification(): void
    {
        $judge = User::create(['name' => 'J1', 'username' => 'j1', 'email' => 'j1@example.com', 'password' => bcrypt('p'), 'role' => 'judge', 'is_active' => true]);

        $candidates = [];
        $scores = [10.0, 9.0, 8.0, 7.0, 6.0, 5.0];

        foreach ($scores as $idx => $score) {
            $c = Candidate::create(['candidate_number' => $idx + 1, 'full_name' => "Cand {$idx}", 'first_name' => 'Cand', 'last_name' => (string) $idx, 'gender' => 'Female']);
            $this->scoreCandidate($c, $judge, $score);
            $candidates[] = $c;
        }

        // Add massive Q&A score (10.0) to Rank 6 candidate (index 5)
        QaScore::create(['candidate_id' => $candidates[5]->id, 'judge_id' => $judge->id, 'score' => 10.0]);

        $top5Ids = Candidate::getTop5QualifiedIds();

        // Q&A score MUST NOT alter preliminary Top 5 qualification
        $this->assertNotContains($candidates[5]->id, $top5Ids);
    }

    /**
     * TEST 12 — Backend Protection (Rejecting Q&A submission for Rank 6)
     */
    public function test_backend_rejects_qa_score_submission_for_non_top_5_candidate(): void
    {
        $admin = User::create(['name' => 'Admin', 'username' => 'admin', 'email' => 'admin@example.com', 'password' => bcrypt('p'), 'role' => 'admin', 'is_active' => true]);
        $judge = User::create(['name' => 'J1', 'username' => 'j1', 'email' => 'j1@example.com', 'password' => bcrypt('p'), 'role' => 'judge', 'is_active' => true]);

        $candidates = [];
        $scores = [10.0, 9.0, 8.0, 7.0, 6.0, 5.0];

        foreach ($scores as $idx => $score) {
            $c = Candidate::create(['candidate_number' => $idx + 1, 'full_name' => "Cand {$idx}", 'first_name' => 'Cand', 'last_name' => (string) $idx, 'gender' => 'Female']);
            $this->scoreCandidate($c, $judge, $score);
            $candidates[] = $c;
        }

        $rank6Candidate = $candidates[5];

        // Attempting to submit Q&A score for Rank 6 candidate via Admin endpoint
        $responseAdmin = $this->actingAs($admin)->postJson(route('admin.qa.save-score'), [
            'candidate_id' => $rank6Candidate->id,
            'judge_id' => $judge->id,
            'score' => 9.5,
        ]);

        $responseAdmin->assertStatus(422);
        $responseAdmin->assertJsonFragment(['message' => 'Candidate is not qualified for Q&A (Top 5 only).']);

        // Attempting to submit Q&A score for Rank 6 candidate via Judge endpoint
        $responseJudge = $this->actingAs($judge)->postJson(route('judge.save-score'), [
            'category' => 'qa',
            'candidate_id' => $rank6Candidate->id,
            'score' => 9.5,
        ]);

        $responseJudge->assertStatus(422);
        $responseJudge->assertJsonFragment(['message' => 'Candidate is not qualified for Q&A (Top 5 only).']);
    }
}
