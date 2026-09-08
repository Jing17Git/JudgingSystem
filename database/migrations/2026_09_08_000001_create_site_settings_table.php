<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, image
            $table->string('label')->nullable();
            $table->timestamps();
        });

        // Seed defaults
        $defaults = [
            ['key' => 'site_name',         'value' => 'CrownScore',                            'type' => 'text',     'label' => 'System Name'],
            ['key' => 'site_tagline',       'value' => 'Pageant Judging System',                'type' => 'text',     'label' => 'Tagline (under system name)'],
            ['key' => 'site_logo',          'value' => 'images/logo.png',                       'type' => 'image',    'label' => 'Logo Image'],
            ['key' => 'hero_badge_text',    'value' => 'Fair. Transparent. Real-Time.',         'type' => 'text',     'label' => 'Hero Badge Text'],
            ['key' => 'hero_headline',      'value' => "Where Excellence\nMeets the Crown.",    'type' => 'textarea', 'label' => 'Hero Headline'],
            ['key' => 'hero_description',   'value' => 'CrownScore is a web-based real-time pageant judging and tabulation system built for accuracy, fairness, and speed.', 'type' => 'textarea', 'label' => 'Hero Description'],
            ['key' => 'hero_credits',       'value' => 'Made by Team MISO.',                    'type' => 'text',     'label' => 'Credits Text'],
            ['key' => 'hero_image',         'value' => 'images/pageant_stage_hero.png',         'type' => 'image',    'label' => 'Hero Stage Image'],
            ['key' => 'feature_1_title',    'value' => 'Real-Time Scoring',                     'type' => 'text',     'label' => 'Feature 1 Title'],
            ['key' => 'feature_1_text',     'value' => 'Instant, accurate results as it happens.', 'type' => 'text', 'label' => 'Feature 1 Description'],
            ['key' => 'feature_2_title',    'value' => 'Secure & Reliable',                     'type' => 'text',     'label' => 'Feature 2 Title'],
            ['key' => 'feature_2_text',     'value' => 'Your data is safe with enterprise-grade security.', 'type' => 'text', 'label' => 'Feature 2 Description'],
            ['key' => 'feature_3_title',    'value' => 'Transparent Results',                   'type' => 'text',     'label' => 'Feature 3 Title'],
            ['key' => 'feature_3_text',     'value' => 'Clear, auditable, and built for fairness.', 'type' => 'text', 'label' => 'Feature 3 Description'],
        ];

        foreach ($defaults as $row) {
            DB::table('site_settings')->insertOrIgnore(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
