<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Authenticate the user.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $credentials = [
            $loginField => $request->input('username'),
            'password' => $request->input('password'),
            'is_active' => true,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectByRole(Auth::user());
        }

        // Check if the account exists but is deactivated
        $user = User::where($loginField, $request->input('username'))->first();
        if ($user && !$user->is_active) {
            return back()->withErrors([
                'username' => 'Your account has been deactivated. Please contact the administrator.',
            ])->withInput($request->only('username'));
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('username'));
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'You have been logged out.');
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle sending a reset password link / token.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'username_or_email' => 'required|string',
        ], [
            'username_or_email.required' => 'Please enter your username or email address.',
        ]);

        $input = trim($request->input('username_or_email'));

        // Find user by email or username
        $user = User::where('email', $input)
            ->orWhere('username', $input)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'username_or_email' => 'We could not find an account with that username or email address.',
            ])->withInput();
        }

        if (!$user->is_active) {
            return back()->withErrors([
                'username_or_email' => 'This account is deactivated. Please contact your system administrator.',
            ])->withInput();
        }

        // Generate a random 64-character token
        $token = Str::random(64);

        // Store token in password_reset_tokens table (hashed for security)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        return back()
            ->with('status', 'A password reset link has been created for your account.')
            ->with('reset_url', $resetUrl)
            ->with('user_email', $user->email);
    }

    /**
     * Show the reset password form.
     */
    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', old('email')),
        ]);
    }

    /**
     * Handle resetting the user's password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 6 characters.',
        ]);

        // Find the reset token record
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->withErrors([
                'email' => 'Invalid password reset request. Please request a new link.',
            ])->withInput();
        }

        // Check token expiration (valid for 60 minutes)
        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors([
                'email' => 'This password reset link has expired. Please request a new one.',
            ])->withInput();
        }

        // Check if token matches
        if (!Hash::check($request->token, $record->token)) {
            return back()->withErrors([
                'email' => 'This password reset token is invalid.',
            ])->withInput();
        }

        // Update the user's password
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors([
                'email' => 'User account not found.',
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Your password has been reset successfully! You can now log in.');
    }

    /**
     * Redirect user based on their role.
     */
    protected function redirectByRole($user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('judge.dashboard');
    }
}
