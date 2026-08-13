<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateManagementController extends Controller
{
    /**
     * Display a listing of candidates.
     */
    public function index(Request $request)
    {
        $query = Candidate::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('candidate_number', 'like', "%{$search}%");
            });
        }

        $candidates = $query->orderBy('candidate_number', 'asc')->paginate(15)->withQueryString();

        return view('admin.candidates.index', compact('candidates'));
    }

    /**
     * Show the form for creating a new candidate.
     */
    public function create()
    {
        return view('admin.candidates.create');
    }

    /**
     * Store a newly created candidate.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidate_number' => [
                'required',
                'integer',
                'min:1',
                \Illuminate\Validation\Rule::unique('candidates', 'candidate_number')->where(function ($query) use ($request) {
                    return $query->where('gender', $request->gender);
                }),
            ],
            'full_name' => 'required|string|min:3|max:100',
            'gender' => 'nullable|in:Male,Female,Other',
            'picture' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('picture')) {
            $photoPath = $request->file('picture')->store('candidates', 'public');
        }

        Candidate::create([
            'candidate_number' => $validated['candidate_number'],
            'full_name' => $validated['full_name'],
            'gender' => $validated['gender'] ?? null,
            'first_name' => $validated['full_name'],
            'last_name' => '',
            'photo_url' => $photoPath,
        ]);

        return redirect()->route('admin.candidates.index')
            ->with('success', 'Candidate created successfully.');
    }

    /**
     * Display the specified candidate.
     */
    public function show(Candidate $candidate)
    {
        return view('admin.candidates.show', compact('candidate'));
    }

    /**
     * Show the form for editing the specified candidate.
     */
    public function edit(Candidate $candidate)
    {
        return view('admin.candidates.edit', compact('candidate'));
    }

    /**
     * Update the specified candidate.
     */
    public function update(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'candidate_number' => [
                'required',
                'integer',
                'min:1',
                \Illuminate\Validation\Rule::unique('candidates', 'candidate_number')->where(function ($query) use ($request) {
                    return $query->where('gender', $request->gender);
                })->ignore($candidate->id),
            ],
            'full_name' => 'required|string|min:3|max:100',
            'gender' => 'nullable|in:Male,Female,Other',
            'picture' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $candidate->candidate_number = $validated['candidate_number'];
        $candidate->full_name = $validated['full_name'];
        $candidate->gender = $validated['gender'] ?? null;
        $candidate->first_name = $validated['full_name'];

        if ($request->hasFile('picture')) {
            // Delete old photo if exists
            if ($candidate->photo_url && Storage::disk('public')->exists($candidate->photo_url)) {
                Storage::disk('public')->delete($candidate->photo_url);
            }
            $candidate->photo_url = $request->file('picture')->store('candidates', 'public');
        }

        $candidate->save();

        return redirect()->route('admin.candidates.index')
            ->with('success', 'Candidate updated successfully.');
    }

    /**
     * Remove the specified candidate.
     */
    public function destroy(Candidate $candidate)
    {
        // Delete photo if exists
        if ($candidate->photo_url && Storage::disk('public')->exists($candidate->photo_url)) {
            Storage::disk('public')->delete($candidate->photo_url);
        }

        $candidate->delete();

        return redirect()->route('admin.candidates.index')
            ->with('success', 'Candidate deleted successfully.');
    }
}
