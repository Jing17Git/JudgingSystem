@extends('layouts.admin')

@section('title', 'Judge Details')

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-[var(--text-muted)] mb-2">
            <a href="{{ route('admin.judges.index') }}" class="hover:text-[var(--green-600)] transition-colors">Judge Management</a>
            <span>/</span>
            <span class="text-[var(--text-secondary)]">View Details</span>
        </div>
        <h1 class="page-title flex items-center gap-3">
            Judge Details
        </h1>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.judges.edit', $judge) }}" class="btn btn-green">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Judge
        </a>
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
        {{-- Judge Avatar & Name Header --}}
        <div class="flex items-center gap-4 pb-6 mb-6 border-b border-[var(--border-default)]">
            <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                {{ strtoupper(substr($judge->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-[var(--text-primary)]">{{ $judge->name }}</h2>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-xs font-mono font-semibold text-[var(--green-600)] bg-green-50 px-2 py-1 rounded-md">J{{ str_pad($judge->id, 3, '0', STR_PAD_LEFT) }}</span>
                    @if($judge->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-error">Inactive</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Details Grid --}}
        <div class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-1">Judge ID</p>
                    <p class="text-sm font-medium text-[var(--text-primary)]">J{{ str_pad($judge->id, 3, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-1">Full Name</p>
                    <p class="text-sm font-medium text-[var(--text-primary)]">{{ $judge->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-1">Username</p>
                    <p class="text-sm font-medium text-[var(--text-primary)] font-mono">{{ $judge->username }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-1">Status</p>
                    @if($judge->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-error">Inactive</span>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-1">Date Created</p>
                    <p class="text-sm font-medium text-[var(--text-primary)]">{{ $judge->created_at->format('M d, Y — h:i A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-1">Last Updated</p>
                    <p class="text-sm font-medium text-[var(--text-primary)]">{{ $judge->updated_at->format('M d, Y — h:i A') }}</p>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="pt-6 mt-6 border-t border-[var(--border-default)] flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.judges.edit', $judge) }}" class="btn btn-outline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>

            <form action="{{ route('admin.judges.toggle-status', $judge) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                @if($judge->is_active)
                    <button type="submit" class="btn btn-outline text-amber-600 border-amber-300 hover:bg-amber-50" onclick="return confirm('Are you sure you want to deactivate this judge?');">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Deactivate
                    </button>
                @else
                    <button type="submit" class="btn btn-outline text-green-600 border-green-300 hover:bg-green-50" onclick="return confirm('Are you sure you want to activate this judge?');">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Activate
                    </button>
                @endif
            </form>

            <form action="{{ route('admin.judges.destroy', $judge) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this judge account?\n\nThis action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Judge
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
