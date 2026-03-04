<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Assignment;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentQuestionOption;
use App\Models\CourseEnrollment;
use App\Models\Student;
use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;
use App\Models\Schedule;
use App\Models\ScheduleActivity;
use App\Models\ScheduleParticipant;
use App\Services\QuizCsvUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Activity::with(['activityType', 'creator', 'quiz.questions', 'assignment.questions'])
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
            $activity->question_count = $activity->quiz
                ? ($activity->quiz?->questions?->count() ?? 0)
                : ($activity->assignment?->questions?->count() ?? 0);
            $activity->total_points = $activity->quiz
                ? ($activity->quiz?->questions?->sum('points') ?? 0)
                : (($activity->assignment?->questions?->sum('points') ?? 0) ?: ($activity->assignment?->total_points ?? 0));
            $activity->has_due_date = $activity->assignment?->due_date ? true : false;
            
            // Add module usage information
            $activity->used_in_modules = $activity->modules->map(function ($module) {
                return [
                    'id' => $module->id,
                    'title' => $module->title,
                ];
            })->toArray();
            
            return $activity->append(['modules_count']);
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

            // Load the activity type to determine what model to create
            $activityType = $activity->activityType;
            
            Log::info('Activity created', [
                'activity_id' => $activity->id,
                'activity_type' => $activityType->name,
                'model' => $activityType->model,
            ]);

            // Create the corresponding model based on activity type
            $this->createActivityTypeModel($activity, $activityType);

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
            
            Log::error('Failed to create activity', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create activity: ' . $e->getMessage()]);
        }
    }

    /**
     * Create the appropriate model based on activity type
     */
    private function createActivityTypeModel(Activity $activity, $activityType)
    {
        try {
            // Get the model class from activity type
            $modelClass = $activityType->model;
            
            if (!$modelClass) {
                Log::info('No model defined for activity type', [
                    'activity_type_id' => $activityType->id,
                    'activity_type_name' => $activityType->name,
                ]);
                return;
            }

            $modelName = strtolower(class_basename($modelClass));
            $typeName = strtolower($activityType->name ?? '');
            $resolvedType = $modelName ?: $typeName;

            // Map activity type names to model classes
            switch ($resolvedType) {
                case 'assignment':
                    Assignment::create([
                        'activity_id' => $activity->id,
                        'created_by' => auth()->id(),
                        'title' => $activity->title,
                        'description' => $activity->description,
                        'assignment_type' => 'objective',
                        'total_points' => 100,
                    ]);
                    Log::info('Assignment created for activity', [
                        'activity_id' => $activity->id,
                    ]);
                    break;

                case 'quiz':
                    // Check if Quiz model exists
                    if (class_exists('App\Models\Quiz')) {
                        \App\Models\Quiz::create([
                            'activity_id' => $activity->id,
                            'created_by' => auth()->id(),
                            'title' => $activity->title,
                            'description' => $activity->description,
                            'total_points' => 100,
                        ]);
                        Log::info('Quiz created for activity', [
                            'activity_id' => $activity->id,
                        ]);
                    }
                    break;

                case 'lesson':
                    // Lesson might not need a separate model or it's handled differently
                    Log::info('Lesson activity created (no separate model needed)', [
                        'activity_id' => $activity->id,
                    ]);
                    break;

                default:
                    Log::warning('Unknown activity type model', [
                        'activity_type_model' => $modelClass,
                        'activity_id' => $activity->id,
                    ]);
            }
        } catch (\Exception $e) {
            Log::error('Error creating activity type model', [
                'activity_id' => $activity->id,
                'error' => $e->getMessage(),
            ]);
            // Don't throw - we want the activity to be created even if the specific model fails
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
            'assignment.questions.options',
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
        $activity->courses_count = $courses->count();
        $activity->related_courses = $courses;
        $activity->append(['modules_count']);

        // Get student progress for ANY activity type (Quiz, Assignment, etc.)
        $studentsProgress = $this->getStudentProgress($activity);

        return Inertia::render('ActivityManagement/Show', [
            'activity' => $activity,
            'studentsProgress' => $studentsProgress,
        ]);
    }

    /**
     * Get student progress for any activity type (Quiz, Assignment, etc.)
     * Returns unified format for all activity types
     */
    private function getStudentProgress(Activity $activity): array
    {
        $courseId = $activity->modules()->value('course_id');

        if (!$courseId) {
            return [];
        }

        $enrollments = CourseEnrollment::with(['student.user', 'user'])
            ->where('course_id', $courseId)
            ->get();

        $activityType = $activity->activityType?->name ?? 'Unknown';
        $totalQuestions = 0;

        // Get total questions based on activity type
        if ($activity->quiz) {
            $totalQuestions = $activity->quiz->questions()->count();
        } elseif ($activity->assignment) {
            $totalQuestions = $activity->assignment->questions()->count();
        }

        return $enrollments->map(function ($enrollment) use ($activity, $courseId, $totalQuestions, $activityType) {
            $student = $enrollment->student;

            if (!$student && $enrollment->user_id) {
                $student = Student::with('user')
                    ->where('user_id', $enrollment->user_id)
                    ->first();
            }

            $studentId = $student?->id;
            $studentUser = $student?->user ?? $enrollment->user;

            // Get progress record
            $progress = null;
            $activityTypeDb = strtolower($activityType);
            
            if ($studentId) {
                $progress = StudentActivityProgress::where('student_id', $studentId)
                    ->where('activity_id', $activity->id)
                    ->where('activity_type', $activityTypeDb)
                    ->latest('updated_at')
                    ->first();
            }

            // Get student activity record
            $studentActivity = null;
            if ($studentId) {
                $studentActivity = StudentActivity::where('student_id', $studentId)
                    ->where('activity_id', $activity->id)
                    ->where('course_id', $courseId)
                    ->first();
            }

            // Calculate progress percentage
            $takingActivity = $progress && (
                $progress->status !== 'not_started'
                || (int) ($progress->answered_questions ?? 0) > 0
                || $progress->started_at !== null
                || $progress->submitted_at !== null
            );

            // Determine status
            $status = 'not_started';
            if ($progress) {
                if ($progress->graded_at !== null || in_array($progress->status, ['graded', 'completed'], true)) {
                    $status = 'graded';
                } elseif ($progress->submitted_at !== null || $progress->is_submitted || $progress->status === 'submitted') {
                    $status = 'submitted';
                } elseif ($takingActivity || $progress->status === 'in_progress') {
                    $status = 'in_progress';
                }
            } elseif ($studentActivity && in_array($studentActivity->status, ['in_progress', 'submitted', 'graded'], true)) {
                $status = $studentActivity->status;
            }

            $answeredQuestions = (int) ($progress?->answered_questions ?? 0);
            $progressPercentage = $progress?->progress_percentage;
            
            if ($progressPercentage === null) {
                // For graded/completed activities with scores, use score percentage
                if ($status === 'graded' && $progress?->score !== null && $progress?->max_score > 0) {
                    $progressPercentage = round(($progress->score / $progress->max_score) * 100, 2);
                } elseif ($status === 'graded' && $studentActivity?->score !== null && $studentActivity?->max_score > 0) {
                    $progressPercentage = round(($studentActivity->score / $studentActivity->max_score) * 100, 2);
                } else {
                    // For in-progress activities, use answered questions percentage
                    $progressPercentage = $totalQuestions > 0
                        ? round(($answeredQuestions / $totalQuestions) * 100, 2)
                        : 0;
                }
            }

            return [
                'student_id' => $studentId,
                'student_name' => $studentUser?->name ?? 'Unknown',
                'student_number' => $student?->student_id_text ?? 'N/A',
                'student_email' => $studentUser?->email ?? 'N/A',
                'is_taking_activity' => (bool) $takingActivity,
                'status' => $status,
                'submission_status' => $progress?->submission_status ?? 'not_started',
                'answered_questions' => $answeredQuestions,
                'total_questions' => $totalQuestions,
                'progress_percentage' => (float) $progressPercentage,
                'score' => $progress?->score ?? $studentActivity?->score ?? null,
                'max_score' => $progress?->max_score ?? $progress?->points_possible ?? $studentActivity?->max_score ?? 100,
                'percentage_score' => $progress?->percentage_score ?? $studentActivity?->percentage_score ?? null,
                'submitted_at' => $progress?->submitted_at?->format('Y-m-d H:i:s') ?? $studentActivity?->submitted_at?->format('Y-m-d H:i:s'),
                'graded_at' => $progress?->graded_at?->format('Y-m-d H:i:s') ?? $studentActivity?->graded_at?->format('Y-m-d H:i:s'),
                'requires_grading' => (bool) ($progress?->requires_grading ?? false),
                'can_review' => in_array($status, ['submitted', 'graded'], true) && ($progress?->student_activity_id || $studentActivity?->id),
                'student_activity_id' => $progress?->student_activity_id ?? $studentActivity?->id,
            ];
        })->values()->toArray();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity): Response
    {
        $activityTypes = ActivityType::all();
        
        // Load assignment with questions if available
        $activity->load(['assignment.questions.options']);
        
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
        \Log::info('Activity update request received', [
            'activity_id' => $activity->id,
            'has_assignment' => (bool) $activity->assignment,
            'request_content_type' => $request->header('Content-Type'),
        ]);

        // Log the actual request payload
        $allData = $request->all();
        \Log::info('Request payload', [
            'keys' => array_keys($allData),
            'has_questions' => isset($allData['questions']),
            'questions_count' => isset($allData['questions']) ? count($allData['questions']) : 0,
        ]);

        // Build validation rules based on activity type
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activity_type_id' => 'required|exists:activity_types,id',
            'due_date' => 'nullable|date',
        ];

        // Add assignment-related validation if this activity has an assignment
        if ($activity->assignment) {
            $rules = array_merge($rules, [
                'instructions' => 'nullable|string',
                'total_points' => 'required|integer|min:1',
                'time_limit' => 'nullable|integer|min:1',
                'allow_late_submission' => 'boolean',
                'questions' => 'nullable|array',
                'questions.*.id' => 'nullable|integer|exists:assignment_questions,id',
                'questions.*.question_text' => 'required|string',
                'questions.*.question_type' => 'required|in:true_false,multiple_choice,enumeration,short_answer',
                'questions.*.points' => 'required|integer|min:1',
                'questions.*.correct_answer' => 'nullable|string',
                'questions.*.acceptable_answers' => 'nullable|array',
                'questions.*.case_sensitive' => 'boolean',
                'questions.*.explanation' => 'nullable|string',
                'questions.*.options' => 'nullable|array',
                'questions.*.options.*.id' => 'nullable|integer|exists:assignment_question_options,id',
                'questions.*.options.*.option_text' => 'required|string',
                'questions.*.options.*.is_correct' => 'boolean',
                'deleted_question_ids' => 'nullable|array',
                'deleted_question_ids.*' => 'integer|exists:assignment_questions,id',
            ]);
        }

        $validated = $request->validate($rules);

        Log::info('Validated data', [
            'has_questions' => !empty($validated['questions']),
            'questions_count' => is_array($validated['questions'] ?? null) ? count($validated['questions']) : 0,
            'deleted_ids_count' => is_array($validated['deleted_question_ids'] ?? null) ? count($validated['deleted_question_ids']) : 0,
        ]);

        DB::beginTransaction();
        
        try {
            // Update the activity
            $activity->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'activity_type_id' => $validated['activity_type_id'],
                'due_date' => $validated['due_date'] ?? null,
            ]);

            // Handle schedule updates based on due_date changes
            $this->syncScheduleForActivity($activity);

            // If activity has an assignment, update assignment data
            if ($activity->assignment && $activity->assignment->id) {
                $assignment = $activity->assignment;

                // Update basic assignment fields
                $assignment->update([
                    'title' => $validated['title'],
                    'description' => $validated['description'] ?? null,
                    'instructions' => $validated['instructions'] ?? null,
                    'total_points' => $validated['total_points'] ?? 0,
                    'time_limit' => $validated['time_limit'] ?? null,
                    'allow_late_submission' => $validated['allow_late_submission'] ?? false,
                ]);

                // Delete removed questions
                if (!empty($validated['deleted_question_ids'])) {
                    AssignmentQuestion::whereIn('id', $validated['deleted_question_ids'])->delete();
                }

                // Update or create questions
                if (!empty($validated['questions'])) {
                    foreach ($validated['questions'] as $index => $questionData) {
                        if (!empty($questionData['id'])) {
                            // Update existing question
                            $question = AssignmentQuestion::find($questionData['id']);
                            if ($question) {
                                $question->update([
                                    'question_text' => $questionData['question_text'],
                                    'question_type' => $questionData['question_type'],
                                    'points' => $questionData['points'],
                                    'correct_answer' => $questionData['correct_answer'] ?? null,
                                    'acceptable_answers' => $questionData['acceptable_answers'] ?? null,
                                    'case_sensitive' => $questionData['case_sensitive'] ?? false,
                                    'explanation' => $questionData['explanation'] ?? null,
                                    'order' => $index + 1,
                                ]);

                                // Sync options
                                if (!empty($questionData['options'])) {
                                    $existingOptionIds = $question->options()->pluck('id')->toArray();
                                    $updatedOptionIds = [];

                                    foreach ($questionData['options'] as $optionData) {
                                        if (!empty($optionData['id'])) {
                                            // Update existing option
                                            $question->options()->where('id', $optionData['id'])->update([
                                                'option_text' => $optionData['option_text'],
                                                'is_correct' => $optionData['is_correct'] ?? false,
                                            ]);
                                            $updatedOptionIds[] = $optionData['id'];
                                        } else {
                                            // Create new option
                                            $option = $question->options()->create([
                                                'option_text' => $optionData['option_text'],
                                                'is_correct' => $optionData['is_correct'] ?? false,
                                            ]);
                                            $updatedOptionIds[] = $option->id;
                                        }
                                    }

                                    // Delete removed options
                                    $optionsToDelete = array_diff($existingOptionIds, $updatedOptionIds);
                                    if (!empty($optionsToDelete)) {
                                        AssignmentQuestionOption::whereIn('id', $optionsToDelete)->delete();
                                    }
                                }
                            }
                        } else {
                            // Create new question
                            $question = $assignment->questions()->create([
                                'question_text' => $questionData['question_text'],
                                'question_type' => $questionData['question_type'],
                                'points' => $questionData['points'],
                                'correct_answer' => $questionData['correct_answer'] ?? null,
                                'acceptable_answers' => $questionData['acceptable_answers'] ?? null,
                                'case_sensitive' => $questionData['case_sensitive'] ?? false,
                                'explanation' => $questionData['explanation'] ?? null,
                                'order' => $index + 1,
                            ]);

                            // Create options for new question
                            if (!empty($questionData['options'])) {
                                foreach ($questionData['options'] as $optionData) {
                                    $question->options()->create([
                                        'option_text' => $optionData['option_text'],
                                        'is_correct' => $optionData['is_correct'] ?? false,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

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
