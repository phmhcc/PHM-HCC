<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Department;
use App\Notifications\AccountInvitation;
use App\Notifications\DashboardInformationPublished;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardModulesTest extends TestCase
{
    use RefreshDatabase;

    private const CSRF_TOKEN = 'dashboard-test-token';

    protected function setUp(): void
    {
        parent::setUp();
        config(['admin.email' => 'admin@example.com']);
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    public function test_admin_can_open_each_dashboard_module(): void
    {
        $this->actingAs(User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin']));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Welcome Back')
            ->assertSee('src="'.asset('Images/images.jpg').'"', false)
            ->assertSee('<footer class="site-footer">', false)
            ->assertSeeText('Pentecostal Holiness Mission Hope Christian Church')
            ->assertSee('aria-label="Facebook"', false)
            ->assertSee('id="profileToggle"', false)
            ->assertSee('id="profileMenu"', false)
            ->assertSee(route('dashboard.profile'))
            ->assertSee('href="'.route('home').'"', false)
            ->assertSee('action="'.route('logout').'" method="POST"', false)
            ->assertSee('name="_token"', false);

        $this->get(route('home'))->assertOk();

        foreach (['deacon', 'elder', 'department-leader', 'treasurer'] as $role) {
            $this->get(route('dashboard.'.$role))->assertOk();
        }

        foreach (['events', 'announcements', 'duties', 'meetings', 'departments', 'reports', 'finance', 'users'] as $module) {
            $response = $this->get(route('dashboard.resource.index', $module));
            self::assertSame(200, $response->status(), $module.' returned '.$response->status().': '.substr(strip_tags($response->getContent()), 0, 250));
        }

        $this->get(route('dashboard.profile'))->assertOk();
        $this->get(route('dashboard.notifications'))->assertOk();
    }

    public function test_dashboard_breadcrumbs_show_module_hierarchy_in_current_language(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('dashboard.resource.index', 'finance'))
            ->assertOk()
            ->assertSee('aria-label="Breadcrumb"', false)
            ->assertSeeText('Dashboard')
            ->assertSeeText('Financial Transactions');

        $this->withSession(['locale' => 'sw'])
            ->get(route('dashboard.resource.index', 'finance'))
            ->assertOk()
            ->assertSeeText('Dashibodi')
            ->assertSeeText('Miamala ya Fedha');
    }

    public function test_department_names_are_translated_when_swahili_is_selected(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin']);
        foreach (['Women', 'IT', 'Youth', 'Children', 'Ushers'] as $name) {
            Department::create(['name' => $name]);
        }

        $this->actingAs($admin)->withSession(['locale' => 'sw']);

        $departments = $this->get(route('dashboard.resource.index', 'departments'))
            ->assertOk();
        foreach (['Wanawake', 'Teknolojia ya Habari', 'Vijana', 'Watoto', 'Wakaribishaji'] as $name) {
            $departments->assertSeeText($name);
        }

        $users = $this->get(route('dashboard.resource.index', 'users'))
            ->assertOk();
        foreach (['Wanawake', 'Teknolojia ya Habari', 'Vijana', 'Watoto', 'Wakaribishaji'] as $name) {
            $users->assertSeeText($name);
        }
    }

    public function test_admin_can_create_update_and_delete_a_church_event(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin']);
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

    public function test_past_events_are_marked_completed_without_changing_cancelled_or_future_events(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 9)->setTime(13, 0));
        $admin = User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin']);
        $this->actingAs($admin);

        $pastEventId = DB::table('events')->insertGetId([
            'title' => 'Past event',
            'start_date' => now()->subDay(),
            'end_date' => now()->subHour(),
            'status' => 'scheduled',
            'created_by' => $admin->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $cancelledEvent = \App\Models\Event::create([
            'title' => 'Cancelled event',
            'start_date' => now()->subDay(),
            'end_date' => now()->subHour(),
            'status' => 'cancelled',
            'created_by' => $admin->id,
        ]);
        $futureEvent = \App\Models\Event::create([
            'title' => 'Future event',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'status' => 'scheduled',
            'created_by' => $admin->id,
        ]);

        $this->get(route('dashboard.resource.index', 'events'))
            ->assertOk()
            ->assertSeeText('Completed');

        $this->assertDatabaseHas('events', ['id' => $pastEventId, 'status' => 'completed']);
        $this->assertDatabaseHas('events', ['id' => $cancelledEvent->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('events', ['id' => $futureEvent->id, 'status' => 'scheduled']);

        $newPastEvent = \App\Models\Event::create([
            'title' => 'New past event',
            'start_date' => now()->subDay(),
            'status' => 'scheduled',
            'created_by' => $admin->id,
        ]);
        $this->assertSame('completed', $newPastEvent->status);

        $this->travelBack();
    }

    public function test_new_announcement_notifies_the_other_users(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin']);
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

    public function test_announcement_without_publish_date_uses_the_current_date_and_time(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin']);
        $publishTime = now()->startOfSecond();
        $this->travelTo($publishTime);
        $this->actingAs($admin);

        $this->withSession(['_token' => self::CSRF_TOKEN])->post(route('dashboard.resource.store', 'announcements'), [
            '_token' => self::CSRF_TOKEN,
            'title' => 'Immediate announcement',
            'message' => 'This announcement is published now.',
        ])->assertRedirect();

        $this->assertDatabaseHas('announcements', [
            'title' => 'Immediate announcement',
            'published_at' => $publishTime->toDateTimeString(),
        ]);
    }

    public function test_admin_created_shared_information_notifies_other_users_and_appears_on_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $recipient = User::factory()->create(['role' => 'deacon']);
        $anotherRecipient = User::factory()->create(['role' => 'elder']);
        $this->actingAs($admin);

        $this->withSession(['_token' => self::CSRF_TOKEN])->post(route('dashboard.resource.store', 'events'), [
            '_token' => self::CSRF_TOKEN,
            'title' => 'Church-wide gathering',
            'start_date' => '2026-11-08T10:00',
            'status' => 'scheduled',
        ])->assertRedirect();

        $this->assertCount(0, $admin->fresh()->notifications);
        $this->assertCount(1, $recipient->fresh()->notifications);
        $this->assertCount(1, $anotherRecipient->fresh()->notifications);

        $this->actingAs($recipient)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('New event: Church-wide gathering')
            ->assertSeeText('An administrator added this to the church dashboard.');
    }

    public function test_department_notification_is_translated_using_recipient_language(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $recipient = User::factory()->create(['role' => 'deacon']);

        $this->actingAs($admin)
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('dashboard.resource.store', 'departments'), [
                '_token' => self::CSRF_TOKEN,
                'name' => 'Women',
            ])
            ->assertRedirect();

        $this->actingAs($recipient)
            ->withSession(['locale' => 'sw'])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Idara mpya: Wanawake')
            ->assertSeeText('Msimamizi ameongeza taarifa hii kwenye dashibodi ya kanisa.');

        $this->get(route('dashboard.notifications'))
            ->assertOk()
            ->assertSeeText('Idara mpya: Wanawake')
            ->assertSeeText('Msimamizi ameongeza taarifa hii kwenye dashibodi ya kanisa.');

        $this->assertSame(
            'Idara mpya: Wanawake',
            DashboardInformationPublished::localizedTitle(['title' => 'New department: Women']),
        );
        $this->assertSame(
            'Msimamizi ameongeza taarifa hii kwenye dashibodi ya kanisa.',
            DashboardInformationPublished::localizedMessage(['message' => 'An administrator added this to the church dashboard.']),
        );
    }

    public function test_all_dashboard_roles_can_see_admin_created_events_duties_and_meetings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = \App\Models\Event::create([
            'title' => 'Church-wide event',
            'start_date' => now()->addDay(),
            'status' => 'scheduled',
            'created_by' => $admin->id,
        ]);
        \App\Models\Duty::create([
            'title' => 'Church-wide duty',
            'duty_date' => today()->addDay(),
            'status' => 'pending',
            'created_by' => $admin->id,
        ]);
        \App\Models\Meeting::create([
            'title' => 'Church-wide meeting',
            'meeting_date' => today()->addDay(),
            'status' => 'scheduled',
            'created_by' => $admin->id,
        ]);

        foreach (['deacon', 'elder', 'treasurer', 'department_leader'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)
                ->get(route('dashboard.resource.index', 'events'))
                ->assertOk()
                ->assertSeeText($event->title);
            $this->get(route('dashboard.resource.index', 'duties'))
                ->assertOk()
                ->assertSeeText('Church-wide duty');
            $this->get(route('dashboard.resource.index', 'meetings'))
                ->assertOk()
                ->assertSeeText('Church-wide meeting');
            $this->get(route('dashboard'))
                ->assertOk()
                ->assertSeeText($event->title);
        }
    }

    public function test_all_dashboard_roles_can_view_all_announcements_but_only_admin_can_create(): void
    {
        $author = User::factory()->create(['role' => 'admin']);
        \App\Models\Announcement::create([
            'title' => 'Church-wide announcement',
            'message' => 'Visible to every dashboard role.',
            'created_by' => $author->id,
        ]);

        foreach (['admin', 'deacon', 'elder', 'department_leader', 'treasurer'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)
                ->get(route('dashboard.resource.index', 'announcements'))
                ->assertOk()
                ->assertSeeText('Church-wide announcement');

            if ($role !== 'admin') {
                $this->withSession(['_token' => self::CSRF_TOKEN])
                    ->post(route('dashboard.resource.store', 'announcements'), [
                        '_token' => self::CSRF_TOKEN,
                        'title' => 'Unauthorized announcement',
                        'message' => 'Only administrators can publish this.',
                    ])
                    ->assertForbidden();
            }

        }

        $this->actingAs($author)
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('dashboard.resource.store', 'announcements'), [
                '_token' => self::CSRF_TOKEN,
                'title' => 'Admin announcement',
                'message' => 'Created by the admin.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('announcements', ['title' => 'Admin announcement']);
        $this->assertDatabaseMissing('announcements', ['title' => 'Unauthorized announcement']);
    }

    public function test_public_announcements_page_shows_all_announcements_from_database(): void
    {
        $publisher = User::factory()->create();
        \App\Models\Announcement::create([
            'title' => 'This Sunday',
            'message' => 'Join the church family for service.',
            'published_at' => now()->subDay(),
            'created_by' => $publisher->id,
        ]);
        \App\Models\Announcement::create([
            'title' => 'Future announcement',
            'message' => 'This scheduled update should be visible.',
            'published_at' => now()->addDay(),
            'created_by' => $publisher->id,
        ]);

        $this->get(route('announcements'))
            ->assertOk()
            ->assertSeeText('This Sunday')
            ->assertSeeText('Join the church family for service.')
            ->assertSeeText('Future announcement')
            ->assertSeeText('This scheduled update should be visible.')
            ->assertSeeText('Need more information?')
            ->assertSee('href="'.route('contact').'"', false);
        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('This Sunday')
            ->assertSeeText('Future announcement');
    }

    public function test_public_events_page_shows_events_from_database(): void
    {
        $publisher = User::factory()->create();
        \App\Models\Event::create([
            'title' => 'Community celebration',
            'description' => 'Everyone is welcome.',
            'start_date' => now()->addWeek(),
            'venue' => 'Main church',
            'status' => 'scheduled',
            'created_by' => $publisher->id,
        ]);

        $this->get(route('events'))
            ->assertOk()
            ->assertSeeText('Community celebration')
            ->assertSeeText('Everyone is welcome.')
            ->assertSeeText('Main church')
            ->assertSeeText('Weekly gatherings');
    }

    public function test_reports_can_store_download_replace_and_delete_pdf_and_excel_attachments(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'department_leader']);
        $this->actingAs($user);

        $this->withSession(['_token' => self::CSRF_TOKEN])->post(route('dashboard.resource.store', 'reports'), [
            '_token' => self::CSRF_TOKEN,
            'title' => 'Monthly report',
            'status' => 'pending',
            'attachment' => UploadedFile::fake()->create('monthly.pdf', 20, 'application/pdf'),
        ])->assertRedirect();

        $report = \App\Models\Report::where('title', 'Monthly report')->firstOrFail();
        $pdfPath = 'reports/'.$report->id.'/attachment.pdf';
        $this->assertTrue(Storage::disk('local')->exists($pdfPath));
        $this->get(route('dashboard.reports.attachment', $report->id))
            ->assertOk()
            ->assertDownload('attachment.pdf');

        $this->actingAs(User::factory()->create(['role' => 'deacon']));
        $this->get(route('dashboard.reports.attachment', $report->id))->assertNotFound();

        $this->actingAs($user);
        $this->withSession(['_token' => self::CSRF_TOKEN])->put(route('dashboard.resource.update', ['reports', $report->id]), [
            '_token' => self::CSRF_TOKEN,
            'title' => 'Monthly report',
            'status' => 'pending',
            'attachment' => UploadedFile::fake()->create(
                'monthly.xlsx',
                20,
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ),
        ])->assertRedirect();

        $this->assertFalse(Storage::disk('local')->exists($pdfPath));
        $this->assertTrue(Storage::disk('local')->exists('reports/'.$report->id.'/attachment.xlsx'));

        $this->withSession(['_token' => self::CSRF_TOKEN])->delete(route('dashboard.resource.destroy', ['reports', $report->id]), [
            '_token' => self::CSRF_TOKEN,
        ])->assertRedirect();
        $this->assertFalse(Storage::disk('local')->exists('reports/'.$report->id.'/attachment.xlsx'));
    }

    public function test_report_owner_can_open_a_printable_report_but_other_users_cannot(): void
    {
        $owner = User::factory()->create(['role' => 'department_leader']);
        $report = \App\Models\Report::create([
            'title' => 'Monthly report',
            'activities' => 'Community outreach and Sunday service.',
            'achievements' => 'Welcomed five new members.',
            'challenges' => 'Limited transport.',
            'recommendations' => 'Arrange additional transport.',
            'submitted_by' => $owner->id,
            'status' => 'reviewed',
        ]);

        $this->actingAs($owner)
            ->get(route('dashboard.reports.print', $report->id))
            ->assertOk()
            ->assertSeeText('Monthly report')
            ->assertSeeText('Community outreach and Sunday service.')
            ->assertSeeText('Welcomed five new members.')
            ->assertSeeText('Limited transport.')
            ->assertSeeText('Arrange additional transport.')
            ->assertSee('onclick="window.print()"', false);

        $this->get(route('dashboard.resource.index', 'reports'))
            ->assertOk()
            ->assertSee('class="resource-row-actions"', false)
            ->assertSee(route('dashboard.reports.print', $report->id));

        $this->actingAs(User::factory()->create(['role' => 'deacon']))
            ->get(route('dashboard.reports.print', $report->id))
            ->assertNotFound();
    }

    public function test_report_attachment_accepts_only_pdf_and_excel_files(): void
    {
        Storage::fake('local');
        $this->actingAs(User::factory()->create(['role' => 'department_leader']));

        $this->from(route('dashboard.resource.index', 'reports'))
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('dashboard.resource.store', 'reports'), [
                '_token' => self::CSRF_TOKEN,
                'title' => 'Invalid attachment report',
                'status' => 'pending',
                'attachment' => UploadedFile::fake()->create('notes.txt', 10, 'text/plain'),
            ])
            ->assertRedirect(route('dashboard.resource.index', 'reports'))
            ->assertSessionHasErrors('attachment');

        $this->assertDatabaseMissing('reports', ['title' => 'Invalid attachment report']);
    }

    public function test_finance_is_for_treasurers_and_admins_while_user_management_is_admin_only(): void
    {
        $this->actingAs(User::factory()->create(['email' => 'member@example.com']));

        $this->get(route('dashboard.resource.index', 'finance'))->assertForbidden();
        $this->get(route('dashboard.resource.index', 'users'))->assertForbidden();

        $this->actingAs(User::factory()->create(['role' => 'treasurer']));
        $this->get(route('dashboard.resource.index', 'finance'))->assertOk();
        $this->get(route('dashboard.resource.index', 'users'))->assertForbidden();
    }

    public function test_role_permissions_restrict_modules_and_record_actions(): void
    {
        $deacon = User::factory()->create(['role' => 'deacon']);
        $this->actingAs($deacon);

        $this->get(route('dashboard.resource.index', 'reports'))
            ->assertOk()
            ->assertSee('Add a record');
        $this->get(route('dashboard.resource.index', 'departments'))
            ->assertOk()
            ->assertDontSee('Add a record')
            ->assertDontSee('Save changes')
            ->assertDontSee('Delete</button>', false);
        $this->get(route('dashboard.resource.index', 'finance'))->assertForbidden();
        $this->get(route('dashboard.resource.index', 'users'))->assertForbidden();

        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('dashboard.resource.store', 'announcements'), [
                '_token' => self::CSRF_TOKEN,
                'title' => 'Unauthorized announcement',
                'message' => 'This should not be created.',
            ])
            ->assertForbidden();
        $this->assertDatabaseMissing('announcements', ['title' => 'Unauthorized announcement']);

        $this->actingAs(User::factory()->create(['role' => 'elder']));
        $this->get(route('dashboard.resource.index', 'announcements'))->assertOk();
        $this->get(route('dashboard.resource.index', 'finance'))->assertForbidden();
        \App\Models\Report::create([
            'title' => 'Deacon report',
            'submitted_by' => User::factory()->create(['role' => 'deacon'])->id,
            'status' => 'pending',
        ]);
        $this->get(route('dashboard.resource.index', 'reports'))
            ->assertOk()
            ->assertDontSeeText('Deacon report');

        $this->actingAs(User::factory()->create(['role' => 'treasurer']));
        $this->get(route('dashboard.resource.index', 'finance'))->assertOk();
        $this->get(route('dashboard.resource.index', 'announcements'))->assertOk();
    }

    public function test_admin_can_manage_non_admin_role_permissions_from_user_management(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $role = Role::where('name', 'deacon')->firstOrFail();
        $dashboardPermission = Permission::where('name', 'dashboard.view')->firstOrFail();
        $eventsPermission = Permission::where('name', 'events.view')->firstOrFail();
        $financePermission = Permission::where('name', 'finance.*')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard.resource.index', 'users'))
            ->assertOk()
            ->assertSee(route('dashboard.roles.index'));

        $this->get(route('dashboard.roles.index'))
            ->assertOk()
            ->assertSeeText('Roles and permissions')
            ->assertSeeText('Deacon');

        $this->get(route('dashboard.roles.edit', $role))
            ->assertOk()
            ->assertSeeText('Edit role')
            ->assertSeeText('Dashboard')
            ->assertSeeText('View');

        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->put(route('dashboard.roles.update', $role), [
                '_token' => self::CSRF_TOKEN,
                'name' => 'deacon',
                'description' => 'Church Deacon',
                'permissions' => ['dashboard.view', 'events.view'],
            ])
            ->assertRedirect(route('dashboard.roles.edit', $role));

        $this->assertDatabaseHas('role_permissions', [
            'role_id' => $role->id,
            'permission_id' => $dashboardPermission->id,
        ]);
        $this->assertDatabaseHas('role_permissions', [
            'role_id' => $role->id,
            'permission_id' => $eventsPermission->id,
        ]);
        $this->assertDatabaseMissing('role_permissions', [
            'role_id' => $role->id,
            'permission_id' => $financePermission->id,
        ]);
        $this->assertDatabaseHas('role_permissions', [
            'role_id' => Role::where('name', 'admin')->value('id'),
            'permission_id' => Permission::where('name', '*')->value('id'),
        ]);
    }

    public function test_admin_can_create_view_rename_and_delete_roles_without_orphaning_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->get(route('dashboard.roles.create'))
            ->assertOk()
            ->assertSeeText('Create role')
            ->assertSeeText('Create');

        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('dashboard.roles.store'), [
                '_token' => self::CSRF_TOKEN,
                'name' => 'Church Coordinator',
                'description' => 'Coordinates church activities.',
                'permissions' => ['dashboard.view', 'events.view', 'events.create'],
            ])
            ->assertSessionHasNoErrors();

        $role = Role::where('name', 'church_coordinator')->firstOrFail();
        $dashboardUser = User::factory()->create(['role' => $role->name]);
        $this->assertDatabaseHas('role_permissions', [
            'role_id' => $role->id,
            'permission_id' => Permission::where('name', 'events.create')->value('id'),
        ]);

        $this->get(route('dashboard.roles.show', $role))
            ->assertOk()
            ->assertSeeText('Church Coordinator')
            ->assertSeeText('Events · Create');

        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->put(route('dashboard.roles.update', $role), [
                '_token' => self::CSRF_TOKEN,
                'name' => 'Church Leader',
                'description' => 'Leads church activities.',
                'permissions' => ['dashboard.view'],
            ])
            ->assertRedirect(route('dashboard.roles.edit', $role->fresh()));
        $role->refresh();
        $this->assertSame('church_leader', $role->name);
        $this->assertSame('church_leader', $role->name);
        $this->assertSame('church_leader', $dashboardUser->fresh()->role);
        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->delete(route('dashboard.roles.destroy', $role), ['_token' => self::CSRF_TOKEN])
            ->assertStatus(422)
            ->assertSeeText('Reassign them before deleting the role.');
        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'church_leader']);

        $dashboardUser->delete();
        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->delete(route('dashboard.roles.destroy', $role), ['_token' => self::CSRF_TOKEN])
            ->assertRedirect(route('dashboard.roles.index'));
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_non_admin_cannot_manage_roles_or_change_administrator_permissions(): void
    {
        $deacon = User::factory()->create(['role' => 'deacon']);
        $adminRole = Role::where('name', 'admin')->firstOrFail();

        $this->actingAs($deacon)
            ->get(route('dashboard.roles.index'))
            ->assertForbidden();

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('dashboard.roles.edit', $adminRole))
            ->assertForbidden();
    }

    public function test_permissions_are_loaded_from_role_permission_database_tables(): void
    {
        $role = Role::create(['name' => 'custom_role', 'description' => 'Custom role']);
        $dashboardPermission = Permission::where('name', 'dashboard.view')->firstOrFail();
        $permission = Permission::where('name', 'announcements.view')->firstOrFail();
        $role->permissions()->attach($dashboardPermission);
        $user = User::factory()->create(['role' => 'custom_role']);

        $this->actingAs($user)
            ->get(route('dashboard.resource.index', 'announcements'))
            ->assertForbidden();

        $role->permissions()->attach($permission);

        $this->actingAs($user->fresh());
        $this->get(route('dashboard.resource.index', 'announcements'))->assertOk();

        $role->permissions()->detach($permission);

        $this->actingAs($user->fresh());
        $this->get(route('dashboard.resource.index', 'announcements'))->assertForbidden();
    }

    public function test_admin_can_create_finance_records_and_users(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin']);
        $department = Department::create(['name' => 'Worship']);
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
            'department_id' => $department->id,
            'role' => 'department_leader',
        ])->assertRedirect();
        $newUser = User::where('email', 'ministry@example.com')->firstOrFail();
        $this->assertSame('department_leader', $newUser->role);
        $this->assertSame($department->id, $newUser->department_id);
        $this->assertNotSame('', $newUser->password);
        Notification::assertSentTo($newUser, AccountInvitation::class);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $newUser->email]);
    }

    public function test_user_creation_form_has_no_password_field_and_promises_an_invitation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('dashboard.resource.index', 'users'))
            ->assertOk()
            ->assertSeeText('The user will receive an email invitation to set their own password.')
            ->assertSee('name="department_id"', false)
            ->assertSee('value="church_secretary"', false);
        preg_match('/<form id="createResourceForm"[^>]*>(.*?)<\/form>/s', $response->getContent(), $createForm);
        self::assertArrayHasKey(1, $createForm);
        self::assertStringNotContainsString('name="password"', $createForm[1]);
        self::assertLessThan(
            strpos($createForm[1], 'name="role"'),
            strpos($createForm[1], 'name="department_id"')
        );
    }

    public function test_duty_schedule_form_does_not_include_a_department_field(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('dashboard.resource.index', 'duties'))
            ->assertOk()
            ->assertDontSee('name="department_id"', false);
    }

    public function test_new_user_can_set_password_using_the_invitation_token(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $department = Department::create(['name' => 'Worship']);
        $this->actingAs($admin)
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('dashboard.resource.store', 'users'), [
                '_token' => self::CSRF_TOKEN,
                'name' => 'Invited User',
                'email' => 'invite@example.com',
                'department_id' => $department->id,
                'role' => 'deacon',
            ])
            ->assertRedirect();

        $user = User::where('email', 'invite@example.com')->firstOrFail();
        $notification = Notification::sent($user, AccountInvitation::class)->first();
        self::assertNotNull($notification);

        $mail = $notification->toMail($user);
        $emailHtml = $mail->render();
        self::assertStringContainsString('Welcome to PHM HCC!', $emailHtml);
        self::assertStringContainsString('Invited User', $emailHtml);
        self::assertStringContainsString('invite@example.com', $emailHtml);
        self::assertStringContainsString(asset('Images/images.jpg'), $emailHtml);
        self::assertStringContainsString('#082f63', $emailHtml);
        self::assertStringContainsString('#f5ad18', $emailHtml);
        self::assertStringContainsString('<strong style="color:#082f63;">Department:</strong><br>Worship', $emailHtml);
        self::assertStringContainsString('Deacon', $emailHtml);
        self::assertStringContainsString(route('password.reset', [
            'token' => $notification->token,
            'email' => $user->email,
        ]), $emailHtml);

        Auth::logout();
        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('password.store'), [
                '_token' => self::CSRF_TOKEN,
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'new-user-password',
                'password_confirmation' => 'new-user-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('new-user-password', $user->fresh()->password));
    }

    public function test_user_invitation_rejects_an_email_already_in_the_database(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin']);
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $this->actingAs($admin)
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->from(route('dashboard.resource.index', 'users'))
            ->post(route('dashboard.resource.store', 'users'), [
                '_token' => self::CSRF_TOKEN,
                'name' => 'Duplicate User',
                'email' => $existingUser->email,
                'role' => 'deacon',
            ])
            ->assertRedirect(route('dashboard.resource.index', 'users'))
            ->assertSessionHasErrors([
                'email' => 'This email address is already registered.',
            ]);

        $this->assertDatabaseCount('users', 2);
        Notification::assertNothingSent();
    }

    public function test_finance_categories_are_limited_and_receipts_can_be_uploaded_downloaded_replaced_and_deleted(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin']);
        $this->actingAs($admin)
            ->get(route('dashboard.resource.index', 'finance'))
            ->assertOk()
            ->assertSeeText('Tithe')
            ->assertSeeText('Thanksgiving')
            ->assertSeeText('Building')
            ->assertSeeText('Supporting Expense')
            ->assertSeeText('Receipt attachment');

        app()->setLocale('sw');
        $this->get(route('dashboard.resource.index', 'finance'))
            ->assertOk()
            ->assertSeeText('Zaka')
            ->assertSeeText('Sadaka')
            ->assertSeeText('Nadhiri')
            ->assertSeeText('Shukrani')
            ->assertSeeText('Ujenzi')
            ->assertSeeText('Gharama za usaidizi')
            ->assertSeeText('Mapato')
            ->assertSeeText('Matumizi');

        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('dashboard.resource.store', 'finance'), [
                '_token' => self::CSRF_TOKEN,
                'transaction_date' => '2026-11-03',
                'type' => 'income',
                'category' => 'Tithe',
                'amount' => '250.00',
                'reference' => 'Sunday service',
                'receipt' => UploadedFile::fake()->create('tithe-receipt.pdf', 20, 'application/pdf'),
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $transaction = \App\Models\FinancialTransaction::where('category', 'Tithe')->firstOrFail();
        $pdfPath = 'finance/'.$transaction->id.'/receipt.pdf';
        $this->assertTrue(Storage::disk('local')->exists($pdfPath));
        $this->get(route('dashboard.finance.receipt', $transaction->id))
            ->assertOk()
            ->assertDownload('receipt.pdf');

        $pngContents = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l9sAAAAASUVORK5CYII=');
        $this->assertNotFalse($pngContents);
        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->put(route('dashboard.resource.update', ['finance', $transaction->id]), [
                '_token' => self::CSRF_TOKEN,
                'transaction_date' => '2026-11-03',
                'type' => 'income',
                'category' => 'Offering',
                'amount' => '300.00',
                'receipt' => UploadedFile::fake()->createWithContent('offering-receipt.png', $pngContents),
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $transaction->refresh();
        $pngPath = 'finance/'.$transaction->id.'/receipt.png';
        $this->assertTrue(Storage::disk('local')->exists($pngPath));
        $this->assertFalse(Storage::disk('local')->exists($pdfPath));
        $this->assertSame('Offering', $transaction->category);

        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->delete(route('dashboard.resource.destroy', ['finance', $transaction->id]), [
                '_token' => self::CSRF_TOKEN,
            ])
            ->assertRedirect();
        $this->assertFalse(Storage::disk('local')->exists($pngPath));
    }

    public function test_finance_rejects_categories_outside_the_dropdown_options(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->from(route('dashboard.resource.index', 'finance'))
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('dashboard.resource.store', 'finance'), [
                '_token' => self::CSRF_TOKEN,
                'transaction_date' => '2026-11-03',
                'type' => 'income',
                'category' => 'Other',
                'amount' => '250.00',
            ])
            ->assertRedirect(route('dashboard.resource.index', 'finance'))
            ->assertSessionHasErrors('category');

        $this->assertDatabaseMissing('financial_transactions', ['category' => 'Other']);
    }

    public function test_user_can_update_profile_and_mark_notification_read(): void
    {
        $user = User::factory()->create(['email' => 'member@example.com', 'role' => 'admin']);
        $user->notify(new \App\Notifications\AnnouncementPublished(
            \App\Models\Announcement::create([
                'title' => 'Service update',
                'message' => 'New service time.',
                'created_by' => User::factory()->create()->id,
            ])
        ));
        $notificationId = $user->notifications()->firstOrFail()->id;
        $this->actingAs($user);

        $this->get(route('dashboard.profile'))
            ->assertOk()
            ->assertSee('aria-controls="current_password"', false)
            ->assertSee('aria-controls="password"', false)
            ->assertSee('aria-controls="password_confirmation"', false);

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
