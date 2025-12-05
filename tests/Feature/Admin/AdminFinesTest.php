<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\GlobalFine;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminFinesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    /**
     * Test non-admin cannot create fine
     */
    public function test_non_admin_cannot_create_fine(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin/fines/create');
        $this->assertEquals(403, $response->status());
    }

    /**
     * Test admin can create fine with valid data
     */
    public function test_admin_can_create_fine(): void
    {
        $fineData = [
            'organisation' => 'Test Corp',
            'regulator' => 'Test Regulator',
            'sector' => 'Technology',
            'region' => 'Europe',
            'fine_amount' => 1000000,
            'currency' => 'EUR',
            'fine_date' => '2025-01-01',
            'law' => 'GDPR',
            'articles_breached' => 'Art. 5',
            'violation_type' => 'Data Breach',
            'summary' => 'This is a test fine for a data breach violation.',
            'badges' => 'gdpr,privacy',
            'link_to_case' => 'https://example.com/case',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/fines', $fineData);

        $response->assertRedirect('/admin/fines');
        $this->assertDatabaseHas('global_fines', [
            'organisation' => 'Test Corp',
            'fine_amount' => 1000000,
        ]);
    }

    /**
     * Test fine amount validation
     */
    public function test_fine_amount_must_be_positive(): void
    {
        $fineData = [
            'organisation' => 'Test Corp',
            'fine_amount' => -1000,
            'currency' => 'EUR',
            'fine_date' => '2025-01-01',
            'summary' => 'This is a test fine for a data breach violation.',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/fines', $fineData);

        $response->assertSessionHasErrors('fine_amount');
    }

    /**
     * Test organisation name is required and min 3 chars
     */
    public function test_organisation_validation(): void
    {
        $fineData = [
            'organisation' => 'AB',  // Too short
            'fine_amount' => 1000,
            'currency' => 'EUR',
            'fine_date' => '2025-01-01',
            'summary' => 'This is a test fine for a data breach violation.',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/fines', $fineData);

        $response->assertSessionHasErrors('organisation');
    }

    /**
     * Test fine date cannot be in future
     */
    public function test_fine_date_cannot_be_future(): void
    {
        $fineData = [
            'organisation' => 'Test Corp',
            'fine_amount' => 1000,
            'currency' => 'EUR',
            'fine_date' => now()->addDays(1)->format('Y-m-d'),
            'summary' => 'This is a test fine for a data breach violation.',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/fines', $fineData);

        $response->assertSessionHasErrors('fine_date');
    }

    /**
     * Test admin can update fine
     */
    public function test_admin_can_update_fine(): void
    {
        $fine = GlobalFine::factory()->create();

        $response = $this->actingAs($this->admin)->put("/admin/fines/{$fine->id}", [
            'organisation' => 'Updated Corp',
            'fine_amount' => 2000000,
            'currency' => 'EUR',
            'fine_date' => '2025-01-01',
            'summary' => 'Updated summary text here.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('global_fines', [
            'id' => $fine->id,
            'organisation' => 'Updated Corp',
        ]);
    }

    /**
     * Test admin can delete fine
     */
    public function test_admin_can_delete_fine(): void
    {
        $fine = GlobalFine::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/admin/fines/{$fine->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('global_fines', ['id' => $fine->id]);
    }

    /**
     * Test non-admin cannot delete fine
     */
    public function test_non_admin_cannot_delete_fine(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $fine = GlobalFine::factory()->create();

        $response = $this->actingAs($user)->delete("/admin/fines/{$fine->id}");

        $this->assertEquals(403, $response->status());
        $this->assertDatabaseHas('global_fines', ['id' => $fine->id]);
    }
}
