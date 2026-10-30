<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardModulesTest extends TestCase
{
    use RefreshDatabase;

    private const CSRF_TOKEN = 'dashboard-test-token';

    protected function setUp(): void
    {
        parent::setUp();
        config(['admin.email' => 'admin@example.com']);
    }

    public function test_admin_can_open_each_dashboard_module(): void
    {
        $this->actingAs(User::factory()->create(['email' => 'admin@example.com']));

        $this->get(route('dashboard'))->assertOk()->assertSeeText('Welcome Back');

        foreach (['pastor', 'deacon', 'elder', 'department-leader', 'treasurer'] as $role) {
            $this->get(route('dashboard.'.$role))->assertOk();
        }

        foreach (['events', 'announcements', 'duties', 'meetings', 'departments', 'reports', 'finance', 'users'] as $module) {
            $response = $this->get(route('dashboard.resource.index', $module));
            self::assertSame(200, $response->status(), $module.' returned '.$response->status().': '.substr(strip_tags($response->getContent()), 0, 250));
        }

        $this->get(route('dashboard.profile'))->assertOk();
        $this->get(route('dashboard.notifications'))->assertOk();
    }

    public function test_admin_can_create_update_and_delete_a_church_event(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $this->actingAs($admin);

        $this->withSession(['_token' => self::CSRF_TOKEN])->post(route('dashboard.resource.store', 'events'), [
            '_token' => self::CSRF_TOKEN,
            'title' => 'Sunday Service',
            'start_date' => '2026-11-01T09:00',
            'status' => 'scheduled',
        ])->assertRedirect();

        $event = \App\Models\Event::where('title', 'Sunday Service')->firstOrFail();
        $this->assertSame($admin->id, $event->created_by);

        $this->withSession(['_token' => self::CSRF_TOKEN])->put(route('dashboard.resource.update', ['events', $event->id]), [
            '_token' => self::CSRF_TOKEN,
            'title' => 'Sunday Worship',
            'start_date' => '2026-11-01T09:00',
            'status' => 'scheduled',
        ])->assertRedirect();
        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'Sunday Worship']);

        $this->withSession(['_token' => self::CSRF_TOKEN])->delete(route('dashboard.resource.destroy', ['events', $event->id]), [
            '_token' => self::CSRF_TOKEN,
        ])->assertRedirect();
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_new_announcement_notifies_the_other_users(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $recipient = User::factory()->create();
        $this->actingAs($admin);

        $this->withSession(['_token' => self::CSRF_TOKEN])->post(route('dashboard.resource.store', 'announcements'), [
            '_token' => self::CSRF_TOKEN,
            'title' => 'Church gathering',
            'message' => 'Join us this Sunday.',
        ])->assertRedirect();

        $this->assertDatabaseHas('announcements', [
            'title' => 'Church gathering',
            'created_by' => $admin->id,
        ]);
        $this->assertCount(1, $recipient->fresh()->notifications);
    }

    public function test_finance_and_user_management_are_admin_only(): void
    {
        $this->actingAs(User::factory()->create(['email' => 'member@example.com']));

        $this->get(route('dashboard.resource.index', 'finance'))->assertForbidden();
        $this->get(route('dashboard.resource.index', 'users'))->assertForbidden();
    }

    public function test_admin_can_create_finance_records_and_users(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $this->actingAs($admin);

        $this->withSession(['_token' => self::CSRF_TOKEN])->post(route('dashboard.resource.store', 'finance'), [
            '_token' => self::CSRF_TOKEN,
            'transaction_date' => '2026-10-30',
            'type' => 'income',
            'category' => 'Offering',
            'amount' => '150.00',
            'description' => 'Sunday offering',
        ])->assertRedirect();
        $this->assertDatabaseHas('financial_transactions', [
            'category' => 'Offering',
            'amount' => '150.00',
            'recorded_by' => $admin->id,
        ]);

        $this->withSession(['_token' => self::CSRF_TOKEN])->post(route('dashboard.resource.store', 'users'), [
            '_token' => self::CSRF_TOKEN,
            'name' => 'Ministry Leader',
            'email' => 'ministry@example.com',
            'password' => 'my-password',
        ])->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'ministry@example.com']);
        $this->assertTrue(Hash::check('my-password', User::where('email', 'ministry@example.com')->value('password')));
    }

    public function test_user_can_update_profile_and_mark_notification_read(): void
    {
        $user = User::factory()->create(['email' => 'member@example.com']);
        $user->notify(new \App\Notifications\AnnouncementPublished(
            \App\Models\Announcement::create([
                'title' => 'Service update',
                'message' => 'New service time.',
                'created_by' => User::factory()->create()->id,
            ])
        ));
        $notificationId = $user->notifications()->firstOrFail()->id;
        $this->actingAs($user);

        $this->withSession(['_token' => self::CSRF_TOKEN])->put(route('dashboard.profile.update'), [
            '_token' => self::CSRF_TOKEN,
            'name' => 'Updated Member',
            'email' => 'member@example.com',
        ])->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Member']);

        $this->withSession(['_token' => self::CSRF_TOKEN])->patch(route('dashboard.notifications.read', $notificationId), [
            '_token' => self::CSRF_TOKEN,
        ])->assertRedirect();
        $this->assertNotNull($user->fresh()->notifications()->findOrFail($notificationId)->read_at);
    }
}
