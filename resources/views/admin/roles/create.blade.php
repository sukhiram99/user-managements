{{-- resources/views/manager/roles/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create New Role')

@section('content')
    {{-- <h3><i class="bi bi-plus-circle"></i> Create New Role</h3> --}}

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form action="{{ route('manager.roles.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Role Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Slug (unique identifier)</label>
                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                        value="{{ old('slug') }}" required>
                    <div class="form-text">Use lowercase, e.g., manager, editor, guest</div>
                    @error('slug')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Save Role
                    </button>
                    <a href="{{ route('manager.roles.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
