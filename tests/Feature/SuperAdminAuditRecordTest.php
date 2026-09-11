<?php

namespace Tests\Feature;

use App\Models\AuditRecord;
use App\Models\Candidate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminAuditRecordTest extends TestCase
{
    use RefreshDatabase;

    /** Helper: create an admin user. */
    private function makeAdmin(string $suffix = ''): User
    {
        return User::create([
            'name' => 'Admin'.$suffix,
            'username' => 'admin'.$suffix,
            'email' => 'admin'.$suffix.'@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    /** Helper: create a minimal candidate to satisfy the FK constraint. */
    private function makeCandidate(): Candidate
    {
        return Candidate::create([
            'candidate_number' => 1,
            'first_name' => 'Jane',
            'last_name' => 'Contestant',
            'gender' => 'Female',
        ]);
    }

    // -----------------------------------------------------------------------
    // TEST 1: Admin can access the audit record page and see entries
    // -----------------------------------------------------------------------
    public function test_admin_can_access_audit_record_page(): void
    {
        $admin = $this->makeAdmin('_page');
        $candidate = $this->makeCandidate();

        AuditRecord::create([
            'event_type' => 'score_submitted',
            'category' => 'qa',
            'user_id' => $admin->id,
            'user_name' => 'Judge John',
            'user_role' => 'judge',
            'candidate_id' => $candidate->id,
            'candidate_name' => 'Jane Contestant',
            'candidate_number' => 1,
            'new_score' => 9.5,
            'action_description' => 'Judge John submitted score 9.50 for Candidate #1 in QA',
            'details' => json_encode(['score' => 9.5, 'category' => 'qa', 'action' => 'saved']),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.settings.audit_record'));

        $response->assertStatus(200);
        $response->assertSee('Audit Management', false);
        $response->assertSee('Jane Contestant');
    }

    // -----------------------------------------------------------------------
    // TEST 2: Judge is blocked from the admin audit page
    // -----------------------------------------------------------------------
    public function test_judge_cannot_access_admin_audit_record_page(): void
    {
        $judge = User::create([
            'name' => 'Regular Judge',
            'username' => 'regular_judge',
            'email' => 'judge_regular@test.com',
            'password' => bcrypt('password'),
            'role' => 'judge',
        ]);

        $response = $this->actingAs($judge)->get(route('admin.settings.audit_record'));

        $this->assertTrue(
            in_array($response->getStatusCode(), [302, 403]),
            'Expected 302 or 403 but got '.$response->getStatusCode()
        );
    }

    // -----------------------------------------------------------------------
    // TEST 3: CSV export returns a valid streaming download
    // -----------------------------------------------------------------------
    public function test_admin_can_export_audit_records_csv(): void
    {
        $admin = $this->makeAdmin('_csv');

        $response = $this->actingAs($admin)->get(route('admin.settings.audit_record.export'));

        $response->assertStatus(200);

        $contentType = $response->headers->get('content-type');
        $this->assertStringStartsWith('text/csv', $contentType,
            "Expected content-type to start with 'text/csv', got: {$contentType}"
        );
    }

    // -----------------------------------------------------------------------
    // TEST 4: Audit record stores all key fields correctly
    // -----------------------------------------------------------------------
    public function test_audit_record_stores_all_fields_correctly(): void
    {
        $admin = $this->makeAdmin('_fields');
        $candidate = $this->makeCandidate();

        $record = AuditRecord::create([
            'event_type' => 'score_reset',
            'category' => 'fitness',
            'user_id' => $admin->id,
            'user_name' => 'Judge Maria',
            'user_role' => 'judge',
            'candidate_id' => $candidate->id,
            'candidate_name' => 'Jane Contestant',
            'candidate_number' => 1,
            'old_score' => 8.0,
            'new_score' => null,
            'action_description' => 'Score reset by judge',
            'details' => json_encode(['action' => 'reset']),
        ]);

        $this->assertDatabaseHas('audit_records', [
            'event_type' => 'score_reset',
            'category' => 'fitness',
            'user_name' => 'Judge Maria',
            'old_score' => 8.0,
            'candidate_id' => $candidate->id,
        ]);

        $this->assertEquals('score_reset', $record->event_type);
        $this->assertEquals(8.0, $record->old_score);
    }

    // -----------------------------------------------------------------------
    // TEST 5: Anomaly detection flags excessive resets as critical
    // -----------------------------------------------------------------------
    public function test_anomaly_detection_flags_excessive_resets_as_critical(): void
    {
        $judge = $this->makeAdmin('_anomaly');
        $candidate = $this->makeCandidate();

        // Insert 3 prior resets so the next evaluation triggers the critical rule (>= 3)
        for ($i = 0; $i < 3; $i++) {
            AuditRecord::create([
                'event_type' => 'score_reset',
                'category' => 'fitness',
                'user_id' => $judge->id,
                'user_name' => $judge->name,
                'user_role' => 'judge',
                'candidate_id' => $candidate->id,
                'candidate_name' => $candidate->name,
                'candidate_number' => $candidate->candidate_number,
                'action_description' => 'Reset #'.($i + 1),
            ]);
        }

        $result = AuditRecord::evaluateAnomaly(
            'score_reset',
            'fitness',
            $judge->id,
            $candidate->id,
            8.0,
            null
        );

        $this->assertEquals('critical', $result['risk_level']);
        $this->assertTrue($result['is_suspicious']);
        $this->assertStringContainsString('Excessive Score Manipulation', $result['suspicious_reason']);
    }

    // -----------------------------------------------------------------------
    // TEST 6: Search filter returns matching audit records
    // -----------------------------------------------------------------------
    public function test_admin_can_search_audit_records(): void
    {
        $admin = $this->makeAdmin('_search');
        $candidate = $this->makeCandidate();

        AuditRecord::create([
            'event_type' => 'score_submitted',
            'category' => 'talent_portion',
            'user_id' => $admin->id,
            'user_name' => 'Judge SearchTest',
            'user_role' => 'judge',
            'candidate_id' => $candidate->id,
            'candidate_name' => 'Jane Contestant',
            'candidate_number' => 1,
            'new_score' => 7.5,
            'action_description' => 'Unique-SearchPhrase-XYZ submitted score',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.settings.audit_record', ['search' => 'Unique-SearchPhrase-XYZ']));

        $response->assertStatus(200);
        $response->assertSee('Unique-SearchPhrase-XYZ');
    }
}
