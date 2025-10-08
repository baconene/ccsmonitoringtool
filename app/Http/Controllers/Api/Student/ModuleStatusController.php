<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\StudentActivity;
use App\Models\ModuleCompletion;
use App\Models\CourseEnrollment;
use App\Traits\DynamicMessageTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ModuleStatusController extends Controller
{
    use DynamicMessageTrait;
    
    /**
     * Get dynamic relationships to load based on activity types
     * Caches the result for better performance
     */
    private function getActivityProgressRelationships(): array
    {
        static $cachedRelationships = null;
        
        if ($cachedRelationships === null) {
            $baseRelationships = ['activity'];
            
            // Get all activity types and map them to their corresponding progress relationships
            $activityTypes = \App\Models\ActivityType::all();
            
            foreach ($activityTypes as $activityType) {
                $relationshipName = $this->mapActivityTypeToRelationship($activityType);
                if ($relationshipName) {
                    $baseRelationships[] = $relationshipName;
                }
            }
            
            $cachedRelationships = array_unique($baseRelationships);
        }
        
        return $cachedRelationships;
    }
    
    /**
     * Map activity type to its corresponding progress relationship
     * Uses both name and description to determine the correct relationship
     * 
     * To add support for new activity types:
     * 1. Add the activity type to the database with name and description
     * 2. Create the corresponding StudentXxxProgress model and relationship
     * 3. Add the mapping in the arrays below
     * 
     * @param \App\Models\ActivityType $activityType
     * @return string|null The relationship name or null if no mapping found
     */
    private function mapActivityTypeToRelationship(\App\Models\ActivityType $activityType): ?string
    {
        $typeName = strtolower($activityType->name);
        $description = strtolower($activityType->description);
        
        // Primary mapping based on activity type names
        $nameMapping = [
            'quiz' => 'quizProgress',
            'assignment' => 'assignmentProgress', 
            'project' => 'projectProgress',
            'assessment' => 'assessmentProgress',
            'exercise' => 'exerciseProgress',
        ];
        
        // Secondary mapping based on description keywords
        $descriptionMapping = [
            'quiz' => 'quizProgress',
            'multiple questions' => 'quizProgress',
            'knowledge assessment' => 'quizProgress',
            'assignment' => 'assignmentProgress',
            'document submission' => 'assignmentProgress',
            'grading' => 'assignmentProgress',
            'project' => 'projectProgress',
            'assessment' => 'assessmentProgress',
            'comprehensive assessment' => 'assessmentProgress',
            'evaluate student competency' => 'assessmentProgress',
            'competency' => 'assessmentProgress',
            'exercise' => 'exerciseProgress',
            'practice' => 'exerciseProgress',
            'skill development' => 'exerciseProgress',
            'reinforcement' => 'exerciseProgress',
        ];
        
        // First try name mapping
        if (isset($nameMapping[$typeName])) {
            return $nameMapping[$typeName];
        }
        
        // Then try description mapping
        foreach ($descriptionMapping as $keyword => $relationship) {
            if (strpos($description, $keyword) !== false) {
                return $relationship;
            }
        }
        
        return null;
    }
    
    /**
     * Get the status of a module for the authenticated student
     */
    public function getModuleStatus(Request $request, $moduleId): JsonResponse
    {
        $user = Auth::user();
        
        // Get the student record for this user
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return response()->json([
                'error' => 'Student record not found'
            ], 404);
        }
        
        // Get the module with its related data
        $module = Module::with(['course', 'activities', 'lessons'])->findOrFail($moduleId);
        
        // Check if student is enrolled in the course
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $module->course_id)
            ->first();
            
        if (!$enrollment) {
            return response()->json([
                'error' => 'You are not enrolled in this course'
            ], 403);
        }

        // Get all student activities for this module with dynamic relationships
        $studentActivities = StudentActivity::with($this->getActivityProgressRelationships())
        ->where('student_id', $student->id)
        ->where('module_id', $moduleId)
        ->get();

        // Calculate activity completion status
        $activityStatus = $this->calculateActivityStatus($studentActivities, $module);
        
        // Calculate lesson completion status
        $lessonStatus = $this->calculateLessonStatus($user->id, $module);
        
        // Check overall module completion
        $moduleCompletion = ModuleCompletion::where('user_id', $user->id)
            ->where('module_id', $moduleId)
            ->where('course_id', $module->course_id)
            ->first();

        // Determine if module can be marked as complete
        $canMarkComplete = $this->canMarkModuleComplete($activityStatus, $lessonStatus, $module);
        
        // Calculate overall progress
        $overallProgress = $this->calculateOverallProgress($activityStatus, $lessonStatus);

        return response()->json([
            'module_id' => $moduleId,
            'module_title' => $module->title,
            'module_type' => $module->module_type,
            'course_id' => $module->course_id,
            'course_title' => $module->course->title,
            'is_completed' => $moduleCompletion ? true : false,
            'completed_at' => $moduleCompletion?->completed_at,
            'can_mark_complete' => $canMarkComplete,
            'overall_progress_percentage' => $overallProgress,
            'activity_status' => $activityStatus,
            'lesson_status' => $lessonStatus,
            'detailed_progress' => [
                'total_activities' => $activityStatus['total'],
                'completed_activities' => $activityStatus['completed'],
                'in_progress_activities' => $activityStatus['in_progress'],
                'not_started_activities' => $activityStatus['not_started'],
                'total_lessons' => $lessonStatus['total'],
                'completed_lessons' => $lessonStatus['completed'],
            ],
            'next_actions' => $this->getNextActions($studentActivities, $module, $canMarkComplete),
        ]);
    }

    /**
     * Mark a module as complete for the authenticated student
     */
    public function markModuleComplete(Request $request, $moduleId): JsonResponse
    {
        $user = Auth::user();
        
        // Get the student record for this user
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return response()->json([
                'error' => 'Student record not found'
            ], 404);
        }
        
        $module = Module::with(['course', 'activities', 'lessons'])->findOrFail($moduleId);
        
        // Check enrollment
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $module->course_id)
            ->first();
            
        if (!$enrollment) {
            return response()->json([
                'error' => 'You are not enrolled in this course'
            ], 403);
        }

        // Check if already completed
        $existingCompletion = ModuleCompletion::where('user_id', $user->id)
            ->where('module_id', $moduleId)
            ->where('course_id', $module->course_id)
            ->first();
            
        if ($existingCompletion) {
            return response()->json([
                'message' => 'Module already completed',
                'completed_at' => $existingCompletion->completed_at
            ]);
        }

        // Get student activities and lesson status
        $studentActivities = StudentActivity::where('student_id', $student->id)
            ->where('module_id', $moduleId)
            ->get();

        $activityStatus = $this->calculateActivityStatus($studentActivities, $module);
        $lessonStatus = $this->calculateLessonStatus($user->id, $module);

        // Check if module can be completed
        if (!$this->canMarkModuleComplete($activityStatus, $lessonStatus, $module)) {
            $missingActivities = $activityStatus['total'] - $activityStatus['completed'];
            $missingLessons = $lessonStatus['total'] - $lessonStatus['completed'];
            
            $errorMessage = "Cannot complete '{$module->title}' module yet. ";
            if ($missingActivities > 0 && $missingLessons > 0) {
                $errorMessage .= "You need to complete {$missingActivities} more activities and {$missingLessons} more lessons.";
            } elseif ($missingActivities > 0) {
                $errorMessage .= "You need to complete {$missingActivities} more activities.";
            } elseif ($missingLessons > 0) {
                $errorMessage .= "You need to complete {$missingLessons} more lessons.";
            }
            
            return response()->json([
                'error' => $errorMessage,
                'requirements' => [
                    'activities_completed' => $activityStatus['completed'],
                    'activities_required' => $activityStatus['total'],
                    'lessons_completed' => $lessonStatus['completed'],
                    'lessons_required' => $lessonStatus['total'],
                ]
            ], 422);
        }

        // Create module completion record
        $completion = ModuleCompletion::create([
            'user_id' => $user->id,
            'module_id' => $moduleId,
            'course_id' => $module->course_id,
            'completed_at' => Carbon::now(),
            'completion_data' => [
                'activities_completed' => $activityStatus['completed'],
                'lessons_completed' => $lessonStatus['completed'],
                'completion_method' => 'manual',
            ]
        ]);

        // Update course enrollment progress
        $enrollment->updateProgress();

        return response()->json([
            'message' => $this->getModelSuccessMessage('completed', $module),
            'completion_id' => $completion->id,
            'completed_at' => $completion->completed_at,
            'course_progress' => $enrollment->progress,
        ], 201);
    }

    /**
     * Calculate activity completion status
     */
    private function calculateActivityStatus($studentActivities, $module): array
    {
        $totalActivities = $module->activities->count();
        $completed = $studentActivities->where('status', 'completed')->count() + 
                    $studentActivities->where('status', 'submitted')->count() +
                    $studentActivities->where('status', 'graded')->count();
        $inProgress = $studentActivities->where('status', 'in_progress')->count();
        $notStarted = $totalActivities - $completed - $inProgress;

        return [
            'total' => $totalActivities,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'not_started' => max(0, $notStarted),
            'completion_percentage' => $totalActivities > 0 ? ($completed / $totalActivities) * 100 : 0,
        ];
    }

    /**
     * Calculate lesson completion status
     */
    private function calculateLessonStatus($userId, $module): array
    {
        $totalLessons = $module->lessons->count();
        $completedCount = 0;

        foreach ($module->lessons as $lesson) {
            $completion = \App\Models\LessonCompletion::where('user_id', $userId)
                ->where('lesson_id', $lesson->id)
                ->where('course_id', $module->course_id)
                ->exists();
            
            if ($completion) {
                $completedCount++;
            }
        }

        return [
            'total' => $totalLessons,
            'completed' => $completedCount,
            'completion_percentage' => $totalLessons > 0 ? ($completedCount / $totalLessons) * 100 : 0,
        ];
    }

    /**
     * Determine if module can be marked as complete
     */
    private function canMarkModuleComplete($activityStatus, $lessonStatus, $module): bool
    {
        // For mixed modules, both activities and lessons must be completed
        if ($module->module_type === 'mixed') {
            return $activityStatus['completed'] === $activityStatus['total'] &&
                   $lessonStatus['completed'] === $lessonStatus['total'];
        }

        // For activity-only modules
        if ($module->module_type === 'activity') {
            return $activityStatus['completed'] === $activityStatus['total'];
        }

        // For lesson-only modules
        if ($module->module_type === 'lesson') {
            return $lessonStatus['completed'] === $lessonStatus['total'];
        }

        // Default: both must be completed
        return $activityStatus['completed'] === $activityStatus['total'] &&
               $lessonStatus['completed'] === $lessonStatus['total'];
    }

    /**
     * Calculate overall progress percentage
     */
    private function calculateOverallProgress($activityStatus, $lessonStatus): float
    {
        $totalItems = $activityStatus['total'] + $lessonStatus['total'];
        $completedItems = $activityStatus['completed'] + $lessonStatus['completed'];

        return $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 1) : 0;
    }

    /**
     * Get suggested next actions for the student
     */
    private function getNextActions($studentActivities, $module, $canMarkComplete): array
    {
        $actions = [];

        if ($canMarkComplete) {
            $actions[] = [
                'type' => 'complete_module',
                'title' => 'Mark Module as Complete',
                'description' => "Complete the '{$module->title}' module to proceed to the next section of your course.",
                'priority' => 'high'
            ];
            return $actions;
        }

        // Find incomplete activities
        $moduleActivityIds = $module->activities->pluck('id')->toArray();
        $completedActivityIds = $studentActivities->whereIn('status', ['completed', 'submitted', 'graded'])
            ->pluck('activity_id')->toArray();
        
        $incompleteActivityIds = array_diff($moduleActivityIds, $completedActivityIds);
        
        foreach ($incompleteActivityIds as $activityId) {
            $activity = $module->activities->firstWhere('id', $activityId);
            if ($activity) {
                $actions[] = [
                    'type' => 'complete_activity',
                    'title' => $this->getActivityCompletionMessage($activity),
                    'description' => $this->getActivityDescription($activity, 'Complete this {type} to progress in the module.'),
                    'activity_id' => $activityId,
                    'activity_type' => $activity->activityType->name ?? 'activity',
                    'priority' => 'medium'
                ];
            }
        }

        return $actions;
    }
}
