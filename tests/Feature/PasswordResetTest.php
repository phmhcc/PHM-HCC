<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private const CSRF_TOKEN = 'test-token';

    public function test_guest_can_view_password_recovery_pages(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Reset your password')
            ->assertSee('src="'.asset('Images/images.jpg').'"', false)
            ->assertSee(route('password.email'), false);

        $this->get(route('password.reset', ['token' => 'sample-token']))
            ->assertOk()
            ->assertSee('Choose a new password')
            ->assertSee('src="'.asset('Images/images.jpg').'"', false)
            ->assertSee(route('password.store'), false);
    }

    public function test_password_reset_link_is_sent_for_an_existing_account(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'member@example.com']);

        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('password.email'), [
                '_token' => self::CSRF_TOKEN,
                'email' => $user->email,
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_can_be_reset_with_a_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'member@example.com',
            'password' => 'old-password',
        ]);
        $token = Password::createToken($user);

        $this->withSession(['_token' => self::CSRF_TOKEN])
            ->post(route('password.store'), [
                '_token' => self::CSRF_TOKEN,
                'token' => $token,
                'email' => $user->email,
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
    }
}
