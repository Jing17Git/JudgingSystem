<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingsController extends Controller
{
    /**
     * Display the site settings form.
     */
    public function index()
    {
        $settings = SiteSetting::all()->keyBy('key');

        return view('admin.site-settings.index', compact('settings'));
    }

    /**
     * Update the site settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'hero_badge_text' => 'nullable|string|max:255',
            'hero_headline' => 'nullable|string|max:1000',
            'hero_description' => 'nullable|string|max:2000',
            'hero_credits' => 'nullable|string|max:255',
            'feature_1_title' => 'nullable|string|max:255',
            'feature_1_text' => 'nullable|string|max:500',
            'feature_2_title' => 'nullable|string|max:255',
            'feature_2_text' => 'nullable|string|max:500',
            'feature_3_title' => 'nullable|string|max:255',
            'feature_3_text' => 'nullable|string|max:500',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:8192',
        ]);

        $textKeys = [
            'site_name', 'site_tagline', 'hero_badge_text', 'hero_headline',
            'hero_description', 'hero_credits', 'feature_1_title', 'feature_1_text',
            'feature_2_title', 'feature_2_text', 'feature_3_title', 'feature_3_text',
        ];

        foreach ($textKeys as $key) {
            if ($request->has($key)) {
                SiteSetting::set($key, $request->input($key) ?? '');
            }
        }

        if ($request->hasFile('site_logo')) {
            $logo = $request->file('site_logo');
            $filename = 'logo_'.time().'.'.$logo->getClientOriginalExtension();
            $logo->move(public_path('images'), $filename);
            SiteSetting::set('site_logo', 'images/'.$filename);
        }

        if ($request->hasFile('hero_image')) {
            $heroImg = $request->file('hero_image');
            $filename = 'hero_'.time().'.'.$heroImg->getClientOriginalExtension();
            $heroImg->move(public_path('images'), $filename);
            SiteSetting::set('hero_image', 'images/'.$filename);
        }

        SiteSetting::clearCache();

        return redirect()->back()->with('success', 'Site settings updated successfully!');
    }
}
