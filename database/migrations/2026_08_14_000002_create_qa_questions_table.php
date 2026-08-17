<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qa_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->enum('gender', ['Male', 'Female', 'All'])->default('All');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qa_questions');
    }
};
