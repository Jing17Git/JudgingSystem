@extends('layouts.admin')

@section('title', 'Edit Judge')

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-[var(--text-muted)] mb-2">
            <a href="{{ route('admin.judges.index') }}" class="hover:text-[var(--green-600)] transition-colors">Judge Management</a>
            <span>/</span>
            <span class="text-[var(--text-secondary)]">Edit</span>
        </div>
        <h1 class="page-title flex items-center gap-3">
            Edit Judge: {{ $judge->name }}
        </h1>
    </div>
    <div>
        <a href="{{ route('admin.judges.index') }}" class="btn btn-outline">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to List
        </a>
    </div>
</div>

<div class="panel animate-fade-in-up delay-100 max-w-2xl">
    <div class="panel-body">
        <form action="{{ route('admin.judges.update', $judge) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-input @error('name') border-[var(--danger)] @enderror" value="{{ old('name', $judge->name) }}" required minlength="3" maxlength="100">
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-input @error('username') border-[var(--danger)] @enderror" value="{{ old('username', $judge->username) }}" required minlength="4" maxlength="50">
                    @error('username')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="is_active" class="form-label">Status</label>
                    <select id="is_active" name="is_active" class="form-input @error('is_active') border-[var(--danger)] @enderror">
                        <option value="1" {{ old('is_active', $judge->is_active) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $judge->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <p class="text-xs text-[var(--text-muted)] mt-1">Inactive judges cannot log in or submit scores.</p>
                    @error('is_active')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="p-4 rounded-xl bg-green-50 border border-green-200 mb-6">
                    <h3 class="text-sm font-semibold text-[var(--green-600)] mb-4">Change Password</h3>
                    <p class="text-xs text-[var(--text-muted)] mb-4">Leave these fields blank if you do not wish to change the password.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group mb-0">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" id="password" name="password" class="form-input @error('password') border-[var(--danger)] @enderror" placeholder="Min 8 characters">
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
                    <a href="{{ route('admin.judges.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-green">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Update Judge
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
