<?php

namespace App\Providers;

use App\Models\CriteriaSetting;
use App\Models\JudgeCategorySubmission;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share site settings branding (name, logo, tagline) globally to all views
        View::composer('*', function ($view) {
            try {
                $view->with('siteName', SiteSetting::get('site_name', 'JudgingSystem'));
                $view->with('siteLogo', SiteSetting::get('site_logo', 'favicon.png'));
                $view->with('siteTagline', SiteSetting::get('site_tagline', 'Pageant Judging System'));
            } catch (\Throwable $e) {
                $view->with('siteName', 'JudgingSystem');
                $view->with('siteLogo', 'favicon.png');
                $view->with('siteTagline', 'Pageant Judging System');
            }
        });

        // Share dynamic sidebar preliminary categories to admin and judge layouts.
        // This allows categories added by superadmin to automatically appear in both sidebars.
        View::composer(
            ['layouts.admin', 'layouts.judge'],
            function ($view) {
                try {
                    $sidebarPrelimCategories = CriteriaSetting::where('stage', 'preliminary')
                        ->orderBy('sort_order')
                        ->get();
                } catch (\Throwable $e) {
                    $sidebarPrelimCategories = collect();
                }
                $view->with('sidebarPrelimCategories', $sidebarPrelimCategories);
            }
        );

        // Share finalization data to admin layout for the sidebar toggle indicators
        View::composer('layouts.admin', function ($view) {
            try {
                $totalJudges = User::where('role', 'judge')->count();

                // Count finalized judges per category
                $finalizationCounts = JudgeCategorySubmission::where('is_finalized', true)
                    ->selectRaw('category, COUNT(DISTINCT judge_id) as finalized_count')
                    ->groupBy('category')
                    ->pluck('finalized_count', 'category')
                    ->toArray();

                // Also get final stage categories for the Q&A toggle
                $sidebarFinalCategories = CriteriaSetting::where('stage', 'final')
                    ->orderBy('sort_order')
                    ->get();
            } catch (\Throwable $e) {
                $totalJudges = 0;
                $finalizationCounts = [];
                $sidebarFinalCategories = collect();
            }

            $view->with('sidebarTotalJudges', $totalJudges);
            $view->with('sidebarFinalizationCounts', $finalizationCounts);
            $view->with('sidebarFinalCategories', $sidebarFinalCategories);
        });
    }
}
