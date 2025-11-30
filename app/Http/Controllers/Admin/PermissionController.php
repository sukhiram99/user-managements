<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;

class PermissionController extends Controller
{
    protected $log;

    public function __construct()
    {
        $this->log = Log::channel('user_management');
    }

    public function index()
    {
        abort_if(!auth()->user()->hasPermission('view-permissions'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $permissions = Permission::withCount('roles')->paginate(10);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('create-permissions'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.permissions.create');
    }

    public function store(StorePermissionRequest $request)
    {
        abort_if(!auth()->user()->hasPermission('create-permissions'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        DB::beginTransaction();
        try {
            $permission = Permission::create([
                'name' => $request->name,
                'slug' => $request->slug,
            ]);

            DB::commit();

            $this->log->info('PERMISSION CREATED', [
                'created_by'    => auth()->user()->email,
                'permission_id' => $permission->id,
                'name'          => $permission->name,
                'slug'          => $permission->slug,
                'ip'            => $request->ip(),
            ]);

            return redirect()->route('manager.permissions.index')
                ->with('success', "Permission '{$permission->name}' created successfully!");

        } catch (\Exception $e) {
            DB::rollBack();

            $this->log->error('PERMISSION CREATION FAILED', [
                'created_by' => auth()->user()->email,
                'data'       => $request->only('name', 'slug'),
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            return back()->withInput()->withErrors(['error' => 'Failed to create permission. Slug might already exist.']);
        }
    }

    public function edit(Permission $permission)
    {
        abort_if(!auth()->user()->hasPermission('edit-permissions'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        abort_if(!auth()->user()->hasPermission('edit-permissions'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $oldName = $permission->name;
        $oldSlug = $permission->slug;

        DB::beginTransaction();
        try {
            $permission->update([
                'name' => $request->name,
                'slug' => $request->slug,
            ]);

            DB::commit();

            $this->log->info('PERMISSION UPDATED', [
                'updated_by'    => auth()->user()->email,
                'permission_id' => $permission->id,
                'old_name'      => $oldName,
                'new_name'      => $permission->name,
                'old_slug'      => $oldSlug,
                'new_slug'      => $permission->slug,
                'assigned_roles'=> $permission->roles()->count(),
                'ip'            => $request->ip(),
            ]);

            return redirect()->route('manager.permissions.index')
                ->with('success', "Permission '{$permission->name}' updated successfully!");

        } catch (\Exception $e) {
            DB::rollBack();

            $this->log->error('PERMISSION UPDATE FAILED', [
                'updated_by'    => auth()->user()->email,
                'permission_id' => $permission->id,
                'data'          => $request->only('name', 'slug'),
                'error'         => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors(['error' => 'Failed to update permission. Slug might be duplicate.']);
        }
    }

    public function destroy(Permission $permission)
    {
        abort_if(!auth()->user()->hasPermission('delete-permissions'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Prevent deleting permissions that are still assigned to roles
        if ($permission->roles()->exists()) {
            $this->log->warning('PERMISSION DELETE BLOCKED - IN USE', [
                'attempted_by'  => auth()->user()->email,
                'permission_id' => $permission->id,
                'permission'    => $permission->slug,
                'assigned_roles'=> $permission->roles()->count(),
            ]);

            return back()->withErrors([
                'error' => "Cannot delete permission '{$permission->name}'. It is currently assigned to one or more roles."
            ]);
        }

        DB::beginTransaction();
        try {
            $permissionData = $permission->only(['id', 'name', 'slug']);

            $permission->delete();

            DB::commit();

            $this->log->info('PERMISSION DELETED', [
                'deleted_by'    => auth()->user()->email,
                'permission'    => $permissionData,
                'ip'            => request()->ip(),
            ]);

            return back()->with('success', "Permission '{$permissionData['name']}' deleted successfully!");

        } catch (\Exception $e) {
            DB::rollBack();

            $this->log->error('PERMISSION DELETE FAILED', [
                'deleted_by'    => auth()->user()->email,
                'permission_id' => $permission->id,
                'error'         => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to delete permission.']);
        }
    }
}