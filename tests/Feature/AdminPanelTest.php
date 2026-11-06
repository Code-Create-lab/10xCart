<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\VisitorEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['admin.dashboard'])]
    #[TestWith(['admin.visitors.index'])]
    #[TestWith(['admin.leads.index'])]
    public function test_guests_are_sent_to_the_login_page(string $route): void
    {
        $this->get(route($route))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_sign_in_and_out(): void
    {
        $user = User::factory()->create(['password' => 'correct-horse-battery']);

        $this->post(route('admin.login.store'), ['email' => $user->email, 'password' => 'correct-horse-battery'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->from(route('admin.login'))
            ->post(route('admin.login.store'), ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors(['email' => 'These details do not match an admin account.']);
        $this->assertGuest();
    }

    public function test_dashboard_shows_traffic_and_lead_figures(): void
    {
        $visitor = Visitor::factory()->create(['city' => 'Nagpur', 'last_seen_at' => now()]);
        $visit = Visit::factory()->for($visitor)->create(['source' => 'Google Ads', 'page_views' => 3, 'started_at' => now()->subHour(), 'last_activity_at' => now()]);
        VisitorEvent::factory()->for($visit)->create(['type' => 'section', 'name' => 'Pricing']);
        Lead::factory()->for($visitor)->create(['name' => 'Priya Mehta']);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.dashboard', ['range' => '30d']))
            ->assertOk()
            ->assertSeeInOrder(['Visitors', '1', 'New visitors'])
            ->assertSeeInOrder(['Page views', '3'])
            ->assertSeeInOrder(['Conversion', '100%'])
            ->assertSee('1 on the site now')
            ->assertSee('Google Ads')
            ->assertSee('Nagpur')
            ->assertSee('Priya Mehta');
    }

    public function test_visitor_page_shows_the_visit_timeline_and_leads(): void
    {
        $visitor = Visitor::factory()->create();
        $visit = Visit::factory()->for($visitor)->create(['utm_campaign' => 'diwali-sale']);
        VisitorEvent::factory()->for($visit)->create(['type' => 'click', 'name' => 'Phone: Call us']);
        Lead::factory()->for($visitor)->create(['name' => 'Priya Mehta']);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.visitors.show', $visitor))
            ->assertOk()
            ->assertSee('diwali-sale')
            ->assertSeeInOrder(['Clicked', 'Phone: Call us'])
            ->assertSee('Priya Mehta');
    }

    public function test_visitor_list_can_be_limited_to_visitors_who_sent_a_lead(): void
    {
        Visitor::factory()->create(['city' => 'Pune']);
        Lead::factory()->for(Visitor::factory()->create(['city' => 'Nagpur']))->create();

        $this->actingAs(User::factory()->create())
            ->get(route('admin.visitors.index', ['leads' => 1]))
            ->assertOk()
            ->assertSee('Nagpur')
            ->assertDontSee('Pune');
    }

    public function test_lead_page_shows_the_enquiry_and_its_context(): void
    {
        $lead = Lead::factory()->create([
            'name' => 'Priya Mehta',
            'message' => 'Need a delivery app.',
            'location' => ['status' => 'ok', 'city' => 'Nagpur', 'country' => 'India'],
            'context' => ['last_touch' => ['source' => 'facebook / cpc']],
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.leads.show', $lead))
            ->assertOk()
            ->assertSee('Priya Mehta')
            ->assertSee('Need a delivery app.')
            ->assertSee('facebook / cpc')
            ->assertSee('Nagpur')
            ->assertSee('did not accept analytics cookies');
    }

    public function test_lead_search_matches_phone_numbers(): void
    {
        Lead::factory()->create(['name' => 'Priya Mehta', 'phone' => '9123456789']);
        Lead::factory()->create(['name' => 'Rahul Shah', 'phone' => '9988776655']);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.leads.index', ['q' => '91234']))
            ->assertOk()
            ->assertSee('Priya Mehta')
            ->assertDontSee('Rahul Shah');
    }

    public function test_create_admin_command_creates_a_login(): void
    {
        $this->artisan('admin:create', ['email' => 'owner@example.com'])
            ->expectsQuestion('Password (at least 10 characters)', 'a-long-password')
            ->assertSuccessful();

        $this->assertTrue(Hash::check('a-long-password', User::where('email', 'owner@example.com')->value('password')));
    }

    public function test_create_admin_command_rejects_a_short_password(): void
    {
        $this->artisan('admin:create', ['email' => 'owner@example.com'])
            ->expectsQuestion('Password (at least 10 characters)', 'short')
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }
}
