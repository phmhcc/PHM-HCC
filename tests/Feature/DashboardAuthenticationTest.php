<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private const CSRF_TOKEN = 'test-token';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    public function test_login_page_has_separate_password_visibility_and_submit_controls(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('<nav class="navbar">', false)
            ->assertDontSee('<footer>', false)
            ->assertSee('id="password"', false)
            ->assertSee('id="togglePasswordButton"', false)
            ->assertSee('aria-label="Show password"', false)
            ->assertDontSee('name="role"', false)
            ->assertDontSeeText('Sign in as')
            ->assertSee("password.type = isHidden ? 'text' : 'password'", false)
            ->assertSee('type="submit"', false);
    }

    public function test_public_pages_keep_the_navigation_and_footer(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<nav class="navbar">', false)
            ->assertSee('id="primaryNavigation"', false)
            ->assertSee('aria-controls="primaryNavigation"', false)
            ->assertSee('<footer class="site-footer">', false)
            ->assertSee('src="'.asset('Images/images.jpg').'"', false);
    }

    public function test_guests_are_redirected_to_sign_in_from_dashboard_pages(): void
    {
        $dashboardPages = [
            route('dashboard'),
            route('dashboard.deacon'),
            route('dashboard.elder'),
            route('dashboard.department-leader'),
            route('dashboard.treasurer'),
            route('dashboard.notifications'),
            route('dashboard.profile'),
            route('dashboard.resource.index', 'events'),
            route('dashboard.resource.index', 'announcements'),
            route('dashboard.resource.index', 'duties'),
            route('dashboard.resource.index', 'meetings'),
            route('dashboard.resource.index', 'departments'),
            route('dashboard.resource.index', 'reports'),
            route('dashboard.resource.index', 'finance'),
            route('dashboard.resource.index', 'users'),
        ];

        foreach ($dashboardPages as $page) {
            $this->get($page)->assertRedirect(route('login'));
        }
    }

    public function test_guests_cannot_submit_dashboard_changes(): void
    {
        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('dashboard.resource.store', 'events'), [
                '_token' => self::CSRF_TOKEN,
                'title' => 'Unauthorized event',
            ])
            ->assertRedirect(route('login'));

        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->put(route('dashboard.profile.update'), [
                '_token' => self::CSRF_TOKEN,
                'name' => 'Unauthenticated',
            ])
            ->assertRedirect(route('login'));
    }

    public function test_user_can_sign_in_view_dashboard_and_sign_out(): void
    {
        $user = User::factory()->create([
            'email' => 'leader@example.com',
            'role' => 'admin',
            'password' => 'secure-password',
        ]);

        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('login.store'), [
            '_token' => self::CSRF_TOKEN,
            'email' => $user->email,
            'password' => 'secure-password',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText($user->name);

        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('logout'), ['_token' => self::CSRF_TOKEN])
            ->assertRedirect(route('home'));

        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_invalid_credentials_do_not_authenticate(): void
    {
        User::factory()->create([
            'email' => 'leader@example.com',
            'password' => 'secure-password',
            'role' => 'deacon',
        ]);

        $this->from(route('login'))
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('login.store'), [
                '_token' => self::CSRF_TOKEN,
                'email' => 'leader@example.com',
                'password' => 'incorrect-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
    }

    public function test_admin_seeder_creates_the_administrator_with_phmhcc_password(): void
    {
        $this->seed(AdminUserSeeder::class);

        $this->assertDatabaseHas('users', [
            'name' => 'Tabiana',
            'email' => 'tabianavitus@gmail.com',
            'role' => 'admin',
        ]);
        $this->assertTrue(Hash::check(
            'phmhcc',
            User::where('email', 'tabianavitus@gmail.com')->value('password')
        ));
    }

    public function test_admin_seeder_updates_existing_account_to_phmhcc_password(): void
    {
        config(['admin.email' => 'admin@example.com']);
        User::factory()->create([
            'email' => 'admin@example.com',
            'name' => 'Old Admin Name',
            'role' => 'member',
            'password' => 'old-password',
        ]);

        $this->seed(AdminUserSeeder::class);

        $admin = User::where('email', 'admin@example.com')->firstOrFail();
        $this->assertSame('Tabiana', $admin->name);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('phmhcc', $admin->password));

        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('login.store'), [
                '_token' => self::CSRF_TOKEN,
                'email' => 'admin@example.com',
                'password' => 'phmhcc',
            ])
            ->assertRedirect(route('dashboard'));
    }

    public function test_users_can_sign_in_to_their_assigned_role_dashboard(): void
    {
        foreach ([
            'admin' => 'dashboard',
            'deacon' => 'deacon',
            'elder' => 'elder',
            'department_leader' => 'department-leader',
            'treasurer' => 'treasurer',
        ] as $role => $routeRole) {
            $user = User::factory()->create([
                'email' => $role.'@example.com',
                'role' => $role,
                'password' => 'secure-password',
            ]);

            $this->withSession(['_token' => self::CSRF_TOKEN])
                ->post(route('login.store'), [
                    '_token' => self::CSRF_TOKEN,
                    'email' => $user->email,
                    'password' => 'secure-password',
                ])
                ->assertRedirect($routeRole === 'dashboard' ? route('dashboard') : route('dashboard.'.$routeRole));

            $this->get($routeRole === 'dashboard' ? route('dashboard') : route('dashboard.'.$routeRole))->assertOk();
            $this->post(route('logout'), [
                '_token' => $this->app['session.store']->token(),
            ])->assertRedirect(route('home'));
        }
    }

    public function test_login_uses_the_role_assigned_to_the_account(): void
    {
        $user = User::factory()->create([
            'email' => 'deacon@example.com',
            'role' => 'deacon',
            'password' => 'secure-password',
        ]);

        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('login.store'), [
                '_token' => self::CSRF_TOKEN,
                'email' => $user->email,
                'password' => 'secure-password',
            ])
            ->assertRedirect(route('dashboard.deacon'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_accounts_without_dashboard_permission_cannot_sign_in(): void
    {
        $user = User::factory()->create([
            'email' => 'member@example.com',
            'role' => 'member',
            'password' => 'secure-password',
        ]);

        $this->from(route('login'))
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('login.store'), [
                '_token' => self::CSRF_TOKEN,
                'email' => $user->email,
                'password' => 'secure-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_removed_pastor_role_cannot_sign_in_but_admin_can_use_main_dashboard(): void
    {
        $pastorAccount = User::factory()->create([
            'email' => 'former-pastor@example.com',
            'role' => 'pastor',
            'password' => 'secure-password',
        ]);

        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('login.store'), [
                '_token' => self::CSRF_TOKEN,
                'email' => $pastorAccount->email,
                'password' => 'secure-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();

        $this->seed(AdminUserSeeder::class);
        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('login.store'), [
                '_token' => self::CSRF_TOKEN,
                'email' => config('admin.email'),
                'password' => 'phmhcc',
            ])
            ->assertRedirect(route('dashboard'));
    }

    public function test_role_user_cannot_open_another_role_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'deacon']));

        $this->get(route('dashboard.deacon'))->assertOk();
        $this->get(route('dashboard.treasurer'))->assertForbidden();
    }
}
