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
        Schema::create('audit_records', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // score_submitted, score_reset, settings_updated, cache_cleared, etc.
            $table->string('category')->nullable(); // fitness, production, traditional_attire, indigenous_attire, qa, system
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->foreignId('candidate_id')->nullable()->constrained('candidates')->nullOnDelete();
            $table->string('candidate_name')->nullable();
            $table->integer('candidate_number')->nullable();
            $table->string('action_description');
            $table->text('details')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('status')->default('success'); // success, warning, info, error
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_records');
    }
};
