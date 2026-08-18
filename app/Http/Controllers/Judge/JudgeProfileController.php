<?php

namespace App\Http\Controllers\Judge;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class JudgeProfileController extends Controller
{
    /**
     * Display the judge profile page.
     */
    public function show()
    {
        $judge = Auth::user();
        return view('judge.profile', compact('judge'));
    }

    /**
     * Update the judge profile.
     */
    public function update(Request $request)
    {
        $judge = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|min:3|max:100',
            'email' => ['nullable', 'email', 'max:100', Rule::unique('users', 'email')->ignore($judge->id)],
            'picture' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $judge->name = $validated['name'];
        if (array_key_exists('email', $validated)) {
            $judge->email = $validated['email'] ?? $judge->email;
        }

        // Handle profile photo upload
        if ($request->hasFile('picture')) {
            if ($judge->photo_url && Storage::disk('public')->exists($judge->photo_url)) {
                Storage::disk('public')->delete($judge->photo_url);
            }
            $judge->photo_url = $request->file('picture')->store('judges', 'public');
        }

        // Handle password change
        if (!empty($validated['password'])) {
            if (!Hash::check($validated['current_password'], $judge->password)) {
                return back()->withErrors(['current_password' => 'Your current password does not match our records.']);
            }
            $judge->password = $validated['password'];
        }

        $judge->save();

        return redirect()->route('judge.profile')->with('success', 'Profile updated successfully.');
    }
}
