{{-- resources/views/manager/users/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Users Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        {{-- <h3><i class="bi bi-people"></i> Users List</h3> --}}
        @if (auth()->user()->hasPermission('create-users'))
            <a href="{{ route('manager.users.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Add New User
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>

                            @if (auth()->user()->hasPermission('edit-users'))
                                <th>Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                </td>
                                <td>{{ $user->email }}</td>
                                @if (auth()->user()->hasPermission('edit-users') || auth()->user()->hasPermission('delete-users'))
                                    <td>
                                        @foreach ($user->roles as $role)
                                            <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                        @endforeach
                                        @if ($user->roles_count == 0)
                                            <span class="text-muted">No role</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (auth()->user()->hasPermission('edit-users'))
                                            <a href="{{ route('manager.users.edit', $user) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        @endif

                                        @if (auth()->user()->hasPermission('delete-users'))
                                            <form action="{{ route('manager.users.destroy', $user) }}" method="POST"
                                                class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this user?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        </div>
    </div>
@endsection
