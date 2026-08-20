@extends('layouts.admin')

@section('title', 'Edit Admin')

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-[var(--text-muted)] mb-2">
            <a href="{{ route('admin.admins.index') }}" class="hover:text-[var(--green-600)] transition-colors">Admin Management</a>
            <span>/</span>
            <span class="text-[var(--text-secondary)]">Edit</span>
        </div>
        <h1 class="page-title flex items-center gap-3">
            Edit Administrator: {{ $admin->name }}
        </h1>
    </div>
    <div>
        <a href="{{ route('admin.admins.index') }}" class="btn btn-outline">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to List
        </a>
    </div>
</div>

<div class="panel animate-fade-in-up delay-100 max-w-2xl">
    <div class="panel-body">
        <form action="{{ route('admin.admins.update', $admin) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-input @error('name') border-[var(--danger)] @enderror" value="{{ old('name', $admin->name) }}" required style="text-transform: capitalize;">
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-input @error('username') border-[var(--danger)] @enderror" value="{{ old('username', $admin->username) }}" required>
                    @error('username')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input @error('email') border-[var(--danger)] @enderror" value="{{ old('email', $admin->email) }}" required>
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="p-4 rounded-xl bg-green-50 border border-green-200 mb-6">
                    <h3 class="text-sm font-semibold text-[var(--green-600)] mb-4">Change Password</h3>
                    <p class="text-xs text-[var(--text-muted)] mb-4">Leave these fields blank if you do not wish to change the password.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group mb-0">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" id="password" name="password" class="form-input @error('password') border-[var(--danger)] @enderror" placeholder="Min 6 characters">
                            @error('password')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Re-type new password">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-[var(--border-default)] flex justify-end gap-3">
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-green">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
