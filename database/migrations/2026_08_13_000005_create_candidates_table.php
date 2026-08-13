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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pageant_id')->constrained()->cascadeOnDelete();
            $table->integer('candidate_number');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('photo_url')->nullable();
            $table->text('bio')->nullable();
            $table->string('origin')->nullable();
            $table->timestamps();

            $table->unique(['pageant_id', 'candidate_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
