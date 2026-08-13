<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indigenous_attire_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('judge_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('score', 4, 2)->default(0)->comment('Score from 1 to 10');
            $table->timestamps();

            $table->unique(['candidate_id', 'judge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indigenous_attire_scores');
    }
};
