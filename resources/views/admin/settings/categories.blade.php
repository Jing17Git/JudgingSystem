@extends('layouts.admin')

@section('title', 'Manage Categories — Settings')

@section('content')
<div x-data="{ activeStage: 'preliminary', showAddModal: false, editModal: false, activeEdit: {} }" class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-[var(--text-muted)] mb-1">
                <a href="{{ route('admin.settings.index') }}" class="hover:text-[var(--green-600)] transition-colors">Settings</a>
                <span>/</span>
                <span class="text-[var(--text-secondary)]">Manage Categories</span>
            </div>
            <h1 class="page-title text-2xl font-bold text-[var(--text-primary)] flex items-center gap-2.5">
                <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Manage Judging Categories
            </h1>
            <p class="page-subtitle text-sm text-[var(--text-muted)] mt-1">Configure pageant judging categories, percentage weights, and scoring criteria stages.</p>
        </div>

        <div class="flex items-center gap-3">
            <button @click="showAddModal = true" class="btn btn-green shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add Category</span>
            </button>
        </div>
    </div>

    {{-- Navigation Tabs between Settings Sections --}}
    <div class="flex flex-nowrap overflow-x-auto items-center gap-2 border-b border-[var(--border-default)] pb-3 hide-scrollbar">
        <a href="{{ route('admin.settings.preliminary') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Preliminary Criteria
        </a>
        <a href="{{ route('admin.settings.final') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Final Criteria
        </a>
        <a href="{{ route('admin.settings.categories') }}"
           class="px-4 py-2 rounded-xl text-sm font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-2 shadow-sm whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
            Manage Categories
        </a>
        <a href="{{ route('admin.settings.judge-scores') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Judge Score Sheets
        </a>
        <a href="{{ route('admin.settings.audit_record') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Audit Record
        </a>
        <a href="{{ route('admin.cache.index') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Cache Management
        </a>
        <a href="{{ route('admin.logs.index') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Logs Management
        </a>
    </div>

    {{-- Stage Selector Tabs --}}
    <div class="flex border-b border-[var(--border-default)]">
        <button @click="activeStage = 'preliminary'" 
                :class="activeStage === 'preliminary' ? 'border-emerald-600 text-emerald-700 font-bold border-b-2' : 'text-[var(--text-muted)] font-medium'"
                class="px-6 py-3 text-sm flex items-center gap-2 transition-colors cursor-pointer">
            <span>✨ Preliminary Stage</span>
            <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-800 font-mono font-bold">{{ $preliminaryTotal }}%</span>
        </button>

        <button @click="activeStage = 'final'" 
                :class="activeStage === 'final' ? 'border-emerald-600 text-emerald-700 font-bold border-b-2' : 'text-[var(--text-muted)] font-medium'"
                class="px-6 py-3 text-sm flex items-center gap-2 transition-colors cursor-pointer">
            <span>🏆 Final Stage</span>
            <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800 font-mono font-bold">{{ $finalTotal }}%</span>
        </button>

        <button @click="activeStage = 'categories_table'" 
                :class="activeStage === 'categories_table' ? 'border-emerald-600 text-emerald-700 font-bold border-b-2' : 'text-[var(--text-muted)] font-medium'"
                class="px-6 py-3 text-sm flex items-center gap-2 transition-colors cursor-pointer">
            <span>🗄️ Categories DB Table</span>
            <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-800 font-mono font-bold">{{ $dbCategories->count() }} Records</span>
        </button>
    </div>

    {{-- Preliminary Stage Panel --}}
    <div x-show="activeStage === 'preliminary'" class="space-y-6">
        {{-- Total Weight Status Banner --}}
        <div class="panel p-4 flex flex-col sm:flex-row items-center justify-between gap-4 {{ abs($preliminaryTotal - 100) < 0.01 ? 'bg-emerald-50/70 border-emerald-200' : 'bg-amber-50/70 border-amber-200' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-lg {{ abs($preliminaryTotal - 100) < 0.01 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ abs($preliminaryTotal - 100) < 0.01 ? '✓' : '⚠️' }}
                </div>
                <div>
                    <h4 class="text-sm font-bold text-[var(--text-primary)]">Preliminary Total Weight: <span class="font-mono text-base">{{ $preliminaryTotal }}%</span></h4>
                    <p class="text-xs text-[var(--text-muted)] mt-0.5">
                        {{ abs($preliminaryTotal - 100) < 0.01 ? 'Preliminary category weights equal exactly 100%.' : 'Total must equal 100% for balanced score tabulations.' }}
                    </p>
                </div>
            </div>
            <div class="w-full sm:w-48">
                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                    <div class="h-2.5 rounded-full transition-all duration-500 {{ abs($preliminaryTotal - 100) < 0.01 ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ min($preliminaryTotal, 100) }}%"></div>
                </div>
            </div>
        </div>

        {{-- Categories Table & Form --}}
        <form action="{{ route('admin.settings.categories.percentages') }}" method="POST" class="panel">
            @csrf
            <input type="hidden" name="stage" value="preliminary">
            
            <div class="panel-header flex items-center justify-between">
                <h3 class="panel-title">Preliminary Judging Categories</h3>
                <button type="submit" class="btn btn-green btn-sm">Save Percentages</button>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Category Name</th>
                            <th>Key Identifier</th>
                            <th>Percentage Weight</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($preliminarySettings as $setting)
                            <tr>
                                <td class="font-mono font-medium text-[var(--text-muted)]">#{{ $setting->sort_order }}</td>
                                <td>
                                    <span class="font-bold text-[var(--text-primary)]">{{ $setting->name }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-info font-mono text-[11px]">{{ $setting->key }}</span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <input type="number" 
                                               name="percentages[{{ $setting->key }}]" 
                                               value="{{ (int) $setting->percentage }}" 
                                               min="0" max="100" 
                                               class="form-input w-24 font-mono font-bold text-center py-1">
                                        <span class="text-xs text-[var(--text-muted)] font-bold">%</span>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" 
                                                @click="editModal = true; activeEdit = { id: '{{ $setting->id }}', name: '{{ $setting->name }}', percentage: '{{ $setting->percentage }}', sort_order: '{{ $setting->sort_order }}' }"
                                                class="btn btn-outline btn-sm text-blue-600 border-blue-200 hover:bg-blue-50">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.settings.categories.destroy', $setting->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete category {{ $setting->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-[var(--text-muted)]">
                                    No preliminary categories configured.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>

    {{-- Final Stage Panel --}}
    <div x-show="activeStage === 'final'" class="space-y-6">
        {{-- Total Weight Status Banner --}}
        <div class="panel p-4 flex flex-col sm:flex-row items-center justify-between gap-4 {{ abs($finalTotal - 100) < 0.01 ? 'bg-emerald-50/70 border-emerald-200' : 'bg-amber-50/70 border-amber-200' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-lg {{ abs($finalTotal - 100) < 0.01 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ abs($finalTotal - 100) < 0.01 ? '✓' : '⚠️' }}
                </div>
                <div>
                    <h4 class="text-sm font-bold text-[var(--text-primary)]">Final Stage Total Weight: <span class="font-mono text-base">{{ $finalTotal }}%</span></h4>
                    <p class="text-xs text-[var(--text-muted)] mt-0.5">
                        {{ abs($finalTotal - 100) < 0.01 ? 'Final stage category weights equal exactly 100%.' : 'Total must equal 100% for final tabulation.' }}
                    </p>
                </div>
            </div>
            <div class="w-full sm:w-48">
                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                    <div class="h-2.5 rounded-full transition-all duration-500 {{ abs($finalTotal - 100) < 0.01 ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ min($finalTotal, 100) }}%"></div>
                </div>
            </div>
        </div>

        {{-- Categories Table & Form --}}
        <form action="{{ route('admin.settings.categories.percentages') }}" method="POST" class="panel">
            @csrf
            <input type="hidden" name="stage" value="final">
            
            <div class="panel-header flex items-center justify-between">
                <h3 class="panel-title">Final Judging Stage Weights</h3>
                <button type="submit" class="btn btn-green btn-sm">Save Percentages</button>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Category Name</th>
                            <th>Key Identifier</th>
                            <th>Percentage Weight</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($finalSettings as $setting)
                            <tr>
                                <td class="font-mono font-medium text-[var(--text-muted)]">#{{ $setting->sort_order }}</td>
                                <td>
                                    <span class="font-bold text-[var(--text-primary)]">{{ $setting->name }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-info font-mono text-[11px]">{{ $setting->key }}</span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <input type="number" 
                                               name="percentages[{{ $setting->key }}]" 
                                               value="{{ (int) $setting->percentage }}" 
                                               min="0" max="100" 
                                               class="form-input w-24 font-mono font-bold text-center py-1">
                                        <span class="text-xs text-[var(--text-muted)] font-bold">%</span>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" 
                                                @click="editModal = true; activeEdit = { id: '{{ $setting->id }}', name: '{{ $setting->name }}', percentage: '{{ $setting->percentage }}', sort_order: '{{ $setting->sort_order }}' }"
                                                class="btn btn-outline btn-sm text-blue-600 border-blue-200 hover:bg-blue-50">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.settings.categories.destroy', $setting->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete category {{ $setting->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-[var(--text-muted)]">
                                    No final categories configured.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>

    {{-- Categories DB Table Panel --}}
    <div x-show="activeStage === 'categories_table'" class="panel space-y-4">
        <div class="panel-header flex items-center justify-between">
            <div>
                <h3 class="panel-title">Categories Table (`categories`)</h3>
                <p class="text-xs text-[var(--text-muted)] mt-0.5">Live records in the `categories` database table.</p>
            </div>
            <span class="badge badge-success font-mono font-bold">{{ $dbCategories->count() }} Total Records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pageant ID</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Weight (%)</th>
                        <th>Sort Order</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dbCategories as $catItem)
                        <tr>
                            <td class="font-mono text-xs">#{{ $catItem->id }}</td>
                            <td class="font-mono text-xs">{{ $catItem->pageant_id }}</td>
                            <td class="font-bold text-[var(--text-primary)]">{{ $catItem->name }}</td>
                            <td class="text-xs text-[var(--text-muted)]">{{ $catItem->description ?? 'N/A' }}</td>
                            <td>
                                <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-emerald-100 text-emerald-800">
                                    {{ (int) $catItem->weight_percentage }}%
                                </span>
                            </td>
                            <td class="font-mono text-xs">#{{ $catItem->sort_order }}</td>
                            <td class="text-xs text-[var(--text-muted)]">{{ $catItem->created_at?->format('M d, Y H:i') ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-[var(--text-muted)]">
                                No records found in the `categories` database table.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Category Modal --}}
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs" style="display: none;">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-[var(--border-default)]">
            <h3 class="text-lg font-bold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                <span>➕</span> Add New Category
            </h3>
            
            <form action="{{ route('admin.settings.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label">Category Name</label>
                    <input type="text" name="name" placeholder="e.g. Swimwear & Fitness" class="form-input" required>
                </div>

                <div>
                    <label class="form-label">Judging Stage</label>
                    <input type="hidden" name="stage" value="preliminary">
                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-50 border border-emerald-200">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-semibold text-emerald-800">Pre-Judging (Preliminary)</span>
                        <span class="ml-auto text-[11px] text-emerald-600 font-semibold">Always default</span>
                    </div>
                </div>

                <div>
                    <label class="form-label">Initial Weight Percentage (%)</label>
                    <input type="number" name="percentage" min="0" max="100" value="10" class="form-input font-mono font-bold" required>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3">
                    <button type="button" @click="showAddModal = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-green">Create Category</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Category Modal --}}
    <div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs" style="display: none;">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-[var(--border-default)]">
            <h3 class="text-lg font-bold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                <span>✏️</span> Edit Category
            </h3>
            
            <form :action="'{{ url('/admin/settings/categories') }}/' + activeEdit.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="form-label">Category Name</label>
                    <input type="text" name="name" x-model="activeEdit.name" class="form-input" required>
                </div>

                <div>
                    <label class="form-label">Weight Percentage (%)</label>
                    <input type="number" name="percentage" min="0" max="100" x-model="activeEdit.percentage" class="form-input font-mono font-bold" required>
                </div>

                <div>
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" min="1" x-model="activeEdit.sort_order" class="form-input font-mono">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3">
                    <button type="button" @click="editModal = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-green">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
