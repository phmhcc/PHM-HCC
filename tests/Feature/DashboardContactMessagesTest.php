<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Notifications\ContactMessageReceived;
use App\Notifications\DashboardInformationPublished;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DashboardContactMessagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    public function test_contact_form_messages_are_stored_and_only_visible_with_the_role_permission(): void
    {
        Mail::shouldReceive('send')->once();
        Notification::fake();

        $role = Role::create(['name' => 'contact_viewer', 'description' => 'Contact message viewer']);
        $permissions = collect(['dashboard.view', 'contact_messages.view'])
            ->map(fn (string $name) => Permission::firstOrCreate(['name' => $name])->id);
        $role->permissions()->sync($permissions);
        $viewer = User::factory()->create(['role' => $role->name]);
        $withoutPermission = User::factory()->create(['role' => 'deacon']);

        $this->post(route('contact.send'), [
            'name' => 'Alex Visitor',
            'email' => 'alex@example.com',
            'message' => 'Please contact me about joining.',
        ])->assertRedirect();

        $message = ContactMessage::query()->firstOrFail();
        $this->assertSame('Please contact me about joining.', $message->message);
        Notification::assertSentTo($viewer, ContactMessageReceived::class);
        Notification::assertNotSentTo($withoutPermission, ContactMessageReceived::class);

        $this->actingAs($withoutPermission)
            ->get(route('dashboard.contact-messages.index'))
            ->assertForbidden();

        $this->actingAs($viewer)
            ->get(route('dashboard.contact-messages.index'))
            ->assertOk()
            ->assertSeeText('Alex Visitor')
            ->assertSeeText('alex@example.com')
            ->assertSeeText('Please contact me about joining.');
    }

    public function test_roles_form_includes_contact_message_permission(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('dashboard.roles.create'))
            ->assertOk()
            ->assertSee('contact_messages.view');
    }

    public function test_opening_a_notification_marks_it_read_and_redirects_to_its_target(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->notify(new DashboardInformationPublished(
            'New event: Sunday service',
            'An administrator added this to the church dashboard.',
            'New event: :title',
            ['title' => 'Sunday service'],
            'An administrator added this to the church dashboard.',
            route('dashboard.resource.index', 'events', false),
        ));
        $notification = $admin->notifications()->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard.notifications.open', $notification->id))
            ->assertRedirect(route('dashboard.resource.index', 'events'));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_contact_message_notification_dropdown_links_to_inbox_without_view_all_link(): void
    {
        $role = Role::create(['name' => 'contact_viewer', 'description' => 'Contact message viewer']);
        $permissions = collect(['dashboard.view', 'contact_messages.view'])
            ->map(fn (string $name) => Permission::firstOrCreate(['name' => $name])->id);
        $role->permissions()->sync($permissions);
        $viewer = User::factory()->create(['role' => $role->name]);
        $viewer->notify(new ContactMessageReceived(ContactMessage::create([
            'name' => 'Alex Visitor',
            'email' => 'alex@example.com',
            'message' => 'Please contact me.',
        ])));

        $this->actingAs($viewer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('New contact message')
            ->assertSeeText('New message from Alex Visitor')
            ->assertSee('href="'.route('dashboard.notifications.open', $viewer->notifications()->first()->id).'"', false)
            ->assertDontSeeText('View all notifications');

        $this->get(route('dashboard.notifications.open', $viewer->notifications()->first()->id))
            ->assertRedirect(route('dashboard.contact-messages.index'));
    }

    public function test_contact_message_time_is_displayed_in_east_africa_time(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        ContactMessage::create([
            'name' => 'Alex Visitor',
            'email' => 'alex@example.com',
            'message' => 'Please contact me.',
            'created_at' => '2026-11-09 10:35:00',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard.contact-messages.index'))
            ->assertOk()
            ->assertSee('datetime="2026-11-09T13:35:00+03:00"', false)
            ->assertSeeText('09 Nov 2026, 01:35 PM');
    }

    public function test_prune_command_deletes_messages_and_notifications_older_than_one_month(): void
    {
        $expired = ContactMessage::create([
            'name' => 'Expired Sender',
            'email' => 'expired@example.com',
            'message' => 'This message has expired.',
            'created_at' => now()->subMonth()->subSecond(),
        ]);
        $recent = ContactMessage::create([
            'name' => 'Recent Sender',
            'email' => 'recent@example.com',
            'message' => 'Keep this message.',
            'created_at' => now()->subMonth()->addSecond(),
        ]);
        $recipient = User::factory()->create(['role' => 'admin']);
        $recipient->notify(new ContactMessageReceived($expired));
        $recipient->notify(new ContactMessageReceived($recent));

        $this->artisan('contact-messages:prune')
            ->expectsOutput('Deleted 1 contact messages and 1 related notifications.')
            ->assertSuccessful();

        $this->assertDatabaseMissing('contact_messages', ['id' => $expired->id]);
        $this->assertDatabaseHas('contact_messages', ['id' => $recent->id]);
        $this->assertDatabaseCount('notifications', 1);
        $this->assertSame(
            ContactMessageReceived::class,
            $recipient->notifications()->firstOrFail()->type,
        );
    }

    public function test_contact_inbox_and_builtin_role_names_and_descriptions_are_translated_in_swahili(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->withSession(['locale' => 'sw']);

        $this->get(route('dashboard.contact-messages.index'))
            ->assertOk()
            ->assertSeeText('Ujumbe wa mawasiliano')
            ->assertSeeText('Ujumbe uliotumwa kupitia fomu ya mawasiliano ya tovuti.')
            ->assertSeeText('Kikasha pokezi')
            ->assertSeeText('Bado hakuna ujumbe.');

        $this->get(route('dashboard.roles.index'))
            ->assertOk()
            ->assertSeeText('Katibu wa Kanisa')
            ->assertSeeText('Shemasi')
            ->assertSeeText('Kiongozi wa Idara')
            ->assertSeeText('Mzee wa Kanisa')
            ->assertSeeText('Mweka Hazina')
            ->assertDontSeeText('Church Secretary')
            ->assertDontSeeText('Deacon')
            ->assertDontSeeText('Department Leader')
            ->assertDontSeeText('Elder')
            ->assertDontSeeText('Treasurer');
    }
}
