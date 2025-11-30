<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRemark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    protected $logChannel;

    public function __construct()
    {
        $this->logChannel = Log::channel('user_management');
    }

    public function index()
    {
        abort_if(!auth()->user()->hasPermission('view-users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userRole = auth()->user()->roles->first()->slug ?? null;

        $query = User::with('roles')->withCount('roles');

        if ($userRole === 'user') {
            $query->where('id', auth()->id());
        }

        $users = $query->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('create-users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

   public function store(StoreUserRequest $request)
    {
        abort_if(!auth()->user()->hasPermission('create-users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        DB::beginTransaction();
        try {
            // 1. Create the user
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'remark'   => $request->remark,
                'password' => Hash::make($request->password),
            ]);

            // 2. Sync roles
            $user->roles()->sync($request->roles ?? []);

            // 3. Log the initial remark (if provided) — this is the FIRST remark
            if (!empty(trim($request->remark ?? ''))) {
                UserRemark::create([
                    'user_id'         => $user->id,
                    'created_user_id' => auth()->id(),
                    'old_remark'      => null,                    // No previous remark
                    'new_remark'      => $request->remark,
                    'is_seen'         => false,
                    'seen_user_id'    => null,
                    'seen_at'         => null,
                ]);
            }

            DB::commit();

            // Success Log
            $this->logChannel->info('USER CREATED', [
                'created_by'      => auth()->user()->email,
                'user_id'         => $user->id,
                'name'            => $user->name,
                'email'           => $user->email,
                'remark'          => $request->remark,
                'initial_remark_logged' => !empty(trim($request->remark ?? '')),
                'roles'           => $user->roles->pluck('name')->toArray(),
                'ip'              => $request->ip(),
            ]);

            return redirect()->route('manager.users.index')
                ->with('success', 'User created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Error Log
            $this->logChannel->error('USER CREATION FAILED', [
                'created_by' => auth()->user()->email,
                'data'       => $request->except(['password', 'password_confirmation']),
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            $notification = array(
                'message' => 'Failed to create user. Please try again.',
                'alert-type' => 'error'
             );
           
            return back()
                ->withInput()
                ->withErrors($notification);
        }
    }

    public function edit(User $user)
    {
        abort_if(!auth()->user()->hasPermission('edit-users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

   public function update(UpdateUserRequest $request, User $user)
    {
        abort_if(!auth()->user()->hasPermission('edit-users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $oldData   = $user->only(['name', 'email', 'remark']);
        $oldRoles  = $user->roles->pluck('name')->toArray();
        $remarkChanged = $request->remark !== $oldData['remark']; // Only save if remark actually changed

        DB::beginTransaction();
        try {
            // 1. Update basic user fields
            $user->update([
                'name'     => $request->name,
                'email'    => $request->email,
                'remark'   => $request->remark,
                'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
            ]);

            // 2. Sync roles
            $user->roles()->sync($request->roles ?? []);

            // 3. Only create remark history if remark actually changed
            if ($remarkChanged && !empty(trim($request->remark ?? ''))) {
                UserRemark::create([
                    'user_id'         => $user->id,
                    'created_user_id' => auth()->id(),
                    'old_remark'      => $oldData['remark'],
                    'new_remark'      => $request->remark,
                    'is_seen'         => false,
                    'seen_user_id'    => null,
                    'seen_at'         => null,
                ]);
            }

            DB::commit();

            // Success Log
            $this->logChannel->info('USER UPDATED', [
                'updated_by'       => auth()->user()->email,
                'user_id'          => $user->id,
                'old_data'         => $oldData,
                'new_data'         => $user->only(['name', 'email', 'remark']),
                'old_roles'        => $oldRoles,
                'new_roles'        => $user->roles->pluck('name')->toArray(),
                'password_changed' => $request->filled('password'),
                'remark_changed'   => $remarkChanged,
                'ip'               => $request->ip(),
            ]);

            return redirect()->route('manager.users.index')
                ->with('success', 'User updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Error Log
            $this->logChannel->error('USER UPDATE FAILED', [
                'updated_by' => auth()->user()->email,
                'user_id'    => $user->id,
                'data'       => $request->except('password', 'password_confirmation'),
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update user. Please try again.']);
        }
    }

    public function destroy(User $user)
    {
        abort_if(!auth()->user()->hasPermission('delete-users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($user->email === 'manager@gmail.com') {
            $this->logChannel->warning('DELETE SUPER MANAGER ATTEMPT', [
                'attempted_by' => auth()->user()->email,
                'target_user'  => $user->email,
            ]);
            return back()->withErrors(['Cannot delete the super manager account']);
        }

        DB::beginTransaction();
        try {
            $userData = $user->only(['id', 'name', 'email']);
            $userRoles = $user->roles->pluck('name')->toArray();

            $user->delete();

            DB::commit();

            $this->logChannel->info('USER DELETED', [
                'deleted_by' => auth()->user()->email,
                'user'       => $userData,
                'roles'      => $userRoles,
                'ip'         => request()->ip(),
            ]);

            return back()->with('success', 'User deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            $this->logChannel->error('USER DELETE FAILED', [
                'deleted_by' => auth()->user()->email,
                'user_id'    => $user->id,
                'error'      => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to delete user.']);
        }
    }

    public function userRemarkDetails($remarkId)
    {
        abort_if(!auth()->user()->hasPermission('view-users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $remarks = UserRemark::with('creator')
            ->with(['creator', 'seener' , 'user'])
            ->where('id', $remarkId)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($remarks) {
            return response()->json([
                'status' => true,
                'data'   => $remarks,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No remarks found for this user.',
            ]);

        
    }

}

     public function closeRemarkDetails(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('view-users'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $remarkId = $request->input('id');

        $remarks = UserRemark::with('creator')
            ->with(['creator', 'seener' , 'user'])
            ->where('id', $remarkId)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($remarks) {

            $remarks->update([
                'is_seen'      => true,
                'seen_user_id' => auth()->id(),
                'seen_at'      => now(),
            ]);

            return response()->json([
                'status' => true,
                'message'   => 'Remark marked as seen.',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No remarks found for this user.',
            ]);

        
    }
}

}