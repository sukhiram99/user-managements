@extends('layouts.app')
@section('title', 'Create User')

@section('content')
    {{-- <h3><i class="bi bi-person-plus"></i> Create New User</h3> --}}

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form action="{{ route('manager.users.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required>
                        @error('email')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label>Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            required>
                        @error('password')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label>Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label>Remark <span class="text-danger">*</span></label>
                        <input type="text" value="{{ old('remark') }}" name="remark"
                            class="form-control @error('remark') is-invalid @enderror" required>
                        @error('remark')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Assign Roles <span class="text-danger">*</span></label>
                    <div class="row">
                        @foreach ($roles as $role)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]"
                                        value="{{ $role->id }}" id="role{{ $role->id }}"
                                        {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="role{{ $role->id }}">
                                        {{ $role->name }}
                                        <code class="small">{{ $role->slug }}</code>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('roles')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Create User
                    </button>
                    <a href="{{ route('manager.users.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>
@endsection
