<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'pageant_id',
        'candidate_number',
        'full_name',
        'gender',
        'first_name',
        'last_name',
        'photo_url',
        'bio',
        'origin',
    ];

    /**
     * Mutator to automatically capitalize candidate names (Title Case).
     */
    public function setFullNameAttribute($value): void
    {
        $this->attributes['full_name'] = ! empty($value) ? mb_convert_case(trim($value), MB_CASE_TITLE, 'UTF-8') : $value;
        $this->attributes['first_name'] = $this->attributes['full_name'];
    }

    /**
     * Get the display name — prefers full_name, falls back to first+last.
     */
    public function getDisplayNameAttribute(): string
    {
        if (! empty($this->full_name)) {
            return $this->full_name;
        }

        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Get the pageant that this candidate belongs to.
     */
    public function pageant()
    {
        return $this->belongsTo(Pageant::class);
    }

    /**
     * Get all scores for this candidate.
     */
    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public const DEFAULT_TOP_QUALIFIERS_COUNT = 5;

    /**
     * Get the configured number of top qualifiers per division.
     */
    public static function topQualifiersCount(): int
    {
        return (int) config('pageant.top_qualifiers_count', self::DEFAULT_TOP_QUALIFIERS_COUNT);
    }

    /**
     * Get Top qualified finalists per division based on preliminary completion & scores.
     * Returns ['male' => Collection, 'female' => Collection, 'all' => Collection]
     */
    public static function getTopQualifiedFinalists(): array
    {
        $allCandidates = static::orderBy('candidate_number')->get();
        $activeJudges = User::where('role', 'judge')->where('is_active', true)->get();
        $judgeCount = $activeJudges->count();
        $judgeIds = $activeJudges->pluck('id')->toArray();

        if (empty($judgeIds)) {
            return [
                'male' => collect(),
                'female' => collect(),
                'all' => collect(),
            ];
        }

        $builtInKeys = ['production', 'fitness', 'traditional_attire', 'indigenous_attire',
            'traditional-attire', 'indigenous-attire', 'qa', 'qanda', 'preliminary_total'];

        $customCategories = CriteriaSetting::where('stage', 'preliminary')
            ->whereNotIn('key', $builtInKeys)
            ->get();

        $prodScoresKeyed = ProductionScore::all()->keyBy(fn ($s) => $s->candidate_id.'_'.$s->judge_id);
        $fitScoresKeyed = FitnessScore::all()->keyBy(fn ($s) => $s->candidate_id.'_'.$s->judge_id);
        $tradScoresKeyed = TraditionalAttireScore::all()->keyBy(fn ($s) => $s->candidate_id.'_'.$s->judge_id);
        $indigScoresKeyed = IndigenousAttireScore::all()->keyBy(fn ($s) => $s->candidate_id.'_'.$s->judge_id);

        $customRawScores = [];
        foreach ($customCategories as $customCat) {
            $customRawScores[$customCat->key] = CustomCategoryScore::forCategory($customCat->key);
        }

        $fullyScored = $allCandidates->filter(function ($c) use ($judgeIds, $prodScoresKeyed, $fitScoresKeyed, $tradScoresKeyed, $indigScoresKeyed, $customCategories, $customRawScores) {
            foreach ($judgeIds as $jid) {
                $key = $c->id.'_'.$jid;
                if (
                    ! isset($prodScoresKeyed[$key]) ||
                    ! isset($fitScoresKeyed[$key]) ||
                    ! isset($tradScoresKeyed[$key]) ||
                    ! isset($indigScoresKeyed[$key])
                ) {
                    return false;
                }
                foreach ($customCategories as $customCat) {
                    $raw = $customRawScores[$customCat->key] ?? collect();
                    if (! isset($raw[$key])) {
                        return false;
                    }
                }
            }

            return true;
        });

        $weights = CriteriaSetting::getPercentageMap();
        $prodWeight = (float) ($weights['production'] ?? 25.0);
        $fitWeight = (float) ($weights['fitness'] ?? 25.0);
        $tradWeight = (float) ($weights['traditional_attire'] ?? 25.0);
        $indigWeight = (float) ($weights['indigenous_attire'] ?? 25.0);

        $preJudgingTotals = [];
        foreach ($fullyScored as $c) {
            $pSum = 0;
            $fSum = 0;
            $tSum = 0;
            $iSum = 0;
            foreach ($judgeIds as $jid) {
                $key = $c->id.'_'.$jid;
                $pSum += (float) $prodScoresKeyed[$key]->score;
                $fSum += (float) $fitScoresKeyed[$key]->score;
                $tSum += (float) $tradScoresKeyed[$key]->score;
                $iSum += (float) $indigScoresKeyed[$key]->score;
            }
            $pAvg = $judgeCount > 0 ? $pSum / $judgeCount : 0;
            $fAvg = $judgeCount > 0 ? $fSum / $judgeCount : 0;
            $tAvg = $judgeCount > 0 ? $tSum / $judgeCount : 0;
            $iAvg = $judgeCount > 0 ? $iSum / $judgeCount : 0;

            $total = ($pAvg * $prodWeight / 100.0)
                   + ($fAvg * $fitWeight / 100.0)
                   + ($tAvg * $tradWeight / 100.0)
                   + ($iAvg * $indigWeight / 100.0);

            foreach ($customCategories as $customCat) {
                $raw = $customRawScores[$customCat->key] ?? collect();
                $cSum = 0;
                foreach ($judgeIds as $jid) {
                    $key = $c->id.'_'.$jid;
                    $cSum += isset($raw[$key]) ? (float) $raw[$key]->score : 0;
                }
                $cAvg = $judgeCount > 0 ? $cSum / $judgeCount : 0;
                $cW = (float) $customCat->percentage;
                $total += ($cAvg * $cW / 100.0);
            }

            $preJudgingTotals[$c->id] = $total;
        }

        $sortFn = function ($a, $b) use ($preJudgingTotals) {
            $totA = $preJudgingTotals[$a->id] ?? 0;
            $totB = $preJudgingTotals[$b->id] ?? 0;
            if ($totA == $totB) {
                return $a->candidate_number <=> $b->candidate_number;
            }

            return $totB <=> $totA;
        };

        $limit = static::topQualifiersCount();

        $topMale = $fullyScored->filter(fn ($c) => $c->gender === 'Male')->sort($sortFn)->take($limit)->values();
        $topFemale = $fullyScored->filter(fn ($c) => $c->gender === 'Female')->sort($sortFn)->take($limit)->values();

        return [
            'male' => $topMale,
            'female' => $topFemale,
            'all' => $topMale->concat($topFemale),
        ];
    }

    /**
     * Get IDs of Top qualified finalists per division based on preliminary completion & scores.
     */
    public static function getTop5QualifiedIds(): array
    {
        $finalists = static::getTopQualifiedFinalists();

        return $finalists['all']->pluck('id')->toArray();
    }
}
