@extends('layouts.judge')

@section('title', 'Judge Profile')

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-[var(--text-muted)] mb-1">
            <a href="{{ route('judge.dashboard') }}" class="hover:text-[var(--green-600)] transition-colors">Judge Dashboard</a>
            <span>/</span>
            <span class="text-[var(--text-secondary)]">Account</span>
        </div>
        <h1 class="page-title flex items-center gap-3">
            <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            My Profile
        </h1>
        <p class="page-subtitle">View and update your judge profile details and security settings.</p>
    </div>
</div>

<div class="panel max-w-2xl animate-fade-in-up">
    <div class="panel-body">
        <form action="{{ route('judge.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- User Avatar Header --}}
                <div class="flex items-center gap-4 pb-6 border-b border-[var(--border-default)]">
                    @if($judge->photo_url)
                        <img src="{{ asset('storage/' . $judge->photo_url) }}" alt="{{ $judge->name }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-green-200 shadow-md">
                    @else
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-3xl font-black shadow-md">
                            {{ strtoupper(substr($judge->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $judge->name }}</h2>
                        <p class="text-xs text-[var(--text-muted)] font-mono">Username: {{ $judge->username }}</p>
                        <span class="inline-block mt-1 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-green-100 text-green-700">Official Judge Account</span>
                    </div>
                </div>

                {{-- Full Name --}}
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-input @error('name') border-[var(--danger)] @enderror" value="{{ old('name', $judge->name) }}" required minlength="3" maxlength="100">
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Profile Picture --}}
                <div class="form-group">
                    <label for="picture" class="form-label">Profile Picture</label>
                    <div x-data="{ preview: '{{ $judge->photo_url ? asset('storage/' . $judge->photo_url) : '' }}' }" class="space-y-3">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
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
                                <input type="file" id="picture" name="picture" accept="image/jpeg,image/png,image/jpg,image/webp"
                                    class="block w-full text-sm text-[var(--text-secondary)]
                                           file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                           file:text-sm file:font-semibold file:bg-green-100 file:text-[var(--green-700)]
                                           hover:file:bg-green-200 cursor-pointer border border-[var(--border-default)] rounded-xl p-2 bg-white"
                                    @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => preview = e.target.result; reader.readAsDataURL(file); }">
                                <p class="text-xs text-[var(--text-muted)] mt-1.5">Leave empty to keep current picture. Max 2MB.</p>
                            </div>
                        </div>
                    </div>
                    @error('picture')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Change Section --}}
                <div class="p-5 rounded-2xl bg-gray-50 border border-gray-200 space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Change Password
                    </h3>
                    <p class="text-xs text-[var(--text-muted)]">Leave blank if you do not wish to change your password.</p>

                    <div class="form-group">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="form-input @error('current_password') border-[var(--danger)] @enderror" placeholder="Enter current password">
                        @error('current_password')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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

                {{-- Form Actions --}}
                <div class="pt-4 border-t border-[var(--border-default)] flex justify-end gap-3">
                    <button type="submit" class="btn btn-green">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
