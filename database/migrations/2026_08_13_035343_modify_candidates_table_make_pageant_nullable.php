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
        Schema::table('candidates', function (Blueprint $table) {
            // Make pageant_id nullable so candidates can be managed independently
            $table->foreignId('pageant_id')->nullable()->change();

            // Add a full_name column for simpler management
            $table->string('full_name')->after('candidate_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->foreignId('pageant_id')->nullable(false)->change();
            $table->dropColumn('full_name');
        });
    }
};
