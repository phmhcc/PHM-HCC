<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Email or password do not match an account.'])
                ->withInput($request->only('email'));
        }

        $user = Auth::user();
        if (! Gate::forUser($user)->allows('dashboard.view')) {
            Auth::logout();

            return redirect()->route('login')
                ->withErrors(['email' => 'This account does not have dashboard access. Please contact your administrator.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        $roleRoute = str_replace('_', '-', $user->role);

        return redirect()->intended(
            in_array($user->role, ['admin'], true)
                ? route('dashboard')
                : route('dashboard.'.$roleRoute)
        );
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
