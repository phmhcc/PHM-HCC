<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardRolePermissionController extends Controller
{
    private const PERMISSION_MODULES = [
        'Dashboard' => ['dashboard' => ['view']],
        'Users' => ['users' => ['view', 'create', 'update', 'delete', 'view_all']],
        'Events' => ['events' => ['view', 'create', 'update', 'delete', 'view_all']],
        'Announcements' => ['announcements' => ['view', 'create', 'update', 'delete', 'view_all']],
        'Duties' => ['duties' => ['view', 'create', 'update', 'delete', 'view_all']],
        'Meetings' => ['meetings' => ['view', 'create', 'update', 'delete', 'view_all']],
        'Departments' => ['departments' => ['view', 'create', 'update', 'delete', 'view_all']],
        'Reports' => ['reports' => ['view', 'create', 'update', 'delete', 'view_all']],
        'Finance' => ['finance' => ['view', 'create', 'update', 'delete', 'view_all']],
    ];

    public function index(): View
    {
        $this->ensureAdmin();
        $roles = Role::query()
            ->where('name', '!=', 'admin')
            ->withCount(['permissions', 'users'])
            ->orderBy('name')
            ->paginate(15);

        return view('dashboards.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $this->ensureAdmin();
        $role = new Role();
        $permissionGroups = $this->permissionGroups();
        $selectedPermissions = [];
        $formAction = route('dashboard.roles.store');
        $formMethod = 'POST';
        $heading = 'Create role';

        return view('dashboards.roles.form', compact(
            'role',
            'permissionGroups',
            'selectedPermissions',
            'formAction',
            'formMethod',
            'heading'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin();
        $validated = $this->validateRole($request);

        $role = DB::transaction(function () use ($validated) {
            $role = Role::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);
            $this->syncPermissions($role, $validated['permissions'] ?? []);

            return $role;
        });

        return redirect()
            ->route('dashboard.roles.edit', $role)
            ->with('status', 'Role created.');
    }

    public function show(Role $role): View
    {
        $this->ensureAdmin();
        abort_if($role->name === 'admin', 404);
        $role->load('permissions');
        $assignedUsers = User::query()->where('role', $role->name)->count();

        return view('dashboards.roles.show', compact('role', 'assignedUsers'));
    }

    public function edit(Role $role): View
    {
        $this->ensureEditableRole($role);

        $permissionGroups = $this->permissionGroups();
        $rolePermissions = $role->permissions()->pluck('name')->all();
        $selectedPermissions = $this->expandWildcardPermissions($rolePermissions);
        $formAction = route('dashboard.roles.update', $role);
        $formMethod = 'PUT';
        $heading = 'Edit role';

        return view('dashboards.roles.form', compact(
            'role',
            'permissionGroups',
            'selectedPermissions',
            'formAction',
            'formMethod',
            'heading'
        ));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->ensureEditableRole($role);
        $validated = $this->validateRole($request, $role);
        $oldName = $role->name;

        DB::transaction(function () use ($role, $validated, $oldName) {
            $role->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            if ($oldName !== $role->name) {
                User::query()->where('role', $oldName)->update(['role' => $role->name]);
            }

            $this->syncPermissions($role, $validated['permissions'] ?? []);
        });

        return redirect()
            ->route('dashboard.roles.edit', $role)
            ->with('status', 'Role and permissions updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->ensureEditableRole($role);
        abort_if(
            User::query()->where('role', $role->name)->exists(),
            422,
            'This role is assigned to users. Reassign them before deleting the role.'
        );

        $role->delete();

        return redirect()
            ->route('dashboard.roles.index')
            ->with('status', 'Role deleted.');
    }

    private function permissionGroups(): array
    {
        $groups = [];

        foreach (self::PERMISSION_MODULES as $group => $modules) {
            foreach ($modules as $module => $actions) {
                foreach ($actions as $action) {
                    $name = $module.'.'.$action;
                    $groups[$group][] = [
                        'name' => $name,
                        'label' => Str::of($action)->replace('_', ' ')->title()->toString(),
                    ];
                }
            }
        }

        return $groups;
    }

    private function expandWildcardPermissions(array $permissions): array
    {
        $available = collect($this->permissionGroups())
            ->flatten(1)
            ->pluck('name');

        foreach ($permissions as $permission) {
            if (! str_ends_with($permission, '.*')) {
                continue;
            }

            $prefix = substr($permission, 0, -1);
            $permissions = array_merge(
                $permissions,
                $available->filter(fn (string $name) => str_starts_with($name, $prefix))->all()
            );
        }

        return array_values(array_unique(array_filter($permissions, fn (string $name) => $available->contains($name))));
    }

    private function validateRole(Request $request, ?Role $role = null): array
    {
        $request->merge([
            'name' => Str::slug((string) $request->input('name'), '_'),
        ]);
        $allowedPermissions = collect($this->permissionGroups())
            ->flatten(1)
            ->pluck('name')
            ->all();

        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z][a-z0-9_]*$/',
                'not_in:admin,member',
                Rule::unique('roles', 'name')->ignore($role?->id),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($allowedPermissions)],
        ]);
    }

    private function syncPermissions(Role $role, array $permissionNames): void
    {
        $permissionIds = collect($permissionNames)
            ->unique()
            ->map(fn (string $name) => Permission::firstOrCreate(['name' => $name])->id)
            ->all();

        $role->permissions()->sync($permissionIds);
    }

    private function ensureEditableRole(Role $role): void
    {
        $this->ensureAdmin();
        abort_if($role->name === 'admin', 403, 'Administrator role cannot be changed here.');
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()->role === 'admin', 403);
    }
}
