@extends('layouts.admin')

@section('title', 'Candidate Details')

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-[var(--text-muted)] mb-2">
            <a href="{{ route('admin.candidates.index') }}" class="hover:text-[var(--green-600)] transition-colors">Candidate Management</a>
            <span>/</span>
            <span class="text-[var(--text-secondary)]">View Details</span>
        </div>
        <h1 class="page-title flex items-center gap-3">
            Candidate Details
        </h1>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.candidates.edit', $candidate) }}" class="btn btn-green">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Candidate
        </a>
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
        {{-- Candidate Photo & Name Header --}}
        <div class="flex items-center gap-4 pb-5 mb-6 border-b border-[var(--border-default)]">
            @if($candidate->photo_url)
                <img src="{{ asset('storage/' . $candidate->photo_url) }}" alt="{{ $candidate->display_name }}" class="w-14 h-14 rounded-xl object-cover border border-[var(--border-default)] shadow-md">
            @else
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-emerald-700 flex items-center justify-center text-white text-xl font-bold shadow-md">
                    {{ strtoupper(substr($candidate->display_name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h2 class="text-xl font-bold text-[var(--text-primary)]">{{ $candidate->display_name }}</h2>
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-xs font-mono font-semibold text-[var(--green-600)] bg-green-50 px-2 py-1 rounded-md">ID: {{ $candidate->id }}</span>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-white bg-gradient-to-r from-green-500 to-emerald-600 px-3 py-1 rounded-full shadow-sm">
                        Candidate #{{ $candidate->candidate_number }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Details Grid --}}
        <div class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-1">ID</p>
                    <p class="text-sm font-medium text-[var(--text-primary)]">{{ $candidate->id }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-1">Candidate Number</p>
                    <p class="text-sm font-medium text-[var(--text-primary)]">#{{ $candidate->candidate_number }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-1">Full Name</p>
                    <p class="text-sm font-medium text-[var(--text-primary)]">{{ $candidate->display_name }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-1">Picture</p>
                    @if($candidate->photo_url)
                        <span class="badge badge-success">Uploaded</span>
                    @else
                        <span class="badge badge-error">No photo</span>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-1">Gender</p>
                    @if($candidate->gender === 'Male')
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">♂ Male</span>
                    @elseif($candidate->gender === 'Female')
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-pink-700 bg-pink-50 px-2 py-0.5 rounded-full">♀ Female</span>
                    @elseif($candidate->gender === 'Other')
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full">⚧ Other</span>
                    @else
                        <span class="text-sm text-[var(--text-muted)]">Not specified</span>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-1">Date Created</p>
                    <p class="text-sm font-medium text-[var(--text-primary)]">{{ $candidate->created_at->format('M d, Y — h:i A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-1">Last Updated</p>
                    <p class="text-sm font-medium text-[var(--text-primary)]">{{ $candidate->updated_at->format('M d, Y — h:i A') }}</p>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="pt-6 mt-6 border-t border-[var(--border-default)] flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.candidates.edit', $candidate) }}" class="btn btn-outline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>

            <form action="{{ route('admin.candidates.destroy', $candidate) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this candidate?\n\nThis action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Candidate
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
