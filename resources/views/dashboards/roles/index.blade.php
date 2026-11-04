@extends('layouts.dashboard')

@section('title', __('Role Permissions').' | PHM HCC')

@section('content')
<header class="resource-page-heading">
    <div>
        <h1>{{ __('Roles and permissions') }}</h1>
        <p>{{ __('Create roles and control their dashboard access.') }}</p>
    </div>
    <a class="dashboard-action" href="{{ route('dashboard.roles.create') }}">
        <i class="fas fa-plus" aria-hidden="true"></i> {{ __('Create role') }}
    </a>
</header>

<section class="content-box">
    <p style="margin-bottom:20px;">
        <a class="dashboard-action dashboard-action-secondary" href="{{ route('dashboard.resource.index', 'users') }}">
            <i class="fas fa-arrow-left" aria-hidden="true"></i> {{ __('Back to users') }}
        </a>
    </p>
    <div class="resource-records-heading">
        <h2>{{ __('Available roles') }}</h2>
        <span>{{ $roles->total() }} {{ __('roles') }}</span>
    </div>
    <div class="table-responsive">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>{{ __('Role') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Permissions') }}</th>
                    <th>{{ __('Assigned users') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>{{ __(str($role->name)->replace('_', ' ')->title()->toString()) }}</td>
                        <td>{{ $role->description ?: '—' }}</td>
                        <td>{{ $role->permissions_count }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td>
                            <div class="resource-row-actions">
                                <a class="dashboard-action dashboard-action-secondary" href="{{ route('dashboard.roles.show', $role) }}">{{ __('View') }}</a>
                                <a class="dashboard-action dashboard-action-secondary" href="{{ route('dashboard.roles.edit', $role) }}">{{ __('Edit') }}</a>
                                <form method="POST" action="{{ route('dashboard.roles.destroy', $role) }}" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button class="dashboard-action dashboard-action-danger" type="submit">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">{{ __('No editable roles have been added.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $roles->links() }}
</section>
@endsection
