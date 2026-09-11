<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditRecord;
use App\Models\Candidate;
use App\Models\Category;
use App\Models\CriteriaSetting;
use App\Models\CustomCategoryScore;
use App\Models\FitnessScore;
use App\Models\IndigenousAttireScore;
use App\Models\JudgeCategorySubmission;
use App\Models\Pageant;
use App\Models\ProductionScore;
use App\Models\QaScore;
use App\Models\TraditionalAttireScore;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SuperCategoryController extends Controller
{
    /**
     * Helper to guarantee at least one Pageant record exists for foreign key constraint.
     */
    private function getOrCreateDefaultPageant(): Pageant
    {
        $pageant = Pageant::first();
        if (! $pageant) {
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
                    'description' => ucfirst($setting->stage).' Stage Judging Category',
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
        $this->syncCategoriesTable();

        $preliminarySettings = CriteriaSetting::where('stage', 'preliminary')->orderBy('sort_order')->get();
        $finalSettings = CriteriaSetting::where('stage', 'final')->orderBy('sort_order')->get();

        $preliminaryTotal = $preliminarySettings->sum('percentage');
        $finalTotal = $finalSettings->sum('percentage');

        $dbCategories = Category::orderBy('sort_order')->get();

        return view('admin.categories.management', compact(
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
        $validated['stage'] = 'preliminary';

        $key = Str::slug($validated['name']);

        $existingCount = CriteriaSetting::where('key', $key)->count();
        if ($existingCount > 0) {
            $key = $key.'-'.rand(100, 999);
        }

        $maxSort = CriteriaSetting::where('stage', $validated['stage'])->max('sort_order') ?? 0;

        $setting = CriteriaSetting::create([
            'key' => $key,
            'name' => $validated['name'],
            'stage' => $validated['stage'],
            'percentage' => (float) $validated['percentage'],
            'sort_order' => $maxSort + 1,
        ]);

        $pageant = $this->getOrCreateDefaultPageant();
        $category = Category::create([
            'pageant_id' => $pageant->id,
            'name' => $validated['name'],
            'description' => ucfirst($validated['stage']).' Judging Category',
            'weight_percentage' => (float) $validated['percentage'],
            'sort_order' => $maxSort + 1,
        ]);

        try {
            AuditRecord::create([
                'event_type' => 'category_created',
                'category' => 'system',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Admin',
                'user_role' => 'admin',
                'action_description' => "Created category '{$setting->name}' in {$setting->stage} stage ({$setting->percentage}%)",
                'ip_address' => $request->ip(),
                'status' => 'success',
            ]);
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.categories.management')
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

        try {
            Category::where('name', $oldName)->update([
                'name' => $validated['name'],
                'weight_percentage' => (float) $validated['percentage'],
                'sort_order' => $validated['sort_order'] ?? $setting->sort_order,
            ]);
        } catch (\Throwable $e) {
        }

        try {
            AuditRecord::create([
                'event_type' => 'category_updated',
                'category' => 'system',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Admin',
                'user_role' => 'admin',
                'action_description' => "Updated category '{$setting->name}' ({$setting->percentage}%)",
                'ip_address' => $request->ip(),
                'status' => 'success',
            ]);
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.categories.management')
            ->with('success', "Category '{$setting->name}' updated in categories table and criteria settings successfully.");
    }

    /**
     * Delete a category setting across both tables.
     */
    public function destroy(CriteriaSetting $setting)
    {
        $name = $setting->name;
        $setting->delete();

        try {
            Category::where('name', $name)->delete();
        } catch (\Throwable $e) {
        }

        try {
            AuditRecord::create([
                'event_type' => 'category_deleted',
                'category' => 'system',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Admin',
                'user_role' => 'admin',
                'action_description' => "Deleted category '{$name}'",
                'ip_address' => request()->ip(),
                'status' => 'warning',
                'risk_level' => 'warning',
            ]);
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.categories.management')
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
                try {
                    Category::where('name', $setting->name)->update([
                        'weight_percentage' => (float) $percentage,
                    ]);
                } catch (\Throwable $e) {
                }
            }
        }

        try {
            AuditRecord::create([
                'event_type' => 'percentages_updated',
                'category' => 'system',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Admin',
                'user_role' => 'admin',
                'action_description' => "Bulk updated {$validated['stage']} category percentage weights (Total 100%)",
                'ip_address' => $request->ip(),
                'status' => 'success',
            ]);
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.categories.management')
            ->with('success', ucfirst($validated['stage']).' category percentage weights updated successfully!');
    }

    /**
     * Toggle the enabled/disabled state of a category.
     */
    public function toggleEnabled(Request $request, CriteriaSetting $setting)
    {
        $setting->update(['is_enabled' => ! $setting->is_enabled]);

        $isUnlocked = (bool) $setting->is_enabled;
        $stateText = $isUnlocked ? 'UNLOCKED' : 'LOCKED';

        // When admin unlocks a category, unlock any finalized judge submissions so judges can vote and edit scores
        if ($isUnlocked) {
            $catSlug = strtolower(str_replace('_', '-', $setting->key));
            $catKey = strtolower(str_replace('-', '_', $setting->key));
            JudgeCategorySubmission::where(function ($q) use ($catSlug, $catKey) {
                $q->where('category', $catSlug)
                    ->orWhere('category', $catKey)
                    ->orWhere('category', 'custom:'.$catSlug)
                    ->orWhere('category', 'custom:'.$catKey);
                if (in_array($catKey, ['qa', 'qa_score', 'qanda'])) {
                    $q->orWhereIn('category', ['qa', 'qanda', 'qa_score']);
                }
            })->delete();
        }

        try {
            AuditRecord::create([
                'event_type' => 'category_voting_toggled',
                'category' => 'system',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Admin',
                'user_role' => 'admin',
                'action_description' => "Voting for '{$setting->name}' has been {$stateText} by Administrator",
                'ip_address' => $request->ip(),
                'status' => $isUnlocked ? 'success' : 'warning',
            ]);
        } catch (\Throwable $e) {
        }

        $feedback = $isUnlocked
            ? "Voting for '{$setting->name}' has been UNLOCKED. Judges are now allowed to vote and submit scores."
            : "Voting for '{$setting->name}' has been LOCKED. Judges can no longer enter or modify scores for this category.";

        return redirect()->route('admin.categories.management')
            ->with('success', $feedback);
    }

    /**
     * Show the Reset All Category Data confirmation page.
     */
    public function resetPage()
    {
        $counts = [
            'production' => ProductionScore::count(),
            'fitness' => FitnessScore::count(),
            'traditional_attire' => TraditionalAttireScore::count(),
            'indigenous_attire' => IndigenousAttireScore::count(),
            'qa' => QaScore::count(),
            'custom' => CustomCategoryScore::count(),
        ];

        $candidateCount = Candidate::count();
        $judgeCount = User::where('role', 'judge')->count();

        $customCategories = CriteriaSetting::where('stage', 'preliminary')
            ->whereNotIn('key', ['production', 'fitness', 'traditional_attire', 'indigenous_attire', 'traditional-attire', 'indigenous-attire', 'qa', 'qanda', 'preliminary_total'])
            ->orderBy('sort_order')
            ->get();

        $totalScores = array_sum($counts);

        return view('admin.categories.reset', compact('counts', 'customCategories', 'totalScores', 'candidateCount', 'judgeCount'));
    }

    /**
     * Perform the actual reset — wipe score data or full master wipe based on scope.
     */
    public function resetAllData(Request $request)
    {
        $request->validate([
            'confirmation' => 'required|in:RESET ALL DATA',
        ], [
            'confirmation.in' => 'You must type exactly "RESET ALL DATA" to confirm.',
        ]);

        $scope = $request->input('scope', 'all');
        $deleted = [];

        if (in_array($scope, ['all', 'scores_only', 'production'])) {
            $deleted['production'] = ProductionScore::count();
            ProductionScore::truncate();
        }
        if (in_array($scope, ['all', 'scores_only', 'fitness'])) {
            $deleted['fitness'] = FitnessScore::count();
            FitnessScore::truncate();
        }
        if (in_array($scope, ['all', 'scores_only', 'traditional_attire'])) {
            $deleted['traditional_attire'] = TraditionalAttireScore::count();
            TraditionalAttireScore::truncate();
        }
        if (in_array($scope, ['all', 'scores_only', 'indigenous_attire'])) {
            $deleted['indigenous_attire'] = IndigenousAttireScore::count();
            IndigenousAttireScore::truncate();
        }
        if (in_array($scope, ['all', 'scores_only', 'qa'])) {
            $deleted['qa'] = QaScore::count();
            QaScore::truncate();
        }
        if (in_array($scope, ['all', 'scores_only', 'custom'])) {
            $deleted['custom'] = CustomCategoryScore::count();
            CustomCategoryScore::truncate();
        }

        if ($scope === 'all' || $scope === 'candidates') {
            $deleted['candidates'] = Candidate::count();
            Schema::disableForeignKeyConstraints();
            Candidate::truncate();
            Schema::enableForeignKeyConstraints();
        }

        if ($scope === 'all' || $scope === 'judges') {
            $deleted['judges'] = User::where('role', 'judge')->count();
            Schema::disableForeignKeyConstraints();
            User::where('role', 'judge')->delete();
            Schema::enableForeignKeyConstraints();
        }

        if (in_array($scope, ['all', 'scores_only', 'judges', 'candidates'])) {
            JudgeCategorySubmission::truncate();
        }

        $total = array_sum($deleted);

        try {
            AuditRecord::create([
                'event_type' => 'master_purge',
                'category' => 'system',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Admin',
                'user_role' => 'admin',
                'action_description' => "MASTER PURGE — scope: {$scope} — {$total} total records deleted",
                'ip_address' => $request->ip(),
                'status' => 'danger',
                'risk_level' => 'critical',
            ]);
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.categories.reset')
            ->with('success', "✅ Reset operation completed successfully. Scope: '{$scope}' ({$total} total record(s) purged).");
    }

    /**
     * Reset scores for a single category, or manual reset for candidates / judges.
     */
    public function resetCategory(Request $request, string $category)
    {
        $allowed = ['production', 'fitness', 'traditional_attire', 'indigenous_attire', 'qa', 'custom', 'candidates', 'judges'];

        if (! in_array($category, $allowed)) {
            return redirect()->route('admin.categories.reset')
                ->with('error', "Unknown reset target: {$category}");
        }

        // Dedicated Manual Reset for Candidates
        if ($category === 'candidates') {
            $deleted = Candidate::count();
            Schema::disableForeignKeyConstraints();
            Candidate::truncate();
            // Wipe scores as candidates no longer exist
            ProductionScore::truncate();
            FitnessScore::truncate();
            TraditionalAttireScore::truncate();
            IndigenousAttireScore::truncate();
            QaScore::truncate();
            CustomCategoryScore::truncate();
            JudgeCategorySubmission::truncate();
            Schema::enableForeignKeyConstraints();

            try {
                AuditRecord::create([
                    'event_type' => 'candidates_reset',
                    'category' => 'candidates',
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()?->name ?? 'Admin',
                    'user_role' => 'admin',
                    'action_description' => "MANUAL RESET 'Candidates' — {$deleted} candidate records purged and scores cleared",
                    'ip_address' => $request->ip(),
                    'status' => 'danger',
                    'risk_level' => 'critical',
                ]);
            } catch (\Throwable $e) {
            }

            return redirect()->route('admin.categories.reset')
                ->with('success', "✅ Candidate Roster reset successfully. {$deleted} contestant record(s) and dependent score entries purged.");
        }

        // Dedicated Manual Reset for Judges
        if ($category === 'judges') {
            $deleted = User::where('role', 'judge')->count();
            Schema::disableForeignKeyConstraints();
            User::where('role', 'judge')->delete();
            // Wipe scores as scoring judges no longer exist
            ProductionScore::truncate();
            FitnessScore::truncate();
            TraditionalAttireScore::truncate();
            IndigenousAttireScore::truncate();
            QaScore::truncate();
            CustomCategoryScore::truncate();
            JudgeCategorySubmission::truncate();
            Schema::enableForeignKeyConstraints();

            try {
                AuditRecord::create([
                    'event_type' => 'judges_reset',
                    'category' => 'judges',
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()?->name ?? 'Admin',
                    'user_role' => 'admin',
                    'action_description' => "MANUAL RESET 'Judges' — {$deleted} judge accounts purged and scores cleared",
                    'ip_address' => $request->ip(),
                    'status' => 'danger',
                    'risk_level' => 'critical',
                ]);
            } catch (\Throwable $e) {
            }

            return redirect()->route('admin.categories.reset')
                ->with('success', "✅ Judges Panel reset successfully. {$deleted} judge evaluator account(s) and dependent score entries purged.");
        }

        // Standard Scoring Category Reset
        $modelMap = [
            'production' => ProductionScore::class,
            'fitness' => FitnessScore::class,
            'traditional_attire' => TraditionalAttireScore::class,
            'indigenous_attire' => IndigenousAttireScore::class,
            'qa' => QaScore::class,
            'custom' => CustomCategoryScore::class,
        ];

        $labelMap = [
            'production' => 'Production',
            'fitness' => 'Fitness',
            'traditional_attire' => 'Traditional Attire',
            'indigenous_attire' => 'Indigenous Attire',
            'qa' => 'Final Q & A',
            'custom' => 'Custom Categories',
        ];

        $model = $modelMap[$category];
        $label = $labelMap[$category];
        $deleted = $model::count();
        $model::truncate();

        // Clear finalization locks for this category so judges can score again
        $normalizedCat = str_replace('_', '-', $category);
        JudgeCategorySubmission::where(function ($q) use ($category, $normalizedCat) {
            $q->where('category', $category)
                ->orWhere('category', $normalizedCat);
            if ($category === 'custom') {
                $q->orWhere('category', 'like', 'custom:%');
            }
        })->delete();

        try {
            AuditRecord::create([
                'event_type' => 'scores_reset',
                'category' => $category,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Admin',
                'user_role' => 'admin',
                'action_description' => "RESET '{$label}' scores — {$deleted} records deleted",
                'ip_address' => $request->ip(),
                'status' => 'danger',
                'risk_level' => 'critical',
            ]);
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.categories.reset')
            ->with('success', "✅ {$label} scores reset successfully. {$deleted} record(s) deleted.");
    }
}
