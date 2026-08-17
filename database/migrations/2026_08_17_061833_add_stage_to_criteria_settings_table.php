<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('criteria_settings', function (Blueprint $table) {
            $table->string('stage')->default('preliminary')->after('name');
        });

        // Insert Final Criteria Settings (30% Preliminary, 70% Q&A)
        \Illuminate\Support\Facades\DB::table('criteria_settings')->insert([
            [
                'key' => 'preliminary_score',
                'name' => 'Preliminary Grand Total',
                'stage' => 'final',
                'percentage' => 30.00,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'qa_score',
                'name' => 'Question & Answer (Q & A)',
                'stage' => 'final',
                'percentage' => 70.00,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('criteria_settings')->where('stage', 'final')->delete();
        Schema::table('criteria_settings', function (Blueprint $table) {
            $table->dropColumn('stage');
        });
    }
};
