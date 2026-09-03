<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SuperAdminRegisterController extends Controller
{
    /**
     * Show the secret super-admin registration form.
     */
    public function showRegister()
    {
        // If already logged in as super-admin, redirect to super-admin dashboard
        if (Auth::check() && Auth::user()->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        return view('auth.super-admin-register');
    }

    /**
     * Process secret super-admin registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'secret_key' => 'required|string',
        ], [
            'secret_key.required' => 'Secret security key is required to register a super-admin account.',
            'username.unique' => 'This username is already taken.',
            'email.unique' => 'This email address is already registered.',
        ]);

        $expectedKey = env('SUPER_ADMIN_SECRET_KEY', 'SUPERADMIN2026');

        if ($request->input('secret_key') !== $expectedKey) {
            return back()
                ->withErrors(['secret_key' => 'Invalid Secret Security Key. Access denied.'])
                ->withInput($request->except(['password', 'password_confirmation', 'secret_key']));
        }

        $user = User::create([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => 'super-admin',
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('super-admin.dashboard')
            ->with('success', 'Secret Super-Admin registration successful! Welcome to your workspace.');
    }
}
