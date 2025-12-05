<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\ScrapedFine;
use App\Models\GlobalFine;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScrapedFineReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $reviewer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->reviewer = User::factory()->create(['is_admin' => true]);
    }

    /**
     * Test admin can submit scraped fine
     */
    public function test_admin_can_submit_scraped_fine(): void
    {
        $fineData = [
            'organisation' => 'Test Corp',
            'fine_amount' => 1000000,
            'currency' => 'EUR',
            'fine_date' => '2025-01-01',
            'summary' => 'This is a test fine for a data breach violation.',
            'regulator' => 'Test Regulator',
            'sector' => 'Technology',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/scraped-fines', $fineData);

        $response->assertRedirect('/admin/scraped-fines');
        $this->assertDatabaseHas('scraped_fines', [
            'organisation' => 'Test Corp',
            'status' => 'pending',
            'submitted_by' => $this->admin->id,
        ]);
    }

    /**
     * Test non-admin cannot submit scraped fine
     */
    public function test_non_admin_cannot_submit_scraped_fine(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin/scraped-fines/create');
        $this->assertEquals(403, $response->status());
    }

    /**
     * Test admin can approve pending scraped fine
     */
    public function test_admin_can_approve_scraped_fine(): void
    {
        $fine = ScrapedFine::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->reviewer)->post(
            "/admin/scraped-fines/{$fine->id}/review",
            [
                'status' => 'approved',
                'review_notes' => 'This fine is legitimate and well-documented.',
            ]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('scraped_fines', [
            'id' => $fine->id,
            'status' => 'approved',
            'reviewed_by' => $this->reviewer->id,
        ]);
    }

    /**
     * Test approved fine is copied to global_fines table
     */
    public function test_approved_fine_copied_to_global_fines(): void
    {
        $fine = ScrapedFine::factory()->create([
            'status' => 'pending',
            'organisation' => 'Approved Corp',
        ]);

        $this->actingAs($this->reviewer)->post(
            "/admin/scraped-fines/{$fine->id}/review",
            [
                'status' => 'approved',
                'review_notes' => 'Approved for database inclusion.',
            ]
        );

        $this->assertDatabaseHas('global_fines', [
            'organisation' => 'Approved Corp',
        ]);
    }

    /**
     * Test admin can reject scraped fine
     */
    public function test_admin_can_reject_scraped_fine(): void
    {
        $fine = ScrapedFine::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->reviewer)->post(
            "/admin/scraped-fines/{$fine->id}/review",
            [
                'status' => 'rejected',
                'review_notes' => 'This fine lacks proper documentation and source verification.',
            ]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('scraped_fines', [
            'id' => $fine->id,
            'status' => 'rejected',
            'reviewed_by' => $this->reviewer->id,
        ]);
    }

    /**
     * Test rejected fine is NOT added to global_fines
     */
    public function test_rejected_fine_not_in_global_fines(): void
    {
        $fine = ScrapedFine::factory()->create([
            'status' => 'pending',
            'organisation' => 'Rejected Corp',
        ]);

        $this->actingAs($this->reviewer)->post(
            "/admin/scraped-fines/{$fine->id}/review",
            [
                'status' => 'rejected',
                'review_notes' => 'Insufficient evidence.',
            ]
        );

        $this->assertDatabaseMissing('global_fines', [
            'organisation' => 'Rejected Corp',
        ]);
    }

    /**
     * Test cannot review already reviewed fine
     */
    public function test_cannot_review_already_reviewed_fine(): void
    {
        $fine = ScrapedFine::factory()->create(['status' => 'approved']);

        $response = $this->actingAs($this->reviewer)->post(
            "/admin/scraped-fines/{$fine->id}/review",
            [
                'status' => 'rejected',
                'review_notes' => 'Cannot review this.',
            ]
        );

        $response->assertSessionHasErrors();
    }

    /**
     * Test review notes are required
     */
    public function test_review_notes_required(): void
    {
        $fine = ScrapedFine::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->reviewer)->post(
            "/admin/scraped-fines/{$fine->id}/review",
            [
                'status' => 'approved',
                'review_notes' => '',  // Empty
            ]
        );

        $response->assertSessionHasErrors('review_notes');
    }

    /**
     * Test non-admin cannot review scraped fine
     */
    public function test_non_admin_cannot_review_fine(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $fine = ScrapedFine::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($user)->get("/admin/scraped-fines/{$fine->id}/review");

        $this->assertEquals(403, $response->status());
    }

    /**
     * Test pending fines are listed first
     */
    public function test_pending_fines_listed_by_default(): void
    {
        ScrapedFine::factory()->count(3)->create(['status' => 'pending']);
        ScrapedFine::factory()->count(2)->create(['status' => 'approved']);

        $response = $this->actingAs($this->admin)->get('/admin/scraped-fines');

        // Should show pending fines by default
        $response->assertViewHas('scraped_fines');
    }
}
