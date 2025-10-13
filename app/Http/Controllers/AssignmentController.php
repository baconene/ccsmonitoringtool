<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Redirect to activities index since assignments are managed through activities
        return redirect()->route('activities.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Redirect to activities index since assignments are created through activities
        return redirect()->route('activities.index')
            ->with('info', 'Create an activity first, then add an assignment to it.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_id' => 'nullable|exists:documents,id',
        ]);

        $assignment = Assignment::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        // Redirect to the activity show page
        return redirect()->route('activities.show', $validated['activity_id'])
            ->with('success', 'Assignment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment)
    {
        // Redirect to the parent activity show page
        return redirect()->route('activities.show', $assignment->activity_id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assignment $assignment)
    {
        // Redirect to the parent activity show page for inline editing
        return redirect()->route('activities.show', $assignment->activity_id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_id' => 'nullable|exists:documents,id',
        ]);

        $assignment->update($validated);

        return redirect()->route('assignments.show', $assignment->id)
            ->with('success', 'Assignment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        $activityId = $assignment->activity_id;
        $assignment->delete();

        return redirect()->route('activities.show', $activityId)
            ->with('success', 'Assignment deleted successfully.');
    }
}
