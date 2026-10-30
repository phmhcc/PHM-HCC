<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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
            'role' => ['required', Rule::in(array_diff(array_keys(config('roles')), ['member']))],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Email, password, and selected role do not match an account.'])
                ->withInput($request->only('email', 'role'));
        }

        $request->session()->regenerate();

        $roleRoute = str_replace('_', '-', Auth::user()->role);

        return redirect()->intended(
            Auth::user()->role === 'admin'
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
