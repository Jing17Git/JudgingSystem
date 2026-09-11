<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CustomCategoryScore;
use App\Models\FitnessScore;
use App\Models\IndigenousAttireScore;
use App\Models\ProductionScore;
use App\Models\QaScore;
use App\Models\TraditionalAttireScore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CategoryResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin_'.Str::random(6),
            'email' => 'superadmin_'.Str::random(6).'@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);
        $this->actingAs($this->admin);

        $judge = User::create([
            'name' => 'Judge 1',
            'username' => 'judge_'.Str::random(6),
            'email' => 'judge_'.Str::random(6).'@example.com',
            'password' => bcrypt('password'),
            'role' => 'judge',
            'is_active' => true,
        ]);

        $c1 = Candidate::create([
            'candidate_number' => 1,
            'full_name' => 'Candidate 1',
            'first_name' => 'Candidate',
            'last_name' => '1',
            'gender' => 'Female',
        ]);
        $c2 = Candidate::create([
            'candidate_number' => 2,
            'full_name' => 'Candidate 2',
            'first_name' => 'Candidate',
            'last_name' => '2',
            'gender' => 'Male',
        ]);

        ProductionScore::create(['candidate_id' => $c1->id, 'judge_id' => $judge->id, 'score' => 9.0]);
        ProductionScore::create(['candidate_id' => $c2->id, 'judge_id' => $judge->id, 'score' => 8.5]);
        FitnessScore::create(['candidate_id' => $c1->id, 'judge_id' => $judge->id, 'score' => 9.2]);
        TraditionalAttireScore::create(['candidate_id' => $c1->id, 'judge_id' => $judge->id, 'score' => 8.8]);
        IndigenousAttireScore::create(['candidate_id' => $c1->id, 'judge_id' => $judge->id, 'score' => 9.1]);
        QaScore::create(['candidate_id' => $c1->id, 'judge_id' => $judge->id, 'score' => 9.5]);
    }

    /** @test */
    public function reset_page_loads_successfully()
    {
        $response = $this->get(route('admin.categories.reset'));
        $response->assertStatus(200);
        $response->assertSee('Score Data Reset Center');
    }

    /** @test */
    public function can_reset_single_category()
    {
        $this->assertEquals(2, ProductionScore::count());
        $response = $this->post(route('admin.categories.reset.single', ['category' => 'production']));
        $response->assertRedirect(route('admin.categories.reset'));
        $this->assertEquals(0, ProductionScore::count());
    }

    /** @test */
    public function can_reset_all_categories()
    {
        $totalBefore = ProductionScore::count() + FitnessScore::count() + TraditionalAttireScore::count() + IndigenousAttireScore::count() + QaScore::count() + CustomCategoryScore::count();
        $this->assertTrue($totalBefore > 0);
        $response = $this->post(route('admin.categories.reset.confirm'), [
            'scope' => 'all',
            'confirmation' => 'RESET ALL DATA',
        ]);
        $response->assertRedirect(route('admin.categories.reset'));
        $this->assertEquals(0, ProductionScore::count());
        $this->assertEquals(0, FitnessScore::count());
        $this->assertEquals(0, TraditionalAttireScore::count());
        $this->assertEquals(0, IndigenousAttireScore::count());
        $this->assertEquals(0, QaScore::count());
        $this->assertEquals(0, CustomCategoryScore::count());
    }

    /** @test */
    public function can_reset_candidates_manually()
    {
        $this->assertTrue(Candidate::count() > 0);
        $response = $this->post(route('admin.categories.reset.single', ['category' => 'candidates']));
        $response->assertRedirect(route('admin.categories.reset'));
        $this->assertEquals(0, Candidate::count());
        $this->assertEquals(0, ProductionScore::count());
    }

    /** @test */
    public function can_reset_judges_manually()
    {
        $this->assertTrue(User::where('role', 'judge')->count() > 0);
        $response = $this->post(route('admin.categories.reset.single', ['category' => 'judges']));
        $response->assertRedirect(route('admin.categories.reset'));
        $this->assertEquals(0, User::where('role', 'judge')->count());
        $this->assertEquals(0, ProductionScore::count());
        // Admin account must still exist
        $this->assertTrue(User::where('role', 'admin')->count() > 0);
    }
}
