<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class JudgeManagementController extends Controller
{
    /**
     * Display a listing of judges with optional search.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'judge');

        // Search by name or username
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $judges = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.judges.index', compact('judges'));
    }

    /**
     * Show the form for creating a new judge.
     */
    public function create()
    {
        return view('admin.judges.create');
    }

    /**
     * Store a newly created judge.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:100',
            'username' => 'required|string|min:4|max:50|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'required|boolean',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['username'] . '@judge.local',
            'password' => $validated['password'],
            'role' => 'judge',
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('admin.judges.index')
            ->with('success', 'Judge account created successfully.');
    }

    /**
     * Display the specified judge.
     */
    public function show(User $judge)
    {
        if (!$judge->isJudge()) {
            abort(404);
        }

        return view('admin.judges.show', compact('judge'));
    }

    /**
     * Show the form for editing the specified judge.
     */
    public function edit(User $judge)
    {
        if (!$judge->isJudge()) {
            abort(404);
        }

        return view('admin.judges.edit', compact('judge'));
    }

    /**
     * Update the specified judge.
     */
    public function update(Request $request, User $judge)
    {
        if (!$judge->isJudge()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|min:3|max:100',
            'username' => ['required', 'string', 'min:4', 'max:50', Rule::unique('users', 'username')->ignore($judge->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'required|boolean',
        ]);

        $judge->name = $validated['name'];
        $judge->username = $validated['username'];
        $judge->is_active = $validated['is_active'];

        if (!empty($validated['password'])) {
            $judge->password = $validated['password'];
        }

        $judge->save();

        return redirect()->route('admin.judges.index')
            ->with('success', 'Judge account updated successfully.');
    }

    /**
     * Remove the specified judge.
     */
    public function destroy(User $judge)
    {
        if (!$judge->isJudge()) {
            abort(404);
        }

        $judge->delete();

        return redirect()->route('admin.judges.index')
            ->with('success', 'Judge account deleted successfully.');
    }

    /**
     * Toggle judge active status.
     */
    public function toggleStatus(User $judge)
    {
        if (!$judge->isJudge()) {
            abort(404);
        }

        $judge->is_active = !$judge->is_active;
        $judge->save();

        $status = $judge->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.judges.index')
            ->with('success', "Judge account {$status} successfully.");
    }
}
