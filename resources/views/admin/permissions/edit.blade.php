{{-- resources/views/manager/permissions/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Permission: ' . $permission->name)

@section('content')
    {{-- <h3>Edit Permission</h3> --}}

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form action="{{ route('manager.permissions.update', $permission) }}" method="POST">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Permission Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $permission->name) }}"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug', $permission->slug) }}"
                        required>
                    <div class="form-text">Cannot change if already used in many places.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $permission->description) }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        Update Permission
                    </button>
                    <a href="{{ route('manager.permissions.index') }}" class="btn btn-secondary">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
