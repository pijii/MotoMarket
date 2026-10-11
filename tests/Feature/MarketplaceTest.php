<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Conversation;
use App\Models\Motorcycle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_approved_motorcycles_from_active_sellers_are_public(): void
    {
        $category = $this->category();
        $seller = $this->seller();
        $visible = $this->motorcycle($seller, $category, ['status' => 'approved']);
        $pending = $this->motorcycle($seller, $category, ['model' => 'Pending Ride']);
        $suspendedSeller = $this->seller(['is_active' => false]);
        $inactiveListing = $this->motorcycle($suspendedSeller, $category, ['model' => 'Hidden Ride', 'status' => 'approved']);

        $this->get(route('motorcycles.index'))->assertOk()->assertSee($visible->model)->assertDontSee($pending->model)->assertDontSee($inactiveListing->model);
        $this->get(route('motorcycles.show', $pending))->assertNotFound();
    }

    public function test_catalog_search_filters_and_sorting_only_use_public_listings(): void
    {
        $category = $this->category();
        $seller = $this->seller();
        $match = $this->motorcycle($seller, $category, ['brand' => 'Yamaha', 'model' => 'NMAX', 'price' => 85000, 'condition' => 'used', 'status' => 'approved']);
        $this->motorcycle($seller, $category, ['brand' => 'Honda', 'model' => 'Click', 'price' => 70000, 'status' => 'approved']);

        $this->get(route('motorcycles.index', ['q' => 'NMAX', 'condition' => 'used', 'max_price' => 100000, 'sort' => 'price_asc']))
            ->assertOk()->assertSee($match->model)->assertDontSee('Click');
    }

    public function test_registration_cannot_assign_an_administrator_role(): void
    {
        $this->post(route('register'), [
            'name' => 'Unsafe Role',
            'email' => 'unsafe@example.com',
            'role' => 'admin',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'unsafe@example.com']);
    }

    public function test_registration_can_choose_buyer_or_seller_but_not_admin(): void
    {
        Notification::fake();

        $this->post(route('register'), [
            'name' => 'New Seller',
            'email' => 'seller-choice@example.com',
            'role' => 'seller',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', ['email' => 'seller-choice@example.com', 'role' => 'seller']);
    Notification::assertNothingSent();
    }

    public function test_listing_creation_is_pending_and_sellers_cannot_edit_another_sellers_listing(): void
    {
        $category = $this->category();
        $seller = $this->seller();
        $this->assertSame('seller', $seller->fresh()->role);
        $this->assertTrue($seller->fresh()->is_active);
        $response = $this->actingAs($seller)->post(route('seller.motorcycles.store'), $this->listingData($category));
        $response->assertRedirect(route('seller.motorcycles.index'));
        $created = Motorcycle::firstOrFail();
        $this->assertSame('pending', $created->status);

        $otherSeller = $this->seller();
        $this->actingAs($otherSeller)->put(route('seller.motorcycles.update', $created), $this->listingData($category, ['model' => 'Stolen Edit']))->assertForbidden();
        $this->assertNotSame('Stolen Edit', $created->fresh()->model);
    }

    public function test_listing_creation_rejects_invalid_details(): void
    {
        $seller = $this->seller();

        $this->actingAs($seller)->post(route('seller.motorcycles.store'), [
            'brand' => '',
            'model' => '',
            'year' => 1900,
            'condition' => 'damaged',
            'price' => 0,
            'mileage' => -1,
            'location' => '',
            'description' => 'Too short.',
        ])->assertSessionHasErrors(['category_id', 'brand', 'model', 'year', 'condition', 'price', 'mileage', 'location', 'description']);

        $this->assertDatabaseCount('motorcycles', 0);
    }

    public function test_admin_can_approve_and_reject_listings(): void
    {
        $category = $this->category();
        $seller = $this->seller();
        $pending = $this->motorcycle($seller, $category);
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.motorcycles.approve', $pending))->assertRedirect();
        $this->assertDatabaseHas('motorcycles', ['id' => $pending->id, 'status' => 'approved']);

        $this->post(route('admin.motorcycles.reject', $pending), ['rejection_reason' => 'Please add accurate details.'])->assertRedirect();
        $this->assertDatabaseHas('motorcycles', ['id' => $pending->id, 'status' => 'rejected', 'rejection_reason' => 'Please add accurate details.']);
    }

    public function test_admin_pages_are_restricted_and_reports_can_be_reviewed(): void
    {
        $category = $this->category();
        $seller = $this->seller();
        $motorcycle = $this->motorcycle($seller, $category, ['status' => 'approved']);
        $reporter = User::factory()->create();
        $this->actingAs($reporter)->post(route('reports.store', $motorcycle), [
            'reason' => 'inaccurate',
            'description' => 'The mileage looks incorrect.',
        ])->assertRedirect();
        $reportId = $reporter->reportedListings()->value('id');

        $this->get(route('admin.dashboard'))->assertForbidden();
        $admin = $this->admin();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Marketplace overview');
        $this->get(route('admin.reports.index'))->assertOk()->assertSee('The mileage looks incorrect.');
        $this->patch(route('admin.reports.update', $reportId), ['status' => 'resolved'])->assertRedirect();
        $this->assertDatabaseHas('reports', ['id' => $reportId, 'status' => 'resolved']);
    }

    public function test_admin_can_download_a_filtered_csv_of_all_reports(): void
    {
        $reporter = User::factory()->create();
        for ($index = 0; $index < 22; $index++) {
            $reporter->reportedListings()->create([
                'reason' => 'other',
                'description' => $index === 0 ? '=HYPERLINK("https://example.com")' : "Pending report {$index}",
                'status' => 'pending',
            ]);
        }
        $reporter->reportedListings()->create([
            'reason' => 'other',
            'description' => 'Resolved report should be filtered out',
            'status' => 'resolved',
        ]);

        $this->actingAs($reporter)->get(route('admin.reports.export'))->assertForbidden();

        $response = $this->actingAs($this->admin())->get(route('admin.reports.export', ['status' => 'pending']));
        $response->assertOk()->assertDownload();

        $csv = $response->streamedContent();
        $headerLine = ltrim(explode("\n", $csv)[0], "\xEF\xBB\xBF");
        $header = str_getcsv(trim($headerLine, "\r"));
        $this->assertSame(['Report ID', 'Status', 'Reason', 'Description'], array_slice($header, 0, 4));
        $this->assertStringContainsString("'=HYPERLINK", $csv);
        $this->assertStringNotContainsString('Resolved report should be filtered out', $csv);
        $this->assertCount(23, array_filter(explode("\n", trim($csv))));
    }

    public function test_editing_an_approved_listing_returns_it_to_review(): void
    {
        $category = $this->category();
        $seller = $this->seller();
        $motorcycle = $this->motorcycle($seller, $category, ['status' => 'approved']);

        $this->actingAs($seller)->put(route('seller.motorcycles.update', $motorcycle), $this->listingData($category, ['price' => 130000]))
            ->assertRedirect(route('seller.motorcycles.index'));
        $this->assertDatabaseHas('motorcycles', ['id' => $motorcycle->id, 'price' => 130000, 'status' => 'pending']);
    }

    public function test_favorites_and_conversations_are_database_backed_and_private(): void
    {
        $category = $this->category();
        $seller = $this->seller();
        $motorcycle = $this->motorcycle($seller, $category, ['status' => 'approved']);
        $buyer = User::factory()->create();

        $this->actingAs($buyer)->post(route('favorites.toggle', $motorcycle))->assertRedirect();
        $this->assertDatabaseHas('favorites', ['user_id' => $buyer->id, 'motorcycle_id' => $motorcycle->id]);

        $this->post(route('conversations.start', $motorcycle))->assertRedirect();
        $conversation = Conversation::firstOrFail();
        $this->post(route('conversations.messages.store', $conversation), ['body' => 'Is this motorcycle still available?'])->assertRedirect();
        $this->assertDatabaseHas('messages', ['conversation_id' => $conversation->id, 'sender_id' => $buyer->id]);

        $outsider = User::factory()->create();
        $this->actingAs($outsider)->get(route('conversations.show', $conversation))->assertForbidden();
    }

    public function test_suspended_users_cannot_access_protected_actions_or_log_in(): void
    {
        $suspended = User::factory()->create();
        $suspended->forceFill(['is_active' => false])->save();

        $this->actingAs($suspended)->get(route('dashboard'))->assertForbidden();
        auth()->logout();
        $this->post(route('login'), ['email' => $suspended->email, 'password' => 'password'])->assertSessionHasErrors('email');
    }

    public function test_unverified_users_can_use_private_features_without_email_confirmation(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->get(route('dashboard'))->assertOk();
    }

    public function test_admin_promotion_command_and_account_management_work(): void
    {
        $user = User::factory()->create();
        $this->artisan('users:promote-admin', ['email' => $user->email, '--force' => true])->assertSuccessful();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 'admin', 'is_active' => true]);

        $admin = $this->admin();
        $this->actingAs($admin)->patch(route('admin.users.role', $user), ['role' => 'seller'])->assertRedirect();
        $this->patch(route('admin.users.status', $user), ['is_active' => 0])->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 'seller', 'is_active' => false]);
    }

    private function category(): Category
    {
        return Category::create(['name' => 'Sport', 'slug' => 'sport']);
    }

    private function seller(array $attributes = []): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'seller'] + $attributes)->save();

        return $user->fresh();
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'admin'])->save();

        return $user->fresh();
    }

    private function motorcycle(User $seller, Category $category, array $attributes = []): Motorcycle
    {
        return Motorcycle::create(array_merge([
            'seller_id' => $seller->id,
            'category_id' => $category->id,
            'brand' => 'Honda',
            'model' => 'CB150R',
            'year' => 2023,
            'price' => 125000,
            'condition' => 'used',
            'mileage' => 1200,
            'description' => 'A clean motorcycle with service records and complete papers.',
            'location' => 'Quezon City',
            'status' => 'pending',
        ], $attributes));
    }

    private function listingData(Category $category, array $overrides = []): array
    {
        return array_merge([
            'category_id' => $category->id,
            'brand' => 'Honda',
            'model' => 'CB150R',
            'year' => 2023,
            'condition' => 'used',
            'price' => 125000,
            'mileage' => 1200,
            'location' => 'Quezon City',
            'description' => 'A clean motorcycle with service records and complete papers.',
        ], $overrides);
    }
}