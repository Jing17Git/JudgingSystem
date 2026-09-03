<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('judge_number')->nullable()->after('role');
        });

        // Initialize judge_number for existing judges sequentially
        $judges = DB::table('users')
            ->where('role', 'judge')
            ->orderBy('id', 'asc')
            ->get();

        $number = 1;
        foreach ($judges as $judge) {
            DB::table('users')
                ->where('id', $judge->id)
                ->update(['judge_number' => $number++]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('judge_number');
        });
    }
};
