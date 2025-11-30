{{-- resources/views/manager/permissions/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Permissions Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        {{-- <h3>Permissions List</h3> --}}
        <a href="{{ route('manager.permissions.create') }}" class="btn btn-success">
            Add New Permission
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
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $permission->name }}</strong></td>
                                <td><code>{{ $permission->slug }}</code></td>
                                <td>{{ $permission->description ?? 'No description' }}</td>
                                <td>
                                    <a href="{{ route('manager.permissions.edit', $permission) }}"
                                        class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form action="{{ route('manager.permissions.destroy', $permission) }}" method="POST"
                                        class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this permission?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No permissions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $permissions->links() }}
            </div>
        </div>
    </div>
@endsection
