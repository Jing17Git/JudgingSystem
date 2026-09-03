<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old unique index if present
        try {
            DB::statement('ALTER TABLE candidates DROP INDEX candidates_pageant_id_candidate_number_unique');
        } catch (Throwable $e) {
            // If already dropped or not present, ignore
        }

        try {
            DB::statement('ALTER TABLE candidates DROP INDEX candidates_candidate_number_unique');
        } catch (Throwable $e) {
            // Ignore
        }

        Schema::table('candidates', function (Blueprint $table) {
            // Add composite unique index for (candidate_number, gender)
            $table->unique(['candidate_number', 'gender'], 'candidates_number_gender_unique');
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            try {
                $table->dropUnique('candidates_number_gender_unique');
            } catch (Throwable $e) {
            }

            $table->unique(['pageant_id', 'candidate_number'], 'candidates_pageant_id_candidate_number_unique');
        });
    }
};
