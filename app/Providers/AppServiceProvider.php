<?php

namespace App\Providers;

use App\Models\CriteriaSetting;
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
    }
}
