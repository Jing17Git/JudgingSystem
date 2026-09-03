<?php

namespace App\Http\Controllers\Judge;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CriteriaSetting;
use App\Models\CustomCategoryScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JudgeCustomCategoryController extends Controller
{
    /**
     * Display the judge scoring pad for a dynamic (custom) category.
     * Reuses the shared judge.scoring.index view.
     */
    public function index(Request $request, string $key)
    {
        // Validate key exists
        $categorySetting = CriteriaSetting::where('key', $key)
            ->where('stage', 'preliminary')
            ->firstOrFail();

        $judgeId    = Auth::id();
        $candidates = Candidate::orderBy('candidate_number')->get();

        $maleCandidates   = $candidates->filter(fn($c) => $c->gender === 'Male');
        $femaleCandidates = $candidates->filter(fn($c) => $c->gender === 'Female');

        // Load this judge's existing scores for this category
        $rawScores = CustomCategoryScore::where('judge_id', $judgeId)
            ->where('category_key', $key)
            ->get()
            ->keyBy('candidate_id');

        $scores = [];
        foreach ($rawScores as $candId => $sObj) {
            $scores[$candId] = (float) $sObj->score;
        }

        // Icon path for generic custom category
        $iconPath = 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z';

        $initialPairIndex = 0;
        $requestedPair = $request->query('pair');
        if ($requestedPair && is_numeric($requestedPair)) {
            $initialPairIndex = max(0, ((int) $requestedPair) - 1);
        }

        // Pass the category_key so the judge scoring view knows to use custom routes
        $categoryName = $categorySetting->name;
        $categorySlug = 'custom:' . $key;

        return view('judge.scoring.index', compact(
            'categoryName',
            'categorySlug',
            'iconPath',
            'maleCandidates',
            'femaleCandidates',
            'scores',
            'initialPairIndex'
        ));
    }
}
