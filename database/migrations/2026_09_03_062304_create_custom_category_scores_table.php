<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_category_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->foreignId('judge_id')->constrained('users')->cascadeOnDelete();
            $table->string('category_key', 100)->index();
            $table->decimal('score', 5, 2)->default(0);
            $table->timestamps();

            // Each judge can only score a candidate once per category
            $table->unique(['candidate_id', 'judge_id', 'category_key'], 'custom_scores_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_category_scores');
    }
};
