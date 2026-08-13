@extends('layouts.admin')

@section('title', 'Candidate Management')

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Candidate Management
        </h1>
        <p class="page-subtitle">Manage contestants participating in the competition</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.candidates.create') }}" class="btn btn-green">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Candidate
        </a>
    </div>
</div>

{{-- Search Bar --}}
<div class="panel animate-fade-in-up mb-6">
    <div class="panel-body py-3">
        <form action="{{ route('admin.candidates.index') }}" method="GET" class="flex items-center gap-3">
            <div class="relative flex-1">
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or candidate number..." class="form-input pl-10">
            </div>
            <button type="submit" class="btn btn-outline">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.candidates.index') }}" class="btn btn-outline text-[var(--text-muted)]">Clear</a>
            @endif
        </form>
    </div>
</div>

@if(request('search'))
    <div class="mb-4 text-sm text-[var(--text-muted)]">
        Showing results for "<strong class="text-[var(--text-primary)]">{{ request('search') }}</strong>" — {{ $candidates->total() }} {{ Str::plural('candidate', $candidates->total()) }} found
    </div>
@endif

<div class="panel animate-fade-in-up delay-100">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Candidate No.</th>
                    <th>Picture</th>
                    <th>Full Name</th>
                    <th>Gender</th>
                    <th>Created Date</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($candidates as $candidate)
                    <tr>
                        <td>
                            <span class="text-xs font-mono font-semibold text-[var(--green-600)] bg-green-50 px-2.5 py-1 rounded-md">{{ $candidates->firstItem() + $loop->index }}</span>
                        </td>
                        <td>
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-emerald-700 text-white text-xs font-bold shadow-sm">
                                {{ $candidate->candidate_number }}
                            </span>
                        </td>
                        <td>
                            @if($candidate->photo_url)
                                <img src="{{ asset('storage/' . $candidate->photo_url) }}" alt="{{ $candidate->display_name }}" class="w-9 h-9 rounded-lg object-cover border border-[var(--border-default)] shadow-sm">
                            @else
                                <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 border border-dashed border-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="font-medium text-[var(--text-primary)]">{{ $candidate->display_name }}</td>
                        <td>
                            @if($candidate->gender === 'Male')
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">♂ Male</span>
                            @elseif($candidate->gender === 'Female')
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-pink-700 bg-pink-50 px-2 py-0.5 rounded-full">♀ Female</span>
                            @elseif($candidate->gender === 'Other')
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full">⚧ Other</span>
                            @else
                                <span class="text-xs text-[var(--text-muted)]">—</span>
                            @endif
                        </td>
                        <td class="text-sm text-[var(--text-muted)]">
                            {{ $candidate->created_at->format('M d, Y') }}
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- View --}}
                                <a href="{{ route('admin.candidates.show', $candidate) }}" class="btn btn-outline btn-sm" title="View Candidate">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('admin.candidates.edit', $candidate) }}" class="btn btn-outline btn-sm" title="Edit Candidate">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                                {{-- Delete --}}
                                <form action="{{ route('admin.candidates.destroy', $candidate) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this candidate?\n\nThis action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete Candidate">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="text-[var(--text-muted)] font-medium">
                                    @if(request('search'))
                                        No candidates found matching your search.
                                    @else
                                        No candidates registered yet.
                                    @endif
                                </p>
                                @if(!request('search'))
                                    <a href="{{ route('admin.candidates.create') }}" class="btn btn-green btn-sm mt-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add First Candidate
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($candidates->hasPages())
        <div class="panel-body border-t border-[var(--border-default)]">
            {{ $candidates->links() }}
        </div>
    @endif
</div>
@endsection
