{{-- resources/views/manager/roles/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Roles Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        {{-- <h3><i class="bi bi-shield-lock"></i> Roles List</h3> --}}
        <a href="{{ route('manager.roles.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add New Role
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
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
                            <th>Slug</th>
                            <th>Total Users</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $role->name }}</strong></td>
                                <td><code>{{ $role->slug }}</code></td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $role->users->count() }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('manager.roles.edit', $role) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>

                                    @if ($role->slug !== 'manager' && $role->slug !== 'user')
                                        <form action="{{ route('manager.roles.destroy', $role) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this role?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No roles found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
@endsection
