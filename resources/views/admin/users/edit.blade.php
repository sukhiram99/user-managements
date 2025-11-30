@extends('layouts.app')
@section('title', 'Edit User: ' . $user->name)

@section('content')
    {{-- <h3><i class="bi bi-pencil"></i> Edit User</h3> --}}

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form action="{{ route('manager.users.update', $user) }}" method="POST">
                @csrf @method('PUT')

                <!-- Same fields as create, but pre-filled -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}"
                            required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}"
                            required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>New Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" value="{{ old('password') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <label>Remark <span class="text-danger">*</span></label>
                    <input type="text" name="remark" class="form-control @error('remark') is-invalid @enderror"
                        value="{{ old('remark', $user->remark) }}" required>
                    @error('remark')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4 {{ !auth()->user()->hasPermission('create-users') ? 'd-none' : '' }}">
                    <label class="form-label">Roles</label>
                    <div class="row">
                        @foreach ($roles as $role)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]"
                                        value="{{ $role->id }}" id="role{{ $role->id }}"
                                        {{ in_array($role->id, $userRoles) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="role{{ $role->id }}">
                                        {{ $role->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update User
                </button>
                <a href="{{ route('manager.users.index') }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
@endsection
