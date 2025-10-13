<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Schedule;
use App\Models\ScheduleActivity;
use App\Models\ScheduleParticipant;
use App\Services\QuizCsvUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        DB::beginTransaction();
        
        try {
            $activity = Activity::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'activity_type_id' => $validated['activity_type_id'],
                'due_date' => $validated['due_date'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Automatically create schedule if due_date is provided
            if (!empty($validated['due_date'])) {
                $this->createScheduleForActivity($activity);
            }

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

                    DB::commit();
                    
                    return redirect()->route('activities.show', $activity->id)
                        ->with('success', "Activity '{$activity->title}' created successfully with {$result['questions_count']} questions!");

                } catch (\Exception $e) {
                    // If CSV processing fails, we still keep the activity but show error
                    DB::commit();
                    
                    return redirect()->route('activities.show', $activity->id)
                        ->withErrors(['csv_upload' => 'Activity created but CSV upload failed: ' . $e->getMessage()])
                        ->with('warning', "Activity '{$activity->title}' created, but quiz upload failed. You can upload the quiz manually.");
                }
            }

            DB::commit();
            
            return redirect()->route('activities.show', $activity->id)
                ->with('success', "'{$activity->title}' activity created successfully.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create activity: ' . $e->getMessage()]);
        }
    }

    /**
     * Create a schedule for an activity based on its due date
     */
    private function createScheduleForActivity(Activity $activity)
    {
        if (!$activity->due_date) {
            return;
        }

        // Create schedule (to_datetime is the due_date, from_datetime is 1 hour before)
        $toDatetime = $activity->due_date;
        $fromDatetime = $activity->due_date->copy()->subHour();

        $schedule = Schedule::create([
            'schedule_type_id' => 1, // Activity type
            'title' => $activity->title,
            'description' => $activity->description,
            'from_datetime' => $fromDatetime,
            'to_datetime' => $toDatetime,
            'is_all_day' => false,
            'is_recurring' => false,
            'status' => 'scheduled',
            'created_by' => $activity->created_by,
            'schedulable_type' => Activity::class,
            'schedulable_id' => $activity->id,
        ]);

        // Create schedule_activity record
        ScheduleActivity::create([
            'schedule_id' => $schedule->id,
            'activity_id' => $activity->id,
            'submission_deadline' => $toDatetime,
            'passing_score' => $activity->passing_percentage,
        ]);

        // Add the activity creator as a participant (organizer role)
        ScheduleParticipant::create([
            'schedule_id' => $schedule->id,
            'user_id' => $activity->created_by,
            'role_in_schedule' => 'organizer',
            'participation_status' => 'accepted',
        ]);

        return $schedule;
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

        DB::beginTransaction();
        
        try {
            $activity->update($validated);

            // Handle schedule updates based on due_date changes
            $this->syncScheduleForActivity($activity);

            DB::commit();
            
            return redirect()->route('activities.show', $activity->id)
                ->with('success', 'Activity updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update activity: ' . $e->getMessage()]);
        }
    }

    /**
     * Sync schedule for an activity (create, update, or delete based on due_date)
     */
    private function syncScheduleForActivity(Activity $activity)
    {
        // Find existing schedule for this activity
        $existingSchedule = Schedule::where('schedulable_type', Activity::class)
            ->where('schedulable_id', $activity->id)
            ->where('schedule_type_id', 1) // Activity type
            ->first();

        if ($activity->due_date) {
            // If activity has due_date, create or update schedule
            if ($existingSchedule) {
                // Update existing schedule
                $toDatetime = $activity->due_date;
                $fromDatetime = $activity->due_date->copy()->subHour();

                $existingSchedule->update([
                    'title' => $activity->title,
                    'description' => $activity->description,
                    'from_datetime' => $fromDatetime,
                    'to_datetime' => $toDatetime,
                ]);

                // Update schedule_activity record
                $existingSchedule->activityDetails()->updateOrCreate(
                    ['schedule_id' => $existingSchedule->id],
                    [
                        'activity_id' => $activity->id,
                        'submission_deadline' => $toDatetime,
                        'passing_score' => $activity->passing_percentage,
                    ]
                );
            } else {
                // Create new schedule
                $this->createScheduleForActivity($activity);
            }
        } else {
            // If activity no longer has due_date, delete associated schedule
            if ($existingSchedule) {
                $existingSchedule->activityDetails()->delete();
                $existingSchedule->delete();
            }
        }
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
