<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Category;
use App\Models\Criterion;
use App\Models\JudgeAssignment;
use App\Models\Pageant;
use App\Models\Score;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Seed demo data for testing the dashboard.
     */
    public function run(): void
    {
        // Create a demo pageant
        $pageant = Pageant::updateOrCreate(
            ['name' => 'Miss Universe Philippines 2026'],
            [
                'description' => 'The most prestigious beauty pageant in the Philippines, featuring the most talented and beautiful candidates from across the nation.',
                'venue' => 'Philippine International Convention Center',
                'event_date' => '2026-09-15',
                'status' => 'active',
            ]
        );

        // Create categories with weights
        $categories = [
            ['name' => 'Swimsuit Competition', 'description' => 'Grace and physical fitness showcase', 'weight_percentage' => 25, 'sort_order' => 1],
            ['name' => 'Evening Gown', 'description' => 'Elegance and poise in formal wear', 'weight_percentage' => 25, 'sort_order' => 2],
            ['name' => 'Question & Answer', 'description' => 'Intelligence and wit under pressure', 'weight_percentage' => 30, 'sort_order' => 3],
            ['name' => 'Beauty of Face', 'description' => 'Natural beauty and charm', 'weight_percentage' => 20, 'sort_order' => 4],
        ];

        foreach ($categories as $catData) {
            $category = Category::updateOrCreate(
                ['pageant_id' => $pageant->id, 'name' => $catData['name']],
                $catData + ['pageant_id' => $pageant->id]
            );

            // Create criteria for each category
            $criteria = [
                ['name' => 'Stage Presence', 'max_score' => 100, 'weight_percentage' => 40, 'sort_order' => 1],
                ['name' => 'Confidence', 'max_score' => 100, 'weight_percentage' => 30, 'sort_order' => 2],
                ['name' => 'Overall Impact', 'max_score' => 100, 'weight_percentage' => 30, 'sort_order' => 3],
            ];

            foreach ($criteria as $critData) {
                Criterion::updateOrCreate(
                    ['category_id' => $category->id, 'name' => $critData['name']],
                    $critData + ['category_id' => $category->id]
                );
            }
        }

        // Create candidates
        $candidates = [
            ['candidate_number' => 1, 'first_name' => 'Maria', 'last_name' => 'Santos', 'origin' => 'Manila'],
            ['candidate_number' => 2, 'first_name' => 'Angela', 'last_name' => 'Cruz', 'origin' => 'Cebu'],
            ['candidate_number' => 3, 'first_name' => 'Isabella', 'last_name' => 'Reyes', 'origin' => 'Davao'],
            ['candidate_number' => 4, 'first_name' => 'Sophia', 'last_name' => 'Garcia', 'origin' => 'Quezon City'],
            ['candidate_number' => 5, 'first_name' => 'Gabriella', 'last_name' => 'Torres', 'origin' => 'Makati'],
            ['candidate_number' => 6, 'first_name' => 'Victoria', 'last_name' => 'Mendoza', 'origin' => 'Pasig'],
            ['candidate_number' => 7, 'first_name' => 'Camille', 'last_name' => 'Lim', 'origin' => 'Taguig'],
            ['candidate_number' => 8, 'first_name' => 'Jasmine', 'last_name' => 'Navarro', 'origin' => 'Iloilo'],
        ];

        foreach ($candidates as $candData) {
            Candidate::updateOrCreate(
                ['pageant_id' => $pageant->id, 'candidate_number' => $candData['candidate_number']],
                $candData + ['pageant_id' => $pageant->id]
            );
        }

        // Create judge accounts
        $judges = [
            ['name' => 'Judge Ana Reyes', 'username' => 'judge1', 'email' => 'judge1@pageant.com'],
            ['name' => 'Judge Marco Santos', 'username' => 'judge2', 'email' => 'judge2@pageant.com'],
            ['name' => 'Judge Elena Cruz', 'username' => 'judge3', 'email' => 'judge3@pageant.com'],
        ];

        foreach ($judges as $judgeData) {
            $judge = User::updateOrCreate(
                ['username' => $judgeData['username']],
                $judgeData + [
                    'password' => 'password',
                    'role' => 'judge',
                    'is_active' => true,
                ]
            );

            // Assign judge to pageant
            JudgeAssignment::updateOrCreate([
                'pageant_id' => $pageant->id,
                'user_id' => $judge->id,
            ]);
        }

        // Generate some demo scores
        $pageantCandidates = $pageant->candidates;
        $assignments = $pageant->judgeAssignments;
        $allCriteria = Criterion::whereIn('category_id', $pageant->categories->pluck('id'))->get();

        foreach ($assignments as $assignment) {
            foreach ($pageantCandidates as $candidate) {
                foreach ($allCriteria as $criterion) {
                    // Random score between 70 and 98 for realistic demo data
                    Score::updateOrCreate(
                        [
                            'judge_assignment_id' => $assignment->id,
                            'candidate_id' => $candidate->id,
                            'criterion_id' => $criterion->id,
                        ],
                        [
                            'score' => rand(70, 98),
                            'is_locked' => false,
                        ]
                    );
                }
            }
        }
    }
}
