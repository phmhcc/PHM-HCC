<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class RoleDashboardController extends Controller
{
    public function show(string $role): View
    {
        $role = str_replace('-', '_', $role);
        abort_unless(isset(config('roles')[$role]) && $role !== 'admin' && $role !== 'member', 404);
        abort_unless(Auth::user()->role === $role || Auth::user()->role === 'admin', 403);

        $view = match ($role) {
            'department_leader' => 'department-leader',
            default => $role,
        };

        return view('dashboards.'.$view);
    }
}
