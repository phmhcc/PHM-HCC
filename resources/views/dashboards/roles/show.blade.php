@extends('layouts.dashboard')

@section('title', __('View Role').' | PHM HCC')
@section('page-heading', __('View Role'))

@section('content')
<header class="resource-page-heading">
    <div>
        <span class="role-eyebrow">{{ __('User Management') }}</span>
        <h1>{{ __(str($role->name)->replace('_', ' ')->title()->toString()) }}</h1>
        <p>{{ $role->description ?: __('No description provided.') }}</p>
    </div>
    <div class="role-form-actions">
        <a class="dashboard-action dashboard-action-secondary" href="{{ route('dashboard.roles.index') }}">{{ __('Back to roles') }}</a>
        <a class="dashboard-action" href="{{ route('dashboard.roles.edit', $role) }}">{{ __('Edit role') }}</a>
    </div>
</header>

<section class="content-box">
    <div class="resource-records-heading">
        <h2>{{ __('Role information') }}</h2>
    </div>
    <p><strong>{{ __('Assigned users') }}:</strong> {{ $assignedUsers }}</p>
    <h2 class="resource-panel-title" style="margin-top:24px;">{{ __('Permissions') }}</h2>
    @forelse($role->permissions->groupBy(fn ($permission) => str_contains($permission->name, '.') ? str()->before($permission->name, '.') : 'Other') as $group => $permissions)
        <section style="padding:18px 0;border-bottom:1px solid #d8dee8;">
            <h3 style="margin-bottom:12px;color:#123b64;">{{ __(str($group)->replace('_', ' ')->title()->toString()) }}</h3>
            <ul>
                @foreach($permissions as $permission)
                    <li style="margin:8px 0;">{{ __(str($permission->name)->replace('.', ' · ')->replace('_', ' ')->title()->toString()) }}</li>
                @endforeach
            </ul>
        </section>
    @empty
        <p>{{ __('This role has no assigned permissions.') }}</p>
    @endforelse
</section>
@endsection
