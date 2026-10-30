<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

    public function test_public_announcements_page_shows_current_announcements_from_database(): void
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
            'message' => 'This should not be visible yet.',
            'published_at' => now()->addDay(),
            'created_by' => $publisher->id,
        ]);

        $this->get(route('announcements'))
            ->assertOk()
            ->assertSeeText('This Sunday')
            ->assertSeeText('Join the church family for service.')
            ->assertSeeText('Need more information?')
            ->assertSee('href="'.route('contact').'"', false)
            ->assertDontSeeText('Future announcement');
    }

    public function test_reports_can_store_download_replace_and_delete_pdf_and_excel_attachments(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->withSession(['_token' => self::CSRF_TOKEN])->post(route('dashboard.resource.store', 'reports'), [
            '_token' => self::CSRF_TOKEN,
            'title' => 'Monthly report',
            'status' => 'pending',
            'attachment' => UploadedFile::fake()->create('monthly.pdf', 20, 'application/pdf'),
        ])->assertRedirect();

        $report = \App\Models\Report::where('title', 'Monthly report')->firstOrFail();
        $pdfPath = 'reports/'.$report->id.'/attachment.pdf';
        Storage::disk('local')->assertExists($pdfPath);
        $this->get(route('dashboard.reports.attachment', $report->id))
            ->assertOk()
            ->assertDownload('attachment.pdf');

        auth()->logout();
        $this->actingAs(User::factory()->create());
        $this->get(route('dashboard.reports.attachment', $report->id))->assertNotFound();

        auth()->logout();
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

        Storage::disk('local')->assertMissing($pdfPath);
        Storage::disk('local')->assertExists('reports/'.$report->id.'/attachment.xlsx');

        $this->withSession(['_token' => self::CSRF_TOKEN])->delete(route('dashboard.resource.destroy', ['reports', $report->id]), [
            '_token' => self::CSRF_TOKEN,
        ])->assertRedirect();
        Storage::disk('local')->assertMissing('reports/'.$report->id.'/attachment.xlsx');
    }

    public function test_report_attachment_accepts_only_pdf_and_excel_files(): void
    {
        Storage::fake('local');
        $this->actingAs(User::factory()->create());

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

        auth()->logout();
        $this->actingAs(User::factory()->create(['role' => 'treasurer']));
        $this->get(route('dashboard.resource.index', 'finance'))->assertOk();
        $this->get(route('dashboard.resource.index', 'users'))->assertForbidden();
    }

    public function test_admin_can_create_finance_records_and_users(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin']);
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
            'role' => 'department_leader',
            'password' => 'my-password',
        ])->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'ministry@example.com', 'role' => 'department_leader']);
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
