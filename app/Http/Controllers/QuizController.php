<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Redirect to activities index since quizzes are managed through activities
        return redirect()->route('activities.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Redirect to activities index since quizzes are created through activities
        return redirect()->route('activities.index')
            ->with('info', 'Create an activity first, then add a quiz to it.');
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
        ]);

        $quiz = Quiz::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        // Redirect to the activity show page
        return redirect()->route('activities.show', $validated['activity_id'])
            ->with('success', 'Quiz created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Quiz $quiz)
    {
        // Redirect to the parent activity show page
        return redirect()->route('activities.show', $quiz->activity_id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quiz $quiz)
    {
        // Redirect to the parent activity show page for inline editing
        return redirect()->route('activities.show', $quiz->activity_id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $quiz->update($validated);

        return redirect()->route('quizzes.show', $quiz->id)
            ->with('success', 'Quiz updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz)
    {
        $activityId = $quiz->activity_id;
        $quiz->delete();

        return redirect()->route('activities.show', $activityId)
            ->with('success', 'Quiz deleted successfully.');
    }
}
