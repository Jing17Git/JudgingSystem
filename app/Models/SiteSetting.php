<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'label'];

    /**
     * Get a single setting value by key, with optional default.
     */
    public static function get(string $key, string $default = ''): string
    {
        return Cache::remember("site_setting_{$key}", 3600, function () use ($key, $default) {
            try {
                if (! Schema::hasTable('site_settings')) {
                    return $default;
                }
                $setting = static::where('key', $key)->first();

                return $setting ? ($setting->value ?? $default) : $default;
            } catch (\Throwable $e) {
                return $default;
            }
        });
    }

    /**
     * Set / update a setting value and clear its cache.
     */
    public static function set(string $key, string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("site_setting_{$key}");
    }

    /**
     * Get all settings as a key => value array.
     */
    public static function allAsMap(): array
    {
        return Cache::remember('site_settings_all', 3600, function () {
            return static::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear the full settings cache (call after any update).
     */
    public static function clearCache(): void
    {
        Cache::forget('site_settings_all');
        $keys = ['site_name', 'site_tagline', 'site_logo', 'hero_badge_text', 'hero_headline',
            'hero_description', 'hero_credits', 'hero_image',
            'feature_1_title', 'feature_1_text', 'feature_2_title', 'feature_2_text',
            'feature_3_title', 'feature_3_text'];
        foreach ($keys as $k) {
            Cache::forget("site_setting_{$k}");
        }
    }
}
