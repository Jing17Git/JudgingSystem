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
        Schema::create('criteria_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->decimal('percentage', 5, 2)->default(25.00);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default 4 criteria settings with equal 25% weights
        \Illuminate\Support\Facades\DB::table('criteria_settings')->insert([
            [
                'key' => 'production',
                'name' => 'Production',
                'percentage' => 25.00,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'fitness',
                'name' => 'Fitness',
                'percentage' => 25.00,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'indigenous_attire',
                'name' => 'Indigenous Attire',
                'percentage' => 25.00,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'traditional_attire',
                'name' => 'Traditional Attire',
                'percentage' => 25.00,
                'sort_order' => 4,
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
        Schema::dropIfExists('criteria_settings');
    }
};
