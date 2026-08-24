@extends('admin.layout')

@section('title', 'Edit user')
@section('sidebar_active', 'users')

@section('content')
<h1 class="mb-4">Edit user</h1>
<div class="card" style="max-width: 500px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" id="userUpdateForm" data-original-role="{{ $user->role }}" data-confirm-mode="user-role">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New password <span class="text-muted">(leave blank to keep)</span></label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">At least 8 characters. Only fill to change.</small>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm new password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                    <option value="editor" {{ old('role', $user->role) === 'editor' ? 'selected' : '' }}>Editor (products, stock)</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (full access)</option>
                </select>
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">Update user</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
