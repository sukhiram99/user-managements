{{-- resources/views/manager/roles/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Role: ' . $role->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        {{-- <h3>Edit Role: <span class="text-primary">{{ $role->name }}</span></h3> --}}
        <a href="{{ route('manager.roles.index') }}" class="btn btn-secondary">
            Back to Roles
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('manager.roles.update', $role) }}" method="POST">
        @csrf @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Role Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Role Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $role->name) }}" required>
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                                value="{{ old('slug', $role->slug) }}" required>
                            @error('slug')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            Permissions
                            <span class="badge bg-light text-dark ms-2">
                                {{ $role->permissions->count() }} assigned
                            </span>
                        </h5>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        @if ($permissions->count() > 0)
                            <div class="row">
                                @foreach ($permissions as $permission)
                                    <div class="col-12 mb-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                                value="{{ $permission->id }}" id="perm{{ $permission->id }}"
                                                {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm{{ $permission->id }}">
                                                <strong>{{ $permission->name }}</strong>
                                                <code class="text-muted small">{{ $permission->slug }}</code>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-4">
                                No permissions created yet.
                                <a href="{{ route('manager.permissions.create') }}">Create one</a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn btn-success btn-lg">
                Update Role & Save Permissions
            </button>

            <button type="button" class="btn btn-outline-primary"
                onclick="document.querySelectorAll('input[name=\'permissions[]\']').forEach(c => c.checked = true)">
                Select All
            </button>

            <button type="button" class="btn btn-outline-secondary"
                onclick="document.querySelectorAll('input[name=\'permissions[]\']').forEach(c => c.checked = false)">
                Unselect All
            </button>
        </div>
    </form>
@endsection
