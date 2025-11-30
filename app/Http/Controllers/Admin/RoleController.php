<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{
    protected $log;

    public function __construct()
    {
        $this->log = Log::channel('user_management');
    }

    public function index()
    {
        abort_if(!auth()->user()->hasPermission('view-roles'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::withCount('users')->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('create-roles'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        abort_if(!auth()->user()->hasPermission('create-roles'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
                'slug' => $request->slug,
            ]);

            // Attach permissions if provided
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            }

            DB::commit();

            $this->log->info('ROLE CREATED', [
                'created_by'    => auth()->user()->email,
                'role_id'       => $role->id,
                'role_name'     => $role->name,
                'role_slug'     => $role->slug,
                'permissions'   => $role->permissions->pluck('slug')->toArray(),
                'ip'            => $request->ip(),
            ]);

            return redirect()->route('manager.roles.index')
                ->with('success', "Role '{$role->name}' created successfully!");

        } catch (\Exception $e) {
            DB::rollBack();

            $this->log->error('ROLE CREATION FAILED', [
                'created_by' => auth()->user()->email,
                'data'       => $request->only('name', 'slug'),
                'permissions' => $request->permissions ?? [],
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Failed to create role. Please try again.']);
        }
    }

    public function edit(Role $role)
    {
        abort_if(!auth()->user()->hasPermission('edit-roles'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        abort_if(!auth()->user()->hasPermission('edit-roles'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $oldName = $role->name;
        $oldSlug = $role->slug;
        $oldPermissions = $role->permissions->pluck('slug')->toArray();

        DB::beginTransaction();
        try {
            $role->update([
                'name' => $request->name,
                'slug' => $request->slug,
            ]);

            $newPermissions = $request->permissions ?? [];
            $role->permissions()->sync($newPermissions);

            DB::commit();

            $this->log->info('ROLE UPDATED', [
                'updated_by'     => auth()->user()->email,
                'role_id'        => $role->id,
                'old_name'       => $oldName,
                'new_name'       => $role->name,
                'old_slug'       => $oldSlug,
                'new_slug'       => $role->slug,
                'old_permissions'=> $oldPermissions,
                'new_permissions'=> $role->permissions->pluck('slug')->toArray(),
                'ip'             => $request->ip(),
            ]);

            return redirect()->route('manager.roles.index')
                ->with('success', "Role '{$role->name}' updated successfully!");

        } catch (\Exception $e) {
            DB::rollBack();

            $this->log->error('ROLE UPDATE FAILED', [
                'updated_by' => auth()->user()->email,
                'role_id'    => $role->id,
                'data'       => $request->only('name', 'slug'),
                'permissions'=> $request->permissions ?? [],
                'error'      => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to update role.']);
        }
    }

    public function destroy(Role $role)
    {
        abort_if(!auth()->user()->hasPermission('delete-roles'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Protect core roles
        if (in_array($role->slug, ['manager', 'user', 'admin'])) {
            $this->log->warning('PROTECTED ROLE DELETE ATTEMPT', [
                'attempted_by' => auth()->user()->email,
                'role'         => $role->slug,
                'role_id'      => $role->id,
            ]);

            return back()->withErrors(['error' => 'Cannot delete protected system roles (manager/user/admin)']);
        }

        DB::beginTransaction();
        try {
            $roleData = $role->only(['id', 'name', 'slug']);
            $assignedUsers = $role->users()->count();
            $permissions = $role->permissions->pluck('slug')->toArray();

            $role->delete();

            DB::commit();

            $this->log->info('ROLE DELETED', [
                'deleted_by'     => auth()->user()->email,
                'role'           => $roleData,
                'assigned_users' => $assignedUsers,
                'permissions'    => $permissions,
                'ip'             => request()->ip(),
            ]);

            return back()->with('success', "Role '{$roleData['name']}' deleted successfully!");

        } catch (\Exception $e) {
            DB::rollBack();

            $this->log->error('ROLE DELETE FAILED', [
                'deleted_by' => auth()->user()->email,
                'role_id'    => $role->id,
                'error'      => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to delete role. It may be in use.']);
        }
    }
}