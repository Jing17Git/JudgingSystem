@extends('layouts.admin')

@section('title', 'Edit Candidate')

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-[var(--text-muted)] mb-2">
            <a href="{{ route('admin.candidates.index') }}" class="hover:text-[var(--green-600)] transition-colors">Candidate Management</a>
            <span>/</span>
            <span class="text-[var(--text-secondary)]">Edit</span>
        </div>
        <h1 class="page-title flex items-center gap-3">
            Edit Candidate: {{ $candidate->display_name }}
        </h1>
    </div>
    <div>
        <a href="{{ route('admin.candidates.index') }}" class="btn btn-outline">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to List
        </a>
    </div>
</div>

<div class="panel animate-fade-in-up delay-100 max-w-2xl">
    <div class="panel-body">
        <form action="{{ route('admin.candidates.update', $candidate) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div class="form-group">
                    <label for="candidate_number" class="form-label">Candidate Number</label>
                    <input type="number" id="candidate_number" name="candidate_number" class="form-input @error('candidate_number') border-[var(--danger)] @enderror" value="{{ old('candidate_number', $candidate->candidate_number) }}" required min="1">
                    @error('candidate_number')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-input @error('full_name') border-[var(--danger)] @enderror" value="{{ old('full_name', $candidate->display_name) }}" required minlength="3" maxlength="100">
                    @error('full_name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="picture" class="form-label">Picture</label>
                    <div x-data="{ preview: '{{ $candidate->photo_url ? asset('storage/' . $candidate->photo_url) : '' }}' }" class="space-y-3">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            {{-- Preview --}}
                            <div class="w-16 h-16 rounded-xl border border-dashed border-gray-300 flex items-center justify-center overflow-hidden bg-gray-50 flex-shrink-0 shadow-sm">
                                <template x-if="preview">
                                    <img :src="preview" class="w-full h-full object-cover rounded-lg">
                                </template>
                                <template x-if="!preview">
                                    <div class="w-full h-full bg-gradient-to-br from-green-500 to-emerald-700 flex items-center justify-center text-white text-lg font-bold rounded-lg">
                                        {{ strtoupper(substr($candidate->display_name, 0, 1)) }}
                                    </div>
                                </template>
                            </div>

                            {{-- File Input --}}
                            <div class="flex-1 min-w-0 w-full">
                                <input type="file" id="picture" name="picture" accept="image/jpeg,image/jpg,image/png,image/webp"
                                    class="block w-full text-sm text-[var(--text-secondary)]
                                           file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                           file:text-sm file:font-semibold file:bg-green-100 file:text-[var(--green-700)]
                                           hover:file:bg-green-200 cursor-pointer border border-[var(--border-default)] rounded-xl p-2 bg-white"
                                    @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => preview = e.target.result; reader.readAsDataURL(file); }">
                                <p class="text-xs text-[var(--text-muted)] mt-1.5">Leave empty to keep current picture. Accepted: JPEG, PNG, WebP. Max: 2MB.</p>
                            </div>
                        </div>
                    </div>
                    @error('picture')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 border-t border-[var(--border-default)] flex justify-end gap-3">
                    <a href="{{ route('admin.candidates.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-green">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Update Candidate
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
