<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private const CSRF_TOKEN = 'test-token';

    public function test_login_page_has_separate_password_visibility_and_submit_controls(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('<nav class="navbar">', false)
            ->assertDontSee('<footer>', false)
            ->assertSee('id="password"', false)
            ->assertSee('id="togglePasswordButton"', false)
            ->assertSee('aria-label="Show password"', false)
            ->assertSee("password.type = isHidden ? 'text' : 'password'", false)
            ->assertSee('type="submit"', false);
    }

    public function test_public_pages_keep_the_navigation_and_footer(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<nav class="navbar">', false)
            ->assertSee('<footer class="site-footer">', false)
            ->assertSee('src="'.asset('Images/images.jpg').'"', false);
    }

    public function test_guests_are_redirected_to_sign_in_from_dashboard_pages(): void
    {
        $dashboardPages = [
            route('dashboard'),
            route('dashboard.pastor'),
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
            'role' => 'pastor',
            'password' => 'secure-password',
        ]);

        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('login.store'), [
            '_token' => self::CSRF_TOKEN,
            'email' => $user->email,
            'password' => 'secure-password',
            'role' => 'pastor',
        ])->assertRedirect(route('dashboard.pastor'));

        $this->get(route('dashboard.pastor'))
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
            'role' => 'pastor',
        ]);

        $this->from(route('login'))
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('login.store'), [
                '_token' => self::CSRF_TOKEN,
                'email' => 'leader@example.com',
                'password' => 'incorrect-password',
                'role' => 'pastor',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
    }

    public function test_admin_seeder_creates_the_configured_administrator_account(): void
    {
        config(['admin.password' => 'short']);

        $this->seed(AdminUserSeeder::class);

        $this->assertDatabaseHas('users', [
            'name' => 'Tabiana',
            'email' => 'tabianavitus@gmail.com',
            'role' => 'admin',
        ]);
        $this->assertTrue(Hash::check(
            'short',
            User::where('email', 'tabianavitus@gmail.com')->value('password')
        ));
    }

    public function test_users_can_sign_in_to_their_assigned_role_dashboard(): void
    {
        foreach ([
            'pastor' => 'pastor',
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
                    'role' => $role,
                ])
                ->assertRedirect(route('dashboard.'.$routeRole));

            $this->get(route('dashboard.'.$routeRole))->assertOk();
            $this->post(route('logout'), ['_token' => self::CSRF_TOKEN])->assertRedirect(route('home'));
        }
    }

    public function test_selected_role_must_match_the_account_role(): void
    {
        $user = User::factory()->create([
            'email' => 'deacon@example.com',
            'role' => 'deacon',
            'password' => 'secure-password',
        ]);

        $this->from(route('login'))
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('login.store'), [
                '_token' => self::CSRF_TOKEN,
                'email' => $user->email,
                'password' => 'secure-password',
                'role' => 'pastor',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_members_cannot_use_an_unavailable_member_role_login(): void
    {
        $user = User::factory()->create([
            'email' => 'member@example.com',
            'role' => 'member',
            'password' => 'secure-password',
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'secure-password',
                'role' => 'member',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('role');

        $this->assertGuest();
    }

    public function test_role_user_cannot_open_another_role_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'deacon']));

        $this->get(route('dashboard.deacon'))->assertOk();
        $this->get(route('dashboard.pastor'))->assertForbidden();
        $this->get(route('dashboard.treasurer'))->assertForbidden();
    }
}
