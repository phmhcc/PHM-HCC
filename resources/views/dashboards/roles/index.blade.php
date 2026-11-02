@extends('layouts.dashboard')

@section('title', 'Role Permissions | PHM HCC')
@section('page-heading', 'User Management')

@section('content')
<header class="resource-page-heading">
    <div>
        <span class="role-eyebrow">User management</span>
        <h1>Roles and permissions</h1>
        <p>Create roles and control their dashboard access.</p>
    </div>
    <a class="dashboard-action" href="{{ route('dashboard.roles.create') }}">
        <i class="fas fa-plus" aria-hidden="true"></i> Create role
    </a>
</header>

<section class="content-box">
    <p style="margin-bottom:20px;">
        <a class="dashboard-action dashboard-action-secondary" href="{{ route('dashboard.resource.index', 'users') }}">
            <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to users
        </a>
    </p>
    <div class="resource-records-heading">
        <h2>Available roles</h2>
        <span>{{ $roles->total() }} roles</span>
    </div>
    <div class="table-responsive">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Description</th>
                    <th>Permissions</th>
                    <th>Assigned users</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>{{ str($role->name)->replace('_', ' ')->title() }}</td>
                        <td>{{ $role->description ?: '—' }}</td>
                        <td>{{ $role->permissions_count }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td>
                            <div class="resource-row-actions">
                                <a class="dashboard-action dashboard-action-secondary" href="{{ route('dashboard.roles.show', $role) }}">View</a>
                                <a class="dashboard-action dashboard-action-secondary" href="{{ route('dashboard.roles.edit', $role) }}">Edit</a>
                                <form method="POST" action="{{ route('dashboard.roles.destroy', $role) }}" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button class="dashboard-action dashboard-action-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No editable roles have been added.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $roles->links() }}
</section>
@endsection
