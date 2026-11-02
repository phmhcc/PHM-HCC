@extends('layouts.dashboard')

@section('title', 'View Role | PHM HCC')
@section('page-heading', 'View Role')

@section('content')
<header class="resource-page-heading">
    <div>
        <span class="role-eyebrow">User management</span>
        <h1>{{ str($role->name)->replace('_', ' ')->title() }}</h1>
        <p>{{ $role->description ?: 'No description provided.' }}</p>
    </div>
    <div class="role-form-actions">
        <a class="dashboard-action dashboard-action-secondary" href="{{ route('dashboard.roles.index') }}">Back to roles</a>
        <a class="dashboard-action" href="{{ route('dashboard.roles.edit', $role) }}">Edit role</a>
    </div>
</header>

<section class="content-box">
    <div class="resource-records-heading">
        <h2>Role information</h2>
    </div>
    <p><strong>Assigned users:</strong> {{ $assignedUsers }}</p>
    <h2 class="resource-panel-title" style="margin-top:24px;">Permissions</h2>
    @forelse($role->permissions->groupBy(fn ($permission) => str_contains($permission->name, '.') ? str()->before($permission->name, '.') : 'Other') as $group => $permissions)
        <section style="padding:18px 0;border-bottom:1px solid #d8dee8;">
            <h3 style="margin-bottom:12px;color:#123b64;">{{ str($group)->replace('_', ' ')->title() }}</h3>
            <ul>
                @foreach($permissions as $permission)
                    <li style="margin:8px 0;">{{ str($permission->name)->replace('.', ' · ')->replace('_', ' ')->title() }}</li>
                @endforeach
            </ul>
        </section>
    @empty
        <p>This role has no assigned permissions.</p>
    @endforelse
</section>
@endsection
