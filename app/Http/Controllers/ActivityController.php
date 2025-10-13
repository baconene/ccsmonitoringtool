<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityType;
use App\Services\QuizCsvUploadService;
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
            // Load modules relationship
            $activity->load('modules');
            
            // Add computed properties
            $activity->question_count = $activity->quiz?->questions?->count() ?? 0;
            $activity->total_points = $activity->quiz?->questions?->sum('points') ?? 0;
            $activity->has_due_date = $activity->assignment?->due_date ? true : false;
            
            // Add module usage information
            $activity->used_in_modules = $activity->modules->map(function ($module) {
                return [
                    'id' => $module->id,
                    'title' => $module->title,
                ];
            })->toArray();
            
            $activity->modules_count = $activity->modules->count();
            
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
            'due_date' => 'nullable|date',
            'create_with_csv' => 'boolean',
            'quiz_title' => 'required_if:create_with_csv,true|string|max:255',
            'quiz_description' => 'nullable|string',
            'csv_file' => 'required_if:create_with_csv,true|file|mimes:csv,txt|max:512000'
        ]);

        $activity = Activity::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'activity_type_id' => $validated['activity_type_id'],
            'due_date' => $validated['due_date'] ?? null,
            'created_by' => auth()->id(),
        ]);

        // If creating with CSV upload, process the quiz
        if ($request->boolean('create_with_csv') && $request->hasFile('csv_file')) {
            try {
                $uploadService = app(QuizCsvUploadService::class);
                
                $result = $uploadService->processQuizCsv(
                    $request->file('csv_file'),
                    $activity->id,
                    $validated['quiz_title'],
                    $validated['quiz_description'] ?? null
                );

                return redirect()->route('activities.show', $activity->id)
                    ->with('success', "Activity '{$activity->title}' created successfully with {$result['questions_count']} questions!");

            } catch (\Exception $e) {
                // If CSV processing fails, we still keep the activity but show error
                return redirect()->route('activities.show', $activity->id)
                    ->withErrors(['csv_upload' => 'Activity created but CSV upload failed: ' . $e->getMessage()])
                    ->with('warning', "Activity '{$activity->title}' created, but quiz upload failed. You can upload the quiz manually.");
            }
        }

        return redirect()->route('activities.show', $activity->id)
            ->with('success', "'{$activity->title}' activity created successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity): Response
    {
        $activity->load([
            'activityType', 
            'creator', 
            'quiz.questions.options', 
            'assignment.document',
            'modules.course'
        ]);

        // Get unique courses through modules
        $courses = $activity->modules->map(function ($module) {
            return [
                'id' => $module->course->id,
                'title' => $module->course->title,
                'description' => $module->course->description,
                'module_id' => $module->id,
                'module_title' => $module->title,
            ];
        })->unique('id')->values();

        // Count modules and courses
        $activity->modules_count = $activity->modules->count();
        $activity->courses_count = $courses->count();
        $activity->related_courses = $courses;

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
            'due_date' => 'nullable|date',
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
