<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Activity::with(['activityType', 'creator', 'quiz.questions', 'assignment'])
            ->where('created_by', auth()->id());

        // Apply filters if provided
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('type') && $request->type) {
            $query->where('activity_type_id', $request->type);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->latest()->get()->map(function ($activity) {
            // Add computed properties
            $activity->question_count = $activity->quiz?->questions?->count() ?? 0;
            $activity->total_points = $activity->quiz?->questions?->sum('points') ?? 0;
            $activity->has_due_date = $activity->assignment?->due_date ? true : false;
            
            // TODO: Add module usage - will be implemented when module-activity relationship is added
            $activity->used_in_modules = [];
            
            return $activity;
        });

        $activityTypes = ActivityType::all();

        return Inertia::render('ActivityManagement/Index', [
            'activities' => $activities,
            'activityTypes' => $activityTypes,
            'filters' => [
                'search' => $request->search,
                'type' => $request->type,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $activityTypes = ActivityType::all();
        
        return Inertia::render('ActivityManagement/Create', [
            'activityTypes' => $activityTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activity_type_id' => 'required|exists:activity_types,id',
        ]);

        $activity = Activity::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('activities.show', $activity->id)
            ->with('success', "'{$activity->title}' activity created successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity): Response
    {
        $activity->load(['activityType', 'creator', 'quiz.questions.options', 'assignment.document']);

        return Inertia::render('ActivityManagement/Show', [
            'activity' => $activity,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity): Response
    {
        $activityTypes = ActivityType::all();
        
        return Inertia::render('ActivityManagement/Edit', [
            'activity' => $activity,
            'activityTypes' => $activityTypes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activity_type_id' => 'required|exists:activity_types,id',
        ]);

        $activity->update($validated);

        return redirect()->route('activities.show', $activity->id)
            ->with('success', 'Activity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Activity deleted successfully.');
    }
}
