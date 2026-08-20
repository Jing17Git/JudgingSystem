@extends('layouts.admin')

@section('title', 'Add Admin')

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-[var(--text-muted)] mb-2">
            <a href="{{ route('admin.admins.index') }}" class="hover:text-[var(--green-600)] transition-colors">Admin Management</a>
            <span>/</span>
            <span class="text-[var(--text-secondary)]">Add New</span>
        </div>
        <h1 class="page-title flex items-center gap-3">
            Add Administrator
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
        <form action="{{ route('admin.admins.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-input @error('name') border-[var(--danger)] @enderror" value="{{ old('name') }}" required autofocus placeholder="e.g. John Doe" style="text-transform: capitalize;">
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-input @error('username') border-[var(--danger)] @enderror" value="{{ old('username') }}" required placeholder="e.g. admin.john">
                    <p class="text-xs text-[var(--text-muted)] mt-1">This will be used for logging in.</p>
                    @error('username')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input @error('email') border-[var(--danger)] @enderror" value="{{ old('email') }}" required placeholder="e.g. john@example.com">
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-input @error('password') border-[var(--danger)] @enderror" required placeholder="Min 6 characters">
                        @error('password')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required placeholder="Re-type password">
                    </div>
                </div>

                <div class="pt-4 border-t border-[var(--border-default)] flex justify-end gap-3">
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-green">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Save Admin
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
