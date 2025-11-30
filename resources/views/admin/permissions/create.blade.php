{{-- resources/views/manager/permissions/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Create Permission')

@section('content')
    {{-- <h3>Create New Permission</h3> --}}

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form action="{{ route('manager.permissions.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Permission Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Slug (unique, lowercase)</label>
                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                        value="{{ old('slug') }}" required placeholder="e.g. create-posts, edit-users">
                    <div class="form-text">Use dashes, no spaces. Must be unique.</div>
                    @error('slug')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Description (optional)</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        Save Permission
                    </button>
                    <a href="{{ route('manager.permissions.index') }}" class="btn btn-secondary">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
