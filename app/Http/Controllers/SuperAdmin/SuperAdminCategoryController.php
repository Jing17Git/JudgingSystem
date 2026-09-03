<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditRecord;
use App\Models\Category;
use App\Models\CriteriaSetting;
use App\Models\Pageant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SuperAdminCategoryController extends Controller
{
    /**
     * Helper to guarantee at least one Pageant record exists for foreign key constraint.
     */
    private function getOrCreateDefaultPageant(): Pageant
    {
        $pageant = Pageant::first();
        if (!$pageant) {
            $pageant = Pageant::create([
                'name' => 'CPSU Judging Pageant 2026',
                'description' => 'Official Campus Pageant Event',
                'venue' => 'Main Grand Hall',
                'event_date' => now(),
                'status' => 'active',
            ]);
        }
        return $pageant;
    }

    /**
     * Helper to sync criteria_settings table into categories table if empty.
     */
    private function syncCategoriesTable()
    {
        $pageant = $this->getOrCreateDefaultPageant();

        $settings = CriteriaSetting::orderBy('sort_order')->get();
        foreach ($settings as $setting) {
            Category::updateOrCreate(
                [
                    'pageant_id' => $pageant->id,
                    'name' => $setting->name,
                ],
                [
                    'description' => ucfirst($setting->stage) . ' Stage Judging Category',
                    'weight_percentage' => (float) $setting->percentage,
                    'sort_order' => $setting->sort_order,
                ]
            );
        }
    }

    /**
     * Display category management dashboard.
     */
    public function index()
    {
        // Ensure default pageant and categories sync
        $this->syncCategoriesTable();

        $preliminarySettings = CriteriaSetting::where('stage', 'preliminary')->orderBy('sort_order')->get();
        $finalSettings = CriteriaSetting::where('stage', 'final')->orderBy('sort_order')->get();

        $preliminaryTotal = $preliminarySettings->sum('percentage');
        $finalTotal = $finalSettings->sum('percentage');

        // Fetch DB categories directly from categories table
        $dbCategories = Category::orderBy('sort_order')->get();

        return view('super-admin.categories.management', compact(
            'preliminarySettings',
            'finalSettings',
            'preliminaryTotal',
            'finalTotal',
            'dbCategories'
        ));
    }

    /**
     * Store a newly created category in BOTH criteria_settings AND categories tables.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stage' => 'nullable|string|in:preliminary,final',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        // New categories are always added to the Pre-Judging (preliminary) stage
        // so they automatically appear in both admin and judge sidebars.
        $validated['stage'] = 'preliminary';

        $key = Str::slug($validated['name']);
        
        // Ensure unique key for stage
        $existingCount = CriteriaSetting::where('key', $key)->count();
        if ($existingCount > 0) {
            $key = $key . '-' . rand(100, 999);
        }

        $maxSort = CriteriaSetting::where('stage', $validated['stage'])->max('sort_order') ?? 0;

        // 1. Insert into criteria_settings table
        $setting = CriteriaSetting::create([
            'key' => $key,
            'name' => $validated['name'],
            'stage' => $validated['stage'],
            'percentage' => (float) $validated['percentage'],
            'sort_order' => $maxSort + 1,
        ]);

        // 2. Insert into `categories` table with valid foreign key pageant_id
        $pageant = $this->getOrCreateDefaultPageant();

        $category = Category::create([
            'pageant_id' => $pageant->id,
            'name' => $validated['name'],
            'description' => ucfirst($validated['stage']) . ' Judging Category',
            'weight_percentage' => (float) $validated['percentage'],
            'sort_order' => $maxSort + 1,
        ]);

        // Audit Log
        try {
            AuditRecord::create([
                'event_type' => 'category_created',
                'category' => 'system',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Super Admin',
                'user_role' => 'super-admin',
                'action_description' => "Created category '{$setting->name}' in {$setting->stage} stage ({$setting->percentage}%)",
                'ip_address' => $request->ip(),
                'status' => 'success',
            ]);
        } catch (\Throwable $e) {}

        return redirect()->route('super-admin.categories.management')
            ->with('success', "Category '{$setting->name}' inserted into categories table (ID #{$category->id}) and criteria settings successfully.");
    }

    /**
     * Update an existing category setting across both tables.
     */
    public function update(Request $request, CriteriaSetting $setting)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $oldName = $setting->name;

        $setting->update([
            'name' => $validated['name'],
            'percentage' => (float) $validated['percentage'],
            'sort_order' => $validated['sort_order'] ?? $setting->sort_order,
        ]);

        // Sync update in `categories` table
        try {
            Category::where('name', $oldName)->update([
                'name' => $validated['name'],
                'weight_percentage' => (float) $validated['percentage'],
                'sort_order' => $validated['sort_order'] ?? $setting->sort_order,
            ]);
        } catch (\Throwable $e) {}

        // Audit Log
        try {
            AuditRecord::create([
                'event_type' => 'category_updated',
                'category' => 'system',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Super Admin',
                'user_role' => 'super-admin',
                'action_description' => "Updated category '{$setting->name}' ({$setting->percentage}%)",
                'ip_address' => $request->ip(),
                'status' => 'success',
            ]);
        } catch (\Throwable $e) {}

        return redirect()->route('super-admin.categories.management')
            ->with('success', "Category '{$setting->name}' updated in categories table and criteria settings successfully.");
    }

    /**
     * Delete a category setting across both tables.
     */
    public function destroy(CriteriaSetting $setting)
    {
        $name = $setting->name;
        $setting->delete();

        // Delete from categories table as well
        try {
            Category::where('name', $name)->delete();
        } catch (\Throwable $e) {}

        // Audit Log
        try {
            AuditRecord::create([
                'event_type' => 'category_deleted',
                'category' => 'system',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Super Admin',
                'user_role' => 'super-admin',
                'action_description' => "Deleted category '{$name}'",
                'ip_address' => request()->ip(),
                'status' => 'warning',
                'risk_level' => 'warning',
            ]);
        } catch (\Throwable $e) {}

        return redirect()->route('super-admin.categories.management')
            ->with('success', "Category '{$name}' deleted from categories table successfully.");
    }

    /**
     * Bulk update percentages for a stage.
     */
    public function updatePercentages(Request $request)
    {
        $validated = $request->validate([
            'percentages' => 'required|array',
            'percentages.*' => 'required|numeric|min:0|max:100',
            'stage' => 'required|string|in:preliminary,final',
        ]);

        $total = array_sum($validated['percentages']);

        if (abs($total - 100) > 0.01) {
            return redirect()->back()
                ->withInput()
                ->with('error', "The total percentage for {$validated['stage']} stage must equal 100%. Current total: {$total}%.");
        }

        foreach ($validated['percentages'] as $key => $percentage) {
            $setting = CriteriaSetting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['percentage' => (float) $percentage]);

                // Also update categories table weight_percentage
                try {
                    Category::where('name', $setting->name)->update([
                        'weight_percentage' => (float) $percentage
                    ]);
                } catch (\Throwable $e) {}
            }
        }

        // Audit Log
        try {
            AuditRecord::create([
                'event_type' => 'percentages_updated',
                'category' => 'system',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Super Admin',
                'user_role' => 'super-admin',
                'action_description' => "Bulk updated {$validated['stage']} category percentage weights (Total 100%)",
                'ip_address' => $request->ip(),
                'status' => 'success',
            ]);
        } catch (\Throwable $e) {}

        return redirect()->route('super-admin.categories.management')
            ->with('success', ucfirst($validated['stage']) . " category percentage weights updated successfully!");
    }
}
