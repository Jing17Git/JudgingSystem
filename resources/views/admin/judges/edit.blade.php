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
        <form action="{{ route('admin.judges.update', $judge) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-input @error('name') border-[var(--danger)] @enderror" value="{{ old('name', $judge->name) }}" required minlength="3" maxlength="100" style="text-transform: capitalize;">
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="judge_number" class="form-label">Judge Number</label>
                    <input type="number" id="judge_number" name="judge_number" class="form-input @error('judge_number') border-[var(--danger)] @enderror" value="{{ old('judge_number', $judge->judge_number) }}" min="1" max="999" placeholder="e.g. 1">
                    <p class="text-xs text-[var(--text-muted)] mt-1">Numerical identifier displayed on all scorecards and tabulation tables (e.g. 1 for Judge 1).</p>
                    @error('judge_number')
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
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input @error('email') border-[var(--danger)] @enderror" value="{{ old('email', $judge->email) }}" placeholder="e.g. judge01@example.com">
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="picture" class="form-label">Picture</label>
                    <div x-data="{ preview: '{{ $judge->photo_url ? asset('storage/' . $judge->photo_url) : '' }}' }" class="space-y-3">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            {{-- Preview --}}
                            <div class="w-16 h-16 rounded-xl border border-dashed border-gray-300 flex items-center justify-center overflow-hidden bg-gray-50 flex-shrink-0 shadow-sm">
                                <template x-if="preview">
                                    <img :src="preview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!preview">
                                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </template>
                            </div>
                            <div class="flex-1 w-full">
                                <label for="picture" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 active:bg-gray-100 cursor-pointer shadow-sm transition-all group">
                                    <svg class="w-4 h-4 text-gray-500 group-hover:text-[var(--green-600)] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Change Photo
                                </label>
                                <input type="file" id="picture" name="picture" accept="image/jpeg,image/png,image/jpg,image/webp" class="sr-only" @change="preview = URL.createObjectURL($event.target.files[0])">
                                <p class="text-xs text-[var(--text-muted)] mt-1.5">PNG, JPG, JPEG or WEBP (Max: 2MB). Leave empty to keep current picture.</p>
                            </div>
                        </div>
                    </div>
                    @error('picture')
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
