@extends('layouts.admin')

@section('title', 'Admin Management')

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Admin Management
        </h1>
        <p class="page-subtitle">Manage system administrator accounts</p>
    </div>
    <div>
        <a href="{{ route('admin.admins.create') }}" class="btn btn-green">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Admin
        </a>
    </div>
</div>

<div class="panel animate-fade-in-up delay-100">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Admin Details</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $admin)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-500 to-emerald-700 flex items-center justify-center text-white text-sm font-bold shadow-md">
                                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-[var(--text-primary)]">{{ $admin->name }}</p>
                                    <p class="text-xs text-[var(--text-muted)]">{{ $admin->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-[var(--text-secondary)]">{{ $admin->username }}</td>
                        <td>
                            @if($admin->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-error">Inactive</span>
                            @endif
                        </td>
                        <td class="text-sm text-[var(--text-muted)]">
                            {{ $admin->created_at->format('M d, Y') }}
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-outline btn-sm" title="Edit Admin">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                                @if($admin->id !== auth()->id())
                                    <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this admin account?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete Admin">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-[var(--text-muted)] py-10">
                            No administrators found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($admins->hasPages())
        <div class="panel-body border-t border-[var(--border-default)]">
            {{ $admins->links() }}
        </div>
    @endif
</div>
@endsection
