@extends('layouts.dashboard')

@section('title', __($heading).' | PHM HCC')
@section('page-heading', __($heading))

@push('styles')
<style>
    .role-form { display: grid; gap: 24px; }
    .role-basic-fields { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px; max-width: 760px; }
    .role-permission-groups { display: grid; gap: 0; }
    .role-permission-group {
        display: grid;
        grid-template-columns: minmax(120px, 1fr) minmax(190px, 1.4fr) minmax(240px, 2fr);
        gap: 20px;
        align-items: start;
        padding: 22px 0;
        border-bottom: 1px solid #d8dee8;
    }
    .role-permission-group h3 { margin: 0; color: #123b64; font-size: 17px; }
    .role-permission-select-all,
    .role-permission-option { display: flex; gap: 10px; align-items: flex-start; color: #34445a; }
    .role-permission-select-all { font-weight: 600; }
    .role-permission-options { display: grid; grid-template-columns: repeat(auto-fit, minmax(155px, 1fr)); gap: 14px; }
    .role-permission-option input,
    .role-permission-select-all input { flex: 0 0 auto; width: 18px; height: 18px; margin: 1px 0 0; accent-color: #003366; }
    .role-form-actions { display: flex; flex-wrap: wrap; gap: 12px; }
    @media (max-width: 760px) {
        .role-permission-group { grid-template-columns: 1fr; gap: 14px; }
    }
</style>
@endpush

@section('content')
<header class="resource-page-heading">
    <div>
        <span class="role-eyebrow">{{ __('User Management') }}</span>
        <h1>{{ __($heading) }}</h1>
        <p>{{ __('Set the role details and choose the permissions assigned to it.') }}</p>
    </div>
    <a class="dashboard-action dashboard-action-secondary" href="{{ route('dashboard.roles.index') }}">
        <i class="fas fa-arrow-left" aria-hidden="true"></i> {{ __('Back to roles') }}
    </a>
</header>

<section class="content-box">
    <form method="POST" action="{{ $formAction }}" class="role-form">
        @csrf
        @if($formMethod === 'PUT')
            @method('PUT')
        @endif

        <div class="role-basic-fields">
            <label class="resource-field">
                <span>{{ __('Role name') }}</span>
                <input name="name" value="{{ old('name', $role->name) }}" required maxlength="50" autocomplete="off">
                <small>{{ __('Use letters, numbers, and spaces. Names are saved in lowercase with underscores.') }}</small>
                @error('name')<small class="form-error">{{ $message }}</small>@enderror
            </label>
            <label class="resource-field">
                <span>{{ __('Description') }}</span>
                <input name="description" value="{{ old('description', $role->description) }}" maxlength="255">
                @error('description')<small class="form-error">{{ $message }}</small>@enderror
            </label>
        </div>

        <h2 class="resource-panel-title"><i class="fas fa-key" aria-hidden="true"></i> {{ __('Permissions') }}</h2>
        <div class="role-permission-groups">
            @foreach($permissionGroups as $group => $permissions)
                @php($groupKey = 'permission-group-'.$loop->index)
                <section class="role-permission-group" aria-labelledby="{{ $groupKey }}-heading">
                    <h3 id="{{ $groupKey }}-heading">{{ __($group) }}</h3>
                    <label class="role-permission-select-all">
                        <input type="checkbox" data-select-group="{{ $groupKey }}">
                        <span>{{ __('Select all') }}</span>
                    </label>
                    <div class="role-permission-options">
                        @foreach($permissions as $permission)
                            <label class="role-permission-option">
                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission['name'] }}"
                                    data-permission-group="{{ $groupKey }}"
                                    @checked(in_array($permission['name'], old('permissions', $selectedPermissions), true))
                                >
                                <span>{{ __($permission['label']) }}</span>
                            </label>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
        @error('permissions')<p class="form-error">{{ $message }}</p>@enderror
        @error('permissions.*')<p class="form-error">{{ $message }}</p>@enderror

        <div class="role-form-actions">
            <button class="dashboard-action" type="submit">{{ __($formMethod === 'POST' ? 'Create role' : 'Save role') }}</button>
            <a class="dashboard-action dashboard-action-secondary" href="{{ route('dashboard.roles.index') }}">{{ __('Cancel') }}</a>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-select-group]').forEach((selectAll) => {
        const group = selectAll.dataset.selectGroup;
        const permissions = [...document.querySelectorAll(`[data-permission-group="${group}"]`)];

        const updateSelectAll = () => {
            const checkedCount = permissions.filter((permission) => permission.checked).length;
            selectAll.checked = checkedCount === permissions.length;
            selectAll.indeterminate = checkedCount > 0 && checkedCount < permissions.length;
        };

        selectAll.addEventListener('change', () => {
            permissions.forEach((permission) => {
                permission.checked = selectAll.checked;
            });
            updateSelectAll();
        });

        permissions.forEach((permission) => permission.addEventListener('change', updateSelectAll));
        updateSelectAll();
    });
</script>
@endpush
