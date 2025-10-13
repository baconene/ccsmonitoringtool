<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Module;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Student;
use App\Models\StudentActivity;
use App\Models\StudentQuizProgress;
use App\Models\ModuleCompletion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GradeCalculatorService
{
    /**
     * Default activity type weights (as percentages of the 80% activity portion)
     */
    private const DEFAULT_ACTIVITY_WEIGHTS = [
        'Quiz' => 30,
        'Assignment' => 15,
        'Assessment' => 35,
        'Exercise' => 20,
    ];

    /**
     * Module composition weights
     */
    private const MODULE_COMPONENT_WEIGHTS = [
        'lessons' => 20,    // Lessons contribute 20% to module score
        'activities' => 80, // Activities contribute 80% to module score
    ];

    /**
     * Calculate comprehensive grades for a student in a specific course
     * @param int $userId The user ID (from auth) of the student
     * @param int $courseId The course ID
     */
    public function calculateStudentCourseGrades(int $userId, int $courseId): array
    {
        $student = Student::with('user')->where('user_id', $userId)->firstOrFail();
        $course = Course::with([
            'modules.activities.activityType'
        ])->findOrFail($courseId);

        $moduleGrades = [];
        $totalWeightedScore = 0;
        $totalModuleWeight = 0;
        $overallActivities = 0;
        $completedActivities = 0;

        foreach ($course->modules as $module) {
            $moduleGrade = $this->calculateModuleGrade($userId, $module);
            $moduleGrades[] = $moduleGrade;
            
            // Calculate weighted contribution to overall grade
            $moduleWeight = $module->module_percentage ?? (100 / $course->modules->count());
            $totalWeightedScore += ($moduleGrade['module_score'] * $moduleWeight) / 100;
            $totalModuleWeight += $moduleWeight;
            
            // Track activity completion
            $overallActivities += count($moduleGrade['activities']);
            $completedActivities += collect($moduleGrade['activities'])->where('is_completed', true)->count();
        }

        $overallGrade = $totalModuleWeight > 0 ? ($totalWeightedScore * 100) / $totalModuleWeight : 0;

        return [
            'student' => [
                'id' => $student->id,
                'user_id' => $student->user_id,
                'name' => $student->user->name,
                'email' => $student->user->email,
                'student_id' => $student->student_id,
            ],
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
            ],
            'modules' => $moduleGrades,
            'overall_grade' => round($overallGrade, 2),
            'overall_letter_grade' => $this->getLetterGrade($overallGrade),
            'completion_status' => $this->getCourseCompletionStatus($moduleGrades),
            'activity_summary' => [
                'total' => $overallActivities,
                'completed' => $completedActivities,
                'completion_rate' => $overallActivities > 0 ? round(($completedActivities / $overallActivities) * 100, 1) : 0,
            ],
            'generated_at' => now(),
        ];
    }

    /**
     * Calculate grade for a specific module
     * @param int $userId The user ID of the student
     */
    public function calculateModuleGrade(int $userId, Module $module): array
    {
        // Calculate lesson score (20% of module)
        $lessonScore = $this->calculateLessonScore($userId, $module);
        
        // Calculate activity score (80% of module)
        $activityResult = $this->calculateModuleActivityScore($userId, $module);
        
        // Calculate final module score: (Lesson Score × 20%) + (Activity Score × 80%)
        $lessonContribution = ($lessonScore * self::MODULE_COMPONENT_WEIGHTS['lessons']) / 100;
        $activityContribution = ($activityResult['average_score'] * self::MODULE_COMPONENT_WEIGHTS['activities']) / 100;
        $moduleScore = $lessonContribution + $activityContribution;

        // Check if module is completed
        $moduleCompletion = ModuleCompletion::where('user_id', $userId)
            ->where('module_id', $module->id)
            ->first();

        return [
            'module_id' => $module->id,
            'module_title' => $module->description,
            'module_type' => $module->module_type ?? 'Mixed',
            'module_weight' => $module->module_percentage ?? (100 / $module->course->modules->count()),
            'module_score' => round($moduleScore, 2),
            'module_letter_grade' => $this->getLetterGrade($moduleScore),
            'lesson_score' => round($lessonScore, 2),
            'lesson_contribution' => round($lessonContribution, 2),
            'activity_score' => round($activityResult['average_score'], 2),
            'activity_contribution' => round($activityContribution, 2),
            'activity_types' => $activityResult['type_scores'],
            'activities' => $activityResult['activity_grades'],
            'completion_status' => $this->getModuleCompletionStatus($activityResult['activity_grades'], $moduleCompletion),
            'is_completed' => $moduleCompletion !== null,
            'completed_at' => $moduleCompletion?->completed_at,
        ];
    }

    /**
     * Calculate scores for activities of a specific type
     */
    private function calculateActivityTypeScore(int $userId, Collection $activities, int $moduleId): array
    {
        $activityGrades = [];
        $totalScore = 0;
        $completedCount = 0;

        foreach ($activities as $activity) {
            $activityGrade = $this->calculateActivityGrade($userId, $activity, $moduleId);
            $activityGrades[] = $activityGrade;
            
            if ($activityGrade['is_completed']) {
                $totalScore += $activityGrade['percentage_score'];
                $completedCount++;
            }
        }

        $averageScore = $completedCount > 0 ? $totalScore / $completedCount : 0;

        return [
            'activities' => $activityGrades,
            'average_score' => round($averageScore, 2),
            'completed_count' => $completedCount,
            'total_count' => $activities->count(),
        ];
    }

    /**
     * Calculate grade for a specific activity
     */
    private function calculateActivityGrade(int $userId, Activity $activity, int $moduleId): array
    {
        // Get student activity record using student_id
        $student = \App\Models\User::find($userId)?->student;
        $studentActivity = null;
        if ($student) {
            $studentActivity = StudentActivity::where('student_id', $student->id)
                ->where('activity_id', $activity->id)
                ->where('module_id', $moduleId)
                ->first();
        }

        // Get quiz progress if it's a quiz
        $quizProgress = null;
        if ($activity->activityType->name === 'Quiz') {
            // Get the student record first to use the correct student_id
            $student = \App\Models\User::find($userId)?->student;
            if ($student) {
                $quizProgress = StudentQuizProgress::where('student_id', $student->id)
                    ->where('activity_id', $activity->id)
                    ->first();
            }
        }

        $score = 0;
        $maxScore = 100;
        $isCompleted = false;
        $submittedAt = null;
        $status = 'not_started';

        // For quiz activities, prioritize StudentQuizProgress as the authoritative source
        if ($quizProgress && $activity->activityType->name === 'Quiz') {
            // Use the percentage_score directly from StudentQuizProgress as it's already calculated correctly
            $percentageScore = $quizProgress->percentage_score ?? 0;
            $score = $quizProgress->score ?? 0;
            $maxScore = 100; // Use 100 as max since we have percentage_score
            $isCompleted = $quizProgress->is_completed;
            $submittedAt = $quizProgress->completed_at ?? $quizProgress->updated_at;
            $status = $quizProgress->is_completed ? 'completed' : 'in_progress';
        } elseif ($studentActivity) {
            $score = $studentActivity->score ?? 0;
            $maxScore = $studentActivity->max_score ?? 100;
            $isCompleted = in_array($studentActivity->status, ['completed', 'submitted', 'graded']);
            $submittedAt = $studentActivity->submitted_at ?? $studentActivity->completed_at;
            $status = $studentActivity->status;
        }

        // For quizzes, use the percentage_score directly if it was set above
        if (!isset($percentageScore)) {
            $percentageScore = $maxScore > 0 ? ($score / $maxScore) * 100 : 0;
        }

        return [
            'activity_id' => $activity->id,
            'activity_title' => $activity->title,
            'activity_type' => $activity->activityType->name,
            'score' => $score,
            'max_score' => $maxScore,
            'percentage_score' => round($percentageScore, 2),
            'letter_grade' => $this->getLetterGrade($percentageScore),
            'is_completed' => $isCompleted,
            'status' => $status,
            'submitted_at' => $submittedAt,
            'due_date' => $activity->due_date,
            'is_overdue' => $this->isOverdue($activity->due_date, $isCompleted),
        ];
    }

    /**
     * Calculate lesson score for a module (lessons are automatically 100% when completed)
     */
    private function calculateLessonScore(int $userId, Module $module): float
    {
        $lessons = $module->lessons;
        if ($lessons->isEmpty()) {
            return 100; // No lessons = 100% lesson score
        }

        $completedLessons = 0;
        foreach ($lessons as $lesson) {
            // Check if lesson is completed (you may need to adjust this based on your lesson completion tracking)
            // For now, assuming lessons are automatically completed
            $completedLessons++;
        }

        $lessonScore = ($completedLessons / $lessons->count()) * 100;
        return $lessonScore;
    }

    /**
     * Calculate activity score for a module
     */
    private function calculateModuleActivityScore(int $userId, Module $module): array
    {
        $activities = $module->activities;
        $activityGrades = [];
        $typeScores = [];
        $totalScore = 0;
        $completedCount = 0;

        if ($activities->isEmpty()) {
            return [
                'average_score' => 100, // No activities = 100% activity score
                'type_scores' => [],
                'activity_grades' => [],
            ];
        }

        // Group activities by type for reporting but calculate simple average for module score
        $activitiesByType = $activities->groupBy('activityType.name');
        
        foreach ($activitiesByType as $typeName => $typeActivities) {
            $typeScore = $this->calculateActivityTypeScore($userId, $typeActivities, $module->id);
            $typeWeight = $this->getActivityTypeWeight($typeName);
            
            $typeScores[] = [
                'type' => $typeName,
                'activities' => $typeScore['activities'],
                'type_score' => $typeScore['average_score'],
                'type_weight' => $typeWeight,
                'completed_count' => $typeScore['completed_count'],
                'total_count' => $typeScore['total_count'],
            ];

            $activityGrades = array_merge($activityGrades, $typeScore['activities']);
        }

        // Calculate simple average of all activities (completed and incomplete)
        // Incomplete activities count as 0% towards the average
        foreach ($activityGrades as $activity) {
            $totalScore += $activity['percentage_score']; // This will be 0 for incomplete activities
        }

        $totalActivityCount = count($activityGrades);
        $averageScore = $totalActivityCount > 0 ? $totalScore / $totalActivityCount : 0;

        return [
            'average_score' => $averageScore,
            'type_scores' => $typeScores,
            'activity_grades' => $activityGrades,
        ];
    }

    /**
     * Get activity type weight
     */
    private function getActivityTypeWeight(string $typeName): int
    {
        return self::DEFAULT_ACTIVITY_WEIGHTS[$typeName] ?? 0;
    }

    /**
     * Convert percentage score to letter grade
     */
    private function getLetterGrade(float $percentage): string
    {
        if ($percentage >= 97) return 'A+';
        if ($percentage >= 93) return 'A';
        if ($percentage >= 90) return 'A-';
        if ($percentage >= 87) return 'B+';
        if ($percentage >= 83) return 'B';
        if ($percentage >= 80) return 'B-';
        if ($percentage >= 77) return 'C+';
        if ($percentage >= 73) return 'C';
        if ($percentage >= 70) return 'C-';
        if ($percentage >= 67) return 'D+';
        if ($percentage >= 63) return 'D';
        if ($percentage >= 60) return 'D-';
        return 'F';
    }

    /**
     * Check if activity is overdue
     */
    private function isOverdue($dueDate, bool $isCompleted): bool
    {
        if (!$dueDate || $isCompleted) {
            return false;
        }
        
        return now()->isAfter($dueDate);
    }

    /**
     * Get module completion status
     */
    private function getModuleCompletionStatus(array $activities, $moduleCompletion = null): string
    {
        if ($moduleCompletion) {
            return 'completed';
        }

        if (empty($activities)) {
            return 'not_started';
        }

        $completedCount = collect($activities)->where('is_completed', true)->count();
        $totalCount = count($activities);

        if ($completedCount === 0) {
            return 'not_started';
        } elseif ($completedCount === $totalCount) {
            return 'ready_to_complete';
        } else {
            return 'in_progress';
        }
    }

    /**
     * Get course completion status
     */
    private function getCourseCompletionStatus(array $modules): string
    {
        if (empty($modules)) {
            return 'not_started';
        }

        $completedCount = collect($modules)->where('is_completed', true)->count();
        $totalCount = count($modules);

        if ($completedCount === 0) {
            return 'not_started';
        } elseif ($completedCount === $totalCount) {
            return 'completed';
        } else {
            return 'in_progress';
        }
    }

    /**
     * Calculate grades for all students in a course (for instructor view)
     */
    public function calculateCourseStudentGrades(int $courseId): array
    {
        $course = Course::with(['students.user', 'modules'])->findOrFail($courseId);
        $studentGrades = [];

        foreach ($course->students as $student) {
            $grades = $this->calculateStudentCourseGrades($student->user_id, $courseId);
            $studentGrades[] = [
                'student_id' => $student->id,
                'student_name' => $student->user->name,
                'student_email' => $student->user->email,
                'overall_grade' => $grades['overall_grade'],
                'overall_letter_grade' => $grades['overall_letter_grade'],
                'completion_status' => $grades['completion_status'],
                'module_count' => count($grades['modules']),
                'completed_modules' => collect($grades['modules'])->where('is_completed', true)->count(),
                'activity_summary' => $grades['activity_summary'],
            ];
        }

        // Sort by overall grade (descending)
        usort($studentGrades, function($a, $b) {
            return $b['overall_grade'] <=> $a['overall_grade'];
        });

        return [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'instructor_id' => $course->instructor_id,
            ],
            'students' => $studentGrades,
            'class_statistics' => $this->calculateClassStatistics($studentGrades),
            'generated_at' => now(),
        ];
    }

    /**
     * Calculate class statistics
     */
    private function calculateClassStatistics(array $studentGrades): array
    {
        if (empty($studentGrades)) {
            return [
                'total_students' => 0,
                'average_grade' => 0,
                'highest_grade' => 0,
                'lowest_grade' => 0,
                'passing_count' => 0,
                'failing_count' => 0,
                'completion_rate' => 0,
            ];
        }

        $grades = collect($studentGrades)->pluck('overall_grade');
        $passingGrades = $grades->filter(fn($grade) => $grade >= 60);
        $completedStudents = collect($studentGrades)->where('completion_status', 'completed');

        return [
            'total_students' => count($studentGrades),
            'average_grade' => round($grades->avg(), 2),
            'highest_grade' => $grades->max(),
            'lowest_grade' => $grades->min(),
            'passing_count' => $passingGrades->count(),
            'failing_count' => $grades->count() - $passingGrades->count(),
            'completion_rate' => round(($completedStudents->count() / count($studentGrades)) * 100, 2),
        ];
    }

    /**
     * Get cached grade data for performance
     */
    public function getCachedStudentGrades(int $studentId, int $courseId): array
    {
        // Find the student to get their user_id for activity queries
        $student = Student::findOrFail($studentId);
        $cacheKey = "student_grades_{$studentId}_{$courseId}";
        
        return Cache::remember($cacheKey, 300, function () use ($student, $courseId) {
            return $this->calculateStudentCourseGrades($student->user_id, $courseId);
        });
    }

    /**
     * Clear grade cache for a student
     */
    public function clearGradeCache(int $studentId, int $courseId = null): void
    {
        if ($courseId) {
            Cache::forget("student_grades_{$studentId}_{$courseId}");
        } else {
            // Clear all grades for this student
            $courses = Course::whereHas('students', function($query) use ($studentId) {
                $query->where('course_student.student_id', $studentId);
            })->pluck('id');
            
            foreach ($courses as $courseId) {
                Cache::forget("student_grades_{$studentId}_{$courseId}");
            }
        }
    }

    /**
     * Get student summary across all courses
     */
    public function getStudentSummary(int $studentId): array
    {
        $user = \App\Models\User::findOrFail($studentId);
        $enrollments = $user->courseEnrollments()->with('course')->get();

        $courseGrades = [];
        foreach ($enrollments as $enrollment) {
            $grades = $this->calculateStudentCourseGrades($studentId, $enrollment->course_id);
            $courseGrades[] = $grades;
        }

        if (empty($courseGrades)) {
            return [
                'student' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'student_id' => $user->id,
                ],
                'overall_gpa' => 0,
                'total_courses' => 0,
                'completed_courses' => 0,
                'in_progress_courses' => 0,
                'average_grade' => 0,
                'overall_letter_grade' => 'N/A',
                'courses' => [],
            ];
        }

        $totalGrades = collect($courseGrades)->pluck('overall_grade');
        $completedCourses = collect($courseGrades)->where('completion_status', 'completed');
        $inProgressCourses = collect($courseGrades)->where('completion_status', 'in_progress');

        $averageGrade = $totalGrades->count() > 0 ? round($totalGrades->avg(), 2) : 0;
        
        return [
            'student' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'student_id' => $user->id,
            ],
            'overall_gpa' => round($this->calculateGPA($totalGrades->toArray()), 2),
            'total_courses' => count($courseGrades),
            'completed_courses' => $completedCourses->count(),
            'in_progress_courses' => $inProgressCourses->count(),
            'average_grade' => $averageGrade,
            'overall_letter_grade' => $this->getLetterGrade($averageGrade),
            'courses' => $courseGrades,
        ];
    }

    /**
     * Convert percentage grades to GPA (4.0 scale)
     */
    private function calculateGPA(array $percentageGrades): float
    {
        if (empty($percentageGrades)) {
            return 0.0;
        }

        $gpaSum = 0;
        foreach ($percentageGrades as $grade) {
            $gpaSum += $this->percentageToGPA($grade);
        }

        return $gpaSum / count($percentageGrades);
    }

    /**
     * Convert percentage to GPA point
     */
    private function percentageToGPA(float $percentage): float
    {
        if ($percentage >= 97) return 4.0;  // A+
        if ($percentage >= 93) return 4.0;  // A
        if ($percentage >= 90) return 3.7;  // A-
        if ($percentage >= 87) return 3.3;  // B+
        if ($percentage >= 83) return 3.0;  // B
        if ($percentage >= 80) return 2.7;  // B-
        if ($percentage >= 77) return 2.3;  // C+
        if ($percentage >= 73) return 2.0;  // C
        if ($percentage >= 70) return 1.7;  // C-
        if ($percentage >= 67) return 1.3;  // D+
        if ($percentage >= 63) return 1.0;  // D
        if ($percentage >= 60) return 0.7;  // D-
        return 0.0; // F
    }
}