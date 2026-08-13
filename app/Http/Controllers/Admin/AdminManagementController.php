<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminManagementController extends Controller
{
    /**
     * Display a listing of admin users.
     */
    public function index()
    {
        $admins = User::where('role', 'admin')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        return view('admin.admins.create');
    }

    /**
     * Store a newly created admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'admin',
            'is_active' => true,
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin account created successfully.');
    }

    /**
     * Show the form for editing an admin.
     */
    public function edit(User $admin)
    {
        if (!$admin->isAdmin()) {
            abort(404);
        }
        return view('admin.admins.edit', compact('admin'));
    }

    /**
     * Update the specified admin.
     */
    public function update(Request $request, User $admin)
    {
        if (!$admin->isAdmin()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($admin->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $admin->name = $validated['name'];
        $admin->username = $validated['username'];
        $admin->email = $validated['email'];

        $passwordChanged = false;
        if (!empty($validated['password'])) {
            $admin->password = $validated['password'];
            $passwordChanged = true;
        }

        $admin->save();

        if ($passwordChanged && auth()->id() === $admin->id) {
            auth()->login($admin);
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin account updated successfully.');
    }

    /**
     * Remove the specified admin.
     */
    public function destroy(User $admin)
    {
        if (!$admin->isAdmin()) {
            abort(404);
        }

        // Prevent self-deletion
        if ($admin->id === auth()->id()) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin account deleted successfully.');
    }
}
