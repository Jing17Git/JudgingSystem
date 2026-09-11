<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CriteriaSetting;
use App\Models\JudgeCategorySubmission;
use App\Models\ProductionScore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JudgeCategoryFinalSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $judge;
    protected Candidate $maleCandidate;
    protected Candidate $femaleCandidate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->judge = User::factory()->create([
            'role' => 'judge',
            'is_active' => true,
            'judge_number' => 1,
            'name' => 'Judge One',
        ]);

        $this->maleCandidate = Candidate::create([
            'candidate_number' => 1,
            'full_name' => 'John Doe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'Male',
            'origin' => 'District A',
        ]);

        $this->femaleCandidate = Candidate::create([
            'candidate_number' => 1,
            'full_name' => 'Jane Smith',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'gender' => 'Female',
            'origin' => 'District B',
        ]);
    }

    public function test_judge_can_view_category_scoring_page_with_overall_table(): void
    {
        $response = $this->actingAs($this->judge)
            ->get(route('judge.production.index'));

        $response->assertStatus(200);
        $response->assertSee('Overall Scoring Table');
        $response->assertSee('Final Category Submission');
        $response->assertSee('Male Contestants');
        $response->assertSee('Female Contestants');
    }

    public function test_judge_can_save_score_via_scoring_endpoint(): void
    {
        $response = $this->actingAs($this->judge)
            ->postJson(route('judge.save-score'), [
                'category' => 'production',
                'candidate_id' => $this->maleCandidate->id,
                'score' => 9.25,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'score' => '9.25',
        ]);

        $this->assertDatabaseHas('production_scores', [
            'candidate_id' => $this->maleCandidate->id,
            'judge_id' => $this->judge->id,
            'score' => 9.25,
        ]);
    }

    public function test_judge_can_finalize_category_submission(): void
    {
        // Score candidates first
        ProductionScore::create([
            'candidate_id' => $this->maleCandidate->id,
            'judge_id' => $this->judge->id,
            'score' => 9.00,
        ]);

        ProductionScore::create([
            'candidate_id' => $this->femaleCandidate->id,
            'judge_id' => $this->judge->id,
            'score' => 8.50,
        ]);

        $response = $this->actingAs($this->judge)
            ->postJson(route('judge.finalize-category'), [
                'category' => 'production',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_finalized' => true,
        ]);

        $this->assertDatabaseHas('judge_category_submissions', [
            'judge_id' => $this->judge->id,
            'category' => 'production',
            'is_finalized' => true,
        ]);

        $this->assertTrue(JudgeCategorySubmission::isFinalized($this->judge->id, 'production'));
    }

    public function test_finalized_category_rejects_further_score_modifications(): void
    {
        // Finalize category
        JudgeCategorySubmission::finalize($this->judge->id, 'production');

        // Attempt save score
        $saveResponse = $this->actingAs($this->judge)
            ->postJson(route('judge.save-score'), [
                'category' => 'production',
                'candidate_id' => $this->maleCandidate->id,
                'score' => 9.50,
            ]);

        $saveResponse->assertStatus(403);
        $saveResponse->assertJson([
            'success' => false,
        ]);

        // Attempt reset score
        $resetResponse = $this->actingAs($this->judge)
            ->postJson(route('judge.reset-score'), [
                'category' => 'production',
                'candidate_id' => $this->maleCandidate->id,
            ]);

        $resetResponse->assertStatus(403);
        $resetResponse->assertJson([
            'success' => false,
        ]);
    }

    public function test_admin_category_reset_clears_judge_submission_lock(): void
    {
        // Finalize category for judge
        JudgeCategorySubmission::finalize($this->judge->id, 'production');
        $this->assertTrue(JudgeCategorySubmission::isFinalized($this->judge->id, 'production'));

        // Admin resets category
        $admin = User::factory()->create(['role' => 'admin']);

        $resetResponse = $this->actingAs($admin)
            ->post(route('admin.categories.reset.single', ['category' => 'production']));

        $resetResponse->assertRedirect(route('admin.categories.reset'));

        // Judge submission lock should be cleared
        $this->assertFalse(JudgeCategorySubmission::isFinalized($this->judge->id, 'production'));

        // Judge can now score again
        $scoreResponse = $this->actingAs($this->judge)
            ->postJson(route('judge.save-score'), [
                'category' => 'production',
                'candidate_id' => $this->maleCandidate->id,
                'score' => 9.75,
            ]);

        $scoreResponse->assertStatus(200);
        $scoreResponse->assertJson(['success' => true]);
    }

    public function test_judge_finalization_disables_category_and_admin_can_re_enable(): void
    {
        $setting = CriteriaSetting::firstOrCreate(
            ['key' => 'production'],
            ['name' => 'Production Number', 'stage' => 'preliminary', 'percentage' => 20, 'sort_order' => 1, 'is_enabled' => true]
        );
        $setting->update(['is_enabled' => true]);

        // Judge finalizes category
        $response = $this->actingAs($this->judge)
            ->postJson(route('judge.finalize-category'), [
                'category' => 'production',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_finalized' => true]);

        // Judge finalization locks the category
        $this->assertFalse((bool) $setting->fresh()->is_enabled,
            'Judge finalization should lock the category.');

        // Judge should be personally locked
        $this->assertTrue(JudgeCategorySubmission::isFinalized($this->judge->id, 'production'),
            'Judge should have a per-judge submission lock after finalization.');

        // Admin unlocks — clears judge submissions so judges can re-score
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->post(route('admin.categories.management.toggle', $setting->id));

        $this->assertTrue((bool) $setting->fresh()->is_enabled,
            'Admin toggle should unlock the category.');

        $this->assertFalse(JudgeCategorySubmission::isFinalized($this->judge->id, 'production'),
            'Admin unlock should clear per-judge submission locks.');

        // Admin can lock the category globally via toggle again at their discretion
        $this->actingAs($admin)
            ->post(route('admin.categories.management.toggle', $setting->id));

        $this->assertFalse((bool) $setting->fresh()->is_enabled,
            'Admin second toggle should lock the category.');
    }

    public function test_fitness_category_admin_lock_unlock_and_judge_flow(): void
    {
        $fitness = CriteriaSetting::firstOrCreate(
            ['key' => 'fitness'],
            ['name' => 'Fitness Attire', 'stage' => 'preliminary', 'percentage' => 10, 'sort_order' => 2, 'is_enabled' => true]
        );
        $fitness->update(['is_enabled' => true]);

        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Admin locks fitness category at their discretion
        $response = $this->actingAs($admin)
            ->post(route('admin.categories.management.toggle', $fitness->id));
        $response->assertRedirect(route('admin.categories.management'));

        $this->assertFalse((bool) $fitness->fresh()->is_enabled);

        // 2. Judge scoring page shows locked banner / disabled status
        $viewResponse = $this->actingAs($this->judge)
            ->get(route('judge.fitness.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertViewHas('isFinalized', true);

        // 3. Judge attempts to submit score while category is locked by admin -> 403
        $saveResponse = $this->actingAs($this->judge)
            ->postJson(route('judge.save-score'), [
                'category' => 'fitness',
                'candidate_id' => $this->maleCandidate->id,
                'score' => 9.0,
            ]);
        $saveResponse->assertStatus(403);

        // 4. Admin unlocks fitness category at their discretion
        $this->actingAs($admin)
            ->post(route('admin.categories.management.toggle', $fitness->id));
        $this->assertTrue((bool) $fitness->fresh()->is_enabled);

        // 5. Judge can now view scoring page as unlocked
        $viewResponse = $this->actingAs($this->judge)
            ->get(route('judge.fitness.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertViewHas('isFinalized', false);

        // 6. Judge submits score successfully
        $saveResponse = $this->actingAs($this->judge)
            ->postJson(route('judge.save-score'), [
                'category' => 'fitness',
                'candidate_id' => $this->maleCandidate->id,
                'score' => 9.0,
            ]);
        $saveResponse->assertStatus(200);

        // 7. Judge finalizes scoring for fitness -> category locks automatically
        $finalizeResponse = $this->actingAs($this->judge)
            ->postJson(route('judge.finalize-category'), [
                'category' => 'fitness',
            ]);
        $finalizeResponse->assertStatus(200);
        $this->assertTrue(JudgeCategorySubmission::isFinalized($this->judge->id, 'fitness'));
        $this->assertFalse((bool) $fitness->fresh()->is_enabled, 'Category should be locked after judge finalization.');

        // 8. Judge cannot edit score after finalization
        $saveResponse = $this->actingAs($this->judge)
            ->postJson(route('judge.save-score'), [
                'category' => 'fitness',
                'candidate_id' => $this->maleCandidate->id,
                'score' => 8.5,
            ]);
        $saveResponse->assertStatus(403);

        // 9. Admin unlocks category at their discretion -> clears judge lock and re-enables
        $this->actingAs($admin)
            ->post(route('admin.categories.management.toggle', $fitness->id));
        $this->assertTrue((bool) $fitness->fresh()->is_enabled, 'Admin unlock should re-enable category.');
        $this->assertFalse(JudgeCategorySubmission::isFinalized($this->judge->id, 'fitness'), 'Admin unlock should clear judge lock.');
    }

    public function test_management_page_renders_with_unlocked_and_locked_categories(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $fitness = CriteriaSetting::firstOrCreate(
            ['key' => 'fitness'],
            ['name' => 'Fitness Attire', 'stage' => 'preliminary', 'percentage' => 10, 'sort_order' => 2, 'is_enabled' => true]
        );

        $response = $this->actingAs($admin)
            ->get(route('admin.categories.management'));

        $response->assertStatus(200);
        $response->assertSee('Preliminary Judging Categories');
        $response->assertSee('Lock Voting');
        $response->assertSee('preliminaryPercentagesForm');
    }
}
