@extends('layouts.admin')

@section('title', 'Judge Management')

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
            Judge Management
        </h1>
        <p class="page-subtitle">Manage judges who evaluate candidates during competitions</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.judges.create') }}" class="btn btn-green">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Judge
        </a>
    </div>
</div>

{{-- Search Bar --}}
<div class="panel animate-fade-in-up mb-6">
    <div class="panel-body py-3">
        <form action="{{ route('admin.judges.index') }}" method="GET" class="flex items-center gap-3">
            <div class="relative flex-1">
            
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by judge name or username..." class="form-input pl-10">
            </div>
            <button type="submit" class="btn btn-outline">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.judges.index') }}" class="btn btn-outline text-[var(--text-muted)]">Clear</a>
            @endif
        </form>
    </div>
</div>

{{-- Results info --}}
@if(request('search'))
    <div class="mb-4 text-sm text-[var(--text-muted)]">
        Showing results for "<strong class="text-[var(--text-primary)]">{{ request('search') }}</strong>" — {{ $judges->total() }} {{ Str::plural('judge', $judges->total()) }} found
    </div>
@endif

<div class="panel animate-fade-in-up delay-100">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Judge #</th>
                    <th>Judge Details</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($judges as $judge)
                    <tr>
                        <td>
                            <span class="text-xs font-semibold text-[var(--green-700)] bg-green-50 px-2.5 py-1 rounded-md border border-green-200 whitespace-nowrap">Judge {{ $judge->judge_number ?? $judge->id }}</span>
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                @if($judge->photo_url)
                                    <img src="{{ asset('storage/' . $judge->photo_url) }}" alt="{{ $judge->name }}" class="w-10 h-10 rounded-lg object-cover border border-[var(--border-default)] shadow-sm flex-shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-sm font-bold shadow-md flex-shrink-0">
                                        {{ strtoupper(substr($judge->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-[var(--text-primary)]">{{ $judge->name }}</p>
                                    <p class="text-xs text-[var(--text-muted)]">{{ $judge->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-[var(--text-secondary)] font-mono text-sm">{{ $judge->username }}</td>
                        <td>
                            @if($judge->is_active)
                                <span class="badge badge-success">Active</span>
               ------------------             @else
                                <span class="badge badge-error">Inactive</span>
                            @endif
                        </td>
                        <td class="text-sm text-[var(--text-muted)]">
                            {{ $judge->created_at->format('M d, Y') }}
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- View --}}
                                <a href="{{ route('admin.judges.show', $judge) }}" class="btn btn-outline btn-sm" title="View Judge">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('admin.judges.edit', $judge) }}" class="btn btn-outline btn-sm" title="Edit Judge">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                                {{-- Toggle Status --}}
                                <form action="{{ route('admin.judges.toggle-status', $judge) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($judge->is_active)
                                        <button type="submit" class="btn btn-outline btn-sm text-amber-600 border-amber-300 hover:bg-amber-50" title="Deactivate Judge" onclick="return confirm('Are you sure you want to deactivate this judge?');">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-outline btn-sm text-green-600 border-green-300 hover:bg-green-50" title="Activate Judge" onclick="return confirm('Are you sure you want to activate this judge?');">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    @endif
                                </form>

                                {{-- Delete --}}
                                <form action="{{ route('admin.judges.destroy', $judge) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this judge account?\n\nThis action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete Judge">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-16">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-[var(--text-muted)] opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                                <p class="text-[var(--text-muted)] font-medium">
                                    @if(request('search'))
                                        No judges found matching your search.
                                    @else
                                        No judges registered yet.
                                    @endif
                                </p>
                                @if(!request('search'))
                                    <a href="{{ route('admin.judges.create') }}" class="btn btn-green btn-sm mt-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add First Judge
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($judges->hasPages())
        <div class="panel-body border-t border-[var(--border-default)]">
            {{ $judges->links() }}
        </div>
    @endif
</div>
@endsection
