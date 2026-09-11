<?php

namespace App\Http\Controllers\Judge;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CriteriaSetting;
use App\Models\CustomCategoryScore;
use App\Models\FitnessScore;
use App\Models\IndigenousAttireScore;
use App\Models\JudgeCategorySubmission;
use App\Models\ProductionScore;
use App\Models\QaScore;
use App\Models\TraditionalAttireScore;
use Illuminate\Support\Facades\Auth;

class JudgeDashboardController extends Controller
{
    /**
     * Display the judge dashboard.
     */
    public function index()
    {
        $judgeId = Auth::id();

        $totalCandidates = Candidate::count();
        $maleCandidatesCount = Candidate::where('gender', 'Male')->count();
        $femaleCandidatesCount = Candidate::where('gender', 'Female')->count();

        // Load finalized submissions map for this judge
        $finalizedMap = JudgeCategorySubmission::where('judge_id', $judgeId)
            ->where('is_finalized', true)
            ->pluck('category')
            ->flip()
            ->toArray();

        // 1. Fetch dynamic preliminary categories from criteria_settings (always up-to-date)
        $prelimSettings = CriteriaSetting::where('stage', 'preliminary')
            ->orderBy('sort_order')
            ->get();

        $categories = [];

        if ($prelimSettings->isNotEmpty()) {
            foreach ($prelimSettings as $setting) {
                $catKey = strtolower(str_replace('_', '-', $setting->key));
                $isDisabled = !$setting->is_enabled;

                if ($catKey === 'production') {
                    $categories[] = [
                        'name' => $setting->name,
                        'slug' => 'production',
                        'route' => 'judge.production.index',
                        'url' => route('judge.production.index'),
                        'icon' => 'M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5',
                        'submitted' => ProductionScore::where('judge_id', $judgeId)->count(),
                        'target_candidates' => $totalCandidates,
                        'is_finalized' => isset($finalizedMap['production']) || $isDisabled,
                    ];
                } elseif ($catKey === 'fitness') {
                    $categories[] = [
                        'name' => $setting->name,
                        'slug' => 'fitness',
                        'route' => 'judge.fitness.index',
                        'url' => route('judge.fitness.index'),
                        'icon' => 'M3.75 13.5l10.5-10.5m0 0L18 6.75M14.25 3l3.75 3.75M3 14.25l3.75 3.75m0 0l10.5-10.5M6.75 18L3 14.25',
                        'submitted' => FitnessScore::where('judge_id', $judgeId)->count(),
                        'target_candidates' => $totalCandidates,
                        'is_finalized' => isset($finalizedMap['fitness']) || $isDisabled,
                    ];
                } elseif (in_array($catKey, ['traditional-attire', 'traditional_attire'])) {
                    $categories[] = [
                        'name' => $setting->name,
                        'slug' => 'traditional-attire',
                        'route' => 'judge.traditional-attire.index',
                        'url' => route('judge.traditional-attire.index'),
                        'icon' => 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09z',
                        'submitted' => TraditionalAttireScore::where('judge_id', $judgeId)->count(),
                        'target_candidates' => $totalCandidates,
                        'is_finalized' => isset($finalizedMap['traditional-attire']) || isset($finalizedMap['traditional_attire']) || $isDisabled,
                    ];
                } elseif (in_array($catKey, ['indigenous-attire', 'indigenous_attire'])) {
                    $categories[] = [
                        'name' => $setting->name,
                        'slug' => 'indigenous-attire',
                        'route' => 'judge.indigenous-attire.index',
                        'url' => route('judge.indigenous-attire.index'),
                        'icon' => 'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 21v-2.25m-6.364-.386l1.591-1.591M3 12h2.25m.386-6.364l1.591 1.591M12 18.75a6.75 6.75 0 100-13.5 6.75 6.75 0 000 13.5z',
                        'submitted' => IndigenousAttireScore::where('judge_id', $judgeId)->count(),
                        'target_candidates' => $totalCandidates,
                        'is_finalized' => isset($finalizedMap['indigenous-attire']) || isset($finalizedMap['indigenous_attire']) || $isDisabled,
                    ];
                } else {
                    // Dynamically added custom category (e.g. Talent Portion, Picture Analysis)
                    $categories[] = [
                        'name' => $setting->name,
                        'slug' => $setting->key,
                        'route' => 'judge.category.index',
                        'url' => route('judge.category.index', ['key' => $setting->key]),
                        'icon' => 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z',
                        'submitted' => CustomCategoryScore::where('judge_id', $judgeId)->where('category_key', $setting->key)->count(),
                        'target_candidates' => $totalCandidates,
                        'is_finalized' => isset($finalizedMap['custom:'.$setting->key]) || isset($finalizedMap[$setting->key]) || $isDisabled,
                    ];
                }
            }
        } else {
            // Built-in defaults fallback if settings table is empty
            $categories = [
                [
                    'name' => 'Production',
                    'slug' => 'production',
                    'route' => 'judge.production.index',
                    'url' => route('judge.production.index'),
                    'icon' => 'M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5',
                    'submitted' => ProductionScore::where('judge_id', $judgeId)->count(),
                    'target_candidates' => $totalCandidates,
                    'is_finalized' => isset($finalizedMap['production']),
                ],
                [
                    'name' => 'Fitness',
                    'slug' => 'fitness',
                    'route' => 'judge.fitness.index',
                    'url' => route('judge.fitness.index'),
                    'icon' => 'M3.75 13.5l10.5-10.5m0 0L18 6.75M14.25 3l3.75 3.75M3 14.25l3.75 3.75m0 0l10.5-10.5M6.75 18L3 14.25',
                    'submitted' => FitnessScore::where('judge_id', $judgeId)->count(),
                    'target_candidates' => $totalCandidates,
                    'is_finalized' => isset($finalizedMap['fitness']),
                ],
                [
                    'name' => 'Traditional Attire',
                    'slug' => 'traditional-attire',
                    'route' => 'judge.traditional-attire.index',
                    'url' => route('judge.traditional-attire.index'),
                    'icon' => 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09z',
                    'submitted' => TraditionalAttireScore::where('judge_id', $judgeId)->count(),
                    'target_candidates' => $totalCandidates,
                    'is_finalized' => isset($finalizedMap['traditional-attire']) || isset($finalizedMap['traditional_attire']),
                ],
                [
                    'name' => 'Indigenous Attire',
                    'slug' => 'indigenous-attire',
                    'route' => 'judge.indigenous-attire.index',
                    'url' => route('judge.indigenous-attire.index'),
                    'icon' => 'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 21v-2.25m-6.364-.386l1.591-1.591M3 12h2.25m.386-6.364l1.591 1.591M12 18.75a6.75 6.75 0 100-13.5 6.75 6.75 0 000 13.5z',
                    'submitted' => IndigenousAttireScore::where('judge_id', $judgeId)->count(),
                    'target_candidates' => $totalCandidates,
                    'is_finalized' => isset($finalizedMap['indigenous-attire']) || isset($finalizedMap['indigenous_attire']),
                ],
            ];
        }

        // Final Stage Category: Q & A (top 5 finalists per gender = 10 candidates max)
        $qaSetting = CriteriaSetting::where('stage', 'final')
            ->whereIn('key', ['qa_score', 'qa'])
            ->first();
        $qaIsDisabled = $qaSetting && !$qaSetting->is_enabled;

        $categories[] = [
            'name' => 'Q & A',
            'slug' => 'qa',
            'route' => 'judge.qa.index',
            'url' => route('judge.qa.index'),
            'icon' => 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09z',
            'submitted' => QaScore::where('judge_id', $judgeId)->count(),
            'target_candidates' => min(10, $totalCandidates),
            'is_finalized' => isset($finalizedMap['qa']) || isset($finalizedMap['qanda']) || $qaIsDisabled,
        ];

        $totalAssignedCategories = count($categories);
        $totalRequiredScores = array_sum(array_column($categories, 'target_candidates'));
        $totalSubmittedScores = array_sum(array_column($categories, 'submitted'));
        $overallProgressPercent = $totalRequiredScores > 0 ? min(100, round(($totalSubmittedScores / $totalRequiredScores) * 100)) : 0;

        return view('judge.dashboard', compact(
            'totalCandidates',
            'maleCandidatesCount',
            'femaleCandidatesCount',
            'categories',
            'totalAssignedCategories',
            'totalRequiredScores',
            'totalSubmittedScores',
            'overallProgressPercent'
        ));
    }
}
