<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseGradeLevel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

class StudentCourseEnrollmentService
{
    /**
     * Enroll a student to a course
     *
     * @param int $courseId
     * @param int $studentId
     * @param array $enrollmentData
     * @return array
     * @throws Exception
     */
    public function enrollStudentToACourse(int $courseId, int $studentId, array $enrollmentData = []): array
    {
        try {
            DB::beginTransaction();

            // Validate course exists and is active (if is_active field exists)
            $course = Course::findOrFail($courseId);
            if (isset($course->is_active) && !$course->is_active) {
                throw new Exception("Cannot enroll in inactive course: {$course->title}");
            }

            // Validate student exists
            $student = Student::findOrFail($studentId);

            // Check if student is already enrolled using courseEnrollments relationship
            $existingEnrollment = $student->courseEnrollments()
                ->where('course_id', $courseId)
                ->first();

            if ($existingEnrollment) {
                // Student is already enrolled - don't allow duplicate enrollment
                throw new Exception("Student is already enrolled in this course");
            }

            // Check enrollment limit if set using Eloquent relationship
            if (isset($course->enrollment_limit)) {
                $currentEnrollments = $course->students()->count();
                    
                if ($currentEnrollments >= $course->enrollment_limit) {
                    throw new Exception("Course enrollment limit of {$course->enrollment_limit} has been reached");
                }
            }

            // Check prerequisites if any (this would be expanded based on your requirements)
            $this->checkEnrollmentPrerequisites($course, $student);

            // Create enrollment record using CourseEnrollment model
            $enrollmentRecord = $student->courseEnrollments()->create([
                'course_id' => $courseId,
                'enrolled_at' => $enrollmentData['enrolled_at'] ?? now(),
                'instructor_id' => Auth::id(), // Track who enrolled the student
                'user_id' => $student->user_id, // Keep for backward compatibility
                'progress' => 0,
                'is_completed' => false,
            ]);

            // Initialize student progress for existing course modules
            $this->initializeStudentProgress($course, $student);

            DB::commit();

            Log::info('Student enrolled successfully', [
                'course_id' => $courseId,
                'student_id' => $studentId,
                'course_title' => $course->title,
                'student_name' => $student->user->name ?? 'Unknown',
                'enrollment_data' => $enrollmentRecord
            ]);

            return [
                'success' => true,
                'message' => 'Student enrolled successfully',
                'enrollment_type' => 'new',
                'enrollment_data' => $enrollmentRecord
            ];

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to enroll student', [
                'error' => $e->getMessage(),
                'course_id' => $courseId,
                'student_id' => $studentId,
                'enrollment_data' => $enrollmentData
            ]);
            
            throw $e;
        }
    }

    /**
     * Update enrollment status for a student
     *
     * @param int $courseId
     * @param int $studentId
     * @param string $status
     * @param array $additionalData
     * @return bool
     * @throws Exception
     */
    public function updateEnrollmentStatus(int $courseId, int $studentId, string $status, array $additionalData = []): bool
    {
        try {
            $validStatuses = ['enrolled', 'completed', 'dropped', 'withdrawn'];
            
            if (!in_array($status, $validStatuses)) {
                throw new Exception("Invalid enrollment status: {$status}. Valid statuses are: " . implode(', ', $validStatuses));
            }

            $student = Student::findOrFail($studentId);

            $updateData = [
                'status' => $status,
                'updated_at' => now()
            ];

            // Add additional data if provided
            if (isset($additionalData['grade'])) {
                $updateData['grade'] = $additionalData['grade'];
            }

            if (isset($additionalData['notes'])) {
                $updateData['notes'] = $additionalData['notes'];
            }

            // Check if enrollment exists
            $enrollment = $student->courseEnrollments()->where('course_id', $courseId)->first();
            if (!$enrollment) {
                throw new Exception("Enrollment record not found for student ID {$studentId} in course ID {$courseId}");
            }

            // Update using CourseEnrollment model
            $enrollment->update($updateData);

            Log::info('Enrollment status updated', [
                'course_id' => $courseId,
                'student_id' => $studentId,
                'new_status' => $status,
                'additional_data' => $additionalData
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Failed to update enrollment status', [
                'error' => $e->getMessage(),
                'course_id' => $courseId,
                'student_id' => $studentId,
                'status' => $status
            ]);
            
            throw $e;
        }
    }

    /**
     * Remove student enrollment from a course
     *
     * @param int $courseId
     * @param int $studentId
     * @param string $reason
     * @return bool
     * @throws Exception
     */
    public function removeStudentEnrollment(int $courseId, int $studentId, string $reason = 'withdrawn'): bool
    {
        try {
            DB::beginTransaction();

            $student = Student::findOrFail($studentId);

            // Check if enrollment exists using courseEnrollments relationship
            $enrollment = $student->courseEnrollments()->where('course_id', $courseId)->first();

            if (!$enrollment) {
                throw new Exception("Student is not enrolled in this course");
            }

            // Delete the enrollment
            $enrollment->delete();

            // Optionally clean up student progress data
            $this->cleanupStudentProgress($courseId, $studentId);

            DB::commit();

            Log::info('Student enrollment removed', [
                'course_id' => $courseId,
                'student_id' => $studentId,
                'reason' => $reason
            ]);

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to remove student enrollment', [
                'error' => $e->getMessage(),
                'course_id' => $courseId,
                'student_id' => $studentId,
                'reason' => $reason
            ]);
            
            throw $e;
        }
    }

    /**
     * Get all enrollments for a course
     *
     * @param int $courseId
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function getCourseEnrollments(int $courseId, array $filters = [])
    {
        $course = Course::findOrFail($courseId);
        
        $query = $course->students()->with('user');

        // Apply filters
        if (isset($filters['status'])) {
            $query->wherePivot('status', $filters['status']);
        }

        if (isset($filters['enrolled_after'])) {
            $query->wherePivot('enrolled_at', '>=', $filters['enrolled_after']);
        }

        if (isset($filters['enrolled_before'])) {
            $query->wherePivot('enrolled_at', '<=', $filters['enrolled_before']);
        }

        $students = $query->orderByPivot('enrolled_at', 'desc')->get();

        // Transform the data to match the expected format
        return $students->map(function ($student) {
            return (object) [
                'id' => $student->pivot->id ?? null,
                'course_id' => $student->pivot->course_id,
                'student_id' => $student->id,
                'enrolled_at' => $student->pivot->enrolled_at,
                'status' => $student->pivot->status,
                'grade' => $student->pivot->grade,
                'notes' => $student->pivot->notes,
                'created_at' => $student->pivot->created_at,
                'updated_at' => $student->pivot->updated_at,
                'student_number' => $student->student_number,
                'enrollment_number' => $student->enrollment_number,
                'student_name' => $student->user->name ?? null,
                'student_email' => $student->user->email ?? null,
            ];
        });
    }

    /**
     * Get all enrollments for a student
     *
     * @param int $studentId
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function getStudentEnrollments(int $studentId, array $filters = [])
    {
        $student = Student::findOrFail($studentId);
        
        $query = $student->courseEnrollments()->with('course');

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['active_only']) && $filters['active_only']) {
            $query->where('status', 'enrolled');
        }

        $enrollments = $query->orderBy('enrolled_at', 'desc')->get();

        // Transform the data to match the expected format
        return $enrollments->map(function ($enrollment) {
            return (object) [
                'id' => $enrollment->id,
                'course_id' => $enrollment->course_id,
                'student_id' => $enrollment->student_id,
                'enrolled_at' => $enrollment->enrolled_at,
                'status' => $enrollment->status,
                'grade' => $enrollment->grade,
                'notes' => $enrollment->notes,
                'created_at' => $enrollment->created_at,
                'updated_at' => $enrollment->updated_at,
                'course_title' => $enrollment->course->title ?? null,
                'course_name' => $enrollment->course->name ?? null,
                'course_description' => $enrollment->course->description ?? null,
            ];
        });
    }

    /**
     * Check enrollment prerequisites
     *
     * @param Course $course
     * @param Student $student
     * @return void
     * @throws Exception
     */
    private function checkEnrollmentPrerequisites(Course $course, Student $student): void
    {
        // Check grade level eligibility - student must be in an allowed grade level for the course
        if ($student->grade_level_id) {
            $isEligible = CourseGradeLevel::canStudentAccessCourse(
                $course->id, 
                $student->grade_level_id
            );
            
            if (!$isEligible) {
                $studentGradeLevel = $student->gradeLevel->display_name ?? 'Unknown';
                throw new Exception("Student's grade level ({$studentGradeLevel}) is not allowed for this course");
            }
        } else {
            // If student has no grade level assigned, check if course has any grade level restrictions
            $courseHasGradeLevels = $course->gradeLevels()->exists();
            if ($courseHasGradeLevels) {
                throw new Exception("Student must have a grade level assigned to enroll in this course");
            }
        }

        // Check if student has too many active enrollments
        $activeEnrollments = $student->courseEnrollments()
            ->where('status', 'enrolled')
            ->count();

        $maxEnrollments = config('app.max_student_enrollments', 10); // Default to 10
        if ($activeEnrollments >= $maxEnrollments) {
            throw new Exception("Student has reached maximum enrollment limit of {$maxEnrollments} courses");
        }
    }

    /**
     * Initialize student progress for course modules
     *
     * @param Course $course
     * @param Student $student
     * @return void
     */
    private function initializeStudentProgress(Course $course, Student $student): void
    {
        // Initialize progress records for modules, activities, etc.
        // This follows the logic from your seeder pattern
        
        $modules = $course->modules()->get();
        
        foreach ($modules as $module) {
            // Check if module completion record already exists
            $existingCompletion = $student->moduleCompletions()
                ->where('module_id', $module->id)
                ->first();

            if (!$existingCompletion) {
                // Create initial module completion record using student relationship
                try {
                    $student->moduleCompletions()->create([
                        'module_id' => $module->id,
                        'course_id' => $course->id,
                        'completed_at' => now(),
                        'completion_data' => [
                            'status' => 'not_started',
                            'progress' => 0
                        ],
                        'user_id' => $student->user_id, // Keep for backward compatibility
                    ]);
                } catch (\Exception $e) {
                    // If there are constraint issues, log and continue
                    Log::warning('Failed to create module completion record', [
                        'student_id' => $student->id,
                        'module_id' => $module->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Initialize activity progress for activities in this module
            $activities = $module->activities()->get();
            
            foreach ($activities as $activity) {
                $existingActivity = $student->studentActivities()
                    ->where('activity_id', $activity->id)
                    ->first();

                if (!$existingActivity) {
                    try {
                        $student->studentActivities()->create([
                            'activity_id' => $activity->id,
                            'module_id' => $module->id, // Required field
                            'score' => null,
                            'status' => 'not_started',
                            'started_at' => null,
                            'completed_at' => null,
                            'attempt_count' => 0,
                        ]);
                    } catch (\Exception $e) {
                        // Log and continue if there are constraint issues
                        Log::warning('Failed to create student activity record', [
                            'student_id' => $student->id,
                            'activity_id' => $activity->id,
                            'module_id' => $module->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Cleanup student progress data when removing enrollment
     *
     * @param int $courseId
     * @param int $studentId
     * @return void
     */
    private function cleanupStudentProgress(int $courseId, int $studentId): void
    {
        // This is optional - you might want to keep historical data
        // For audit purposes, consider soft deleting or archiving instead
        
        // Remove module completion records using student relationship
        $student = Student::findOrFail($studentId);
        $student->moduleCompletions()
            ->where('course_id', $courseId)
            ->delete();

        // Remove activity progress records for activities in this course
        $course = Course::findOrFail($courseId);
        $modules = $course->modules;

        foreach ($modules as $module) {
            $activities = $module->activities;
            $activityIds = $activities->pluck('id')->toArray();

            if (!empty($activityIds)) {
                $student->studentActivities()
                    ->whereIn('activity_id', $activityIds)
                    ->delete();

                // Clean up quiz progress if it exists using student relationship
                $student->quizProgress()
                    ->whereIn('activity_id', $activityIds)
                    ->delete();
            }
        }
    }

    /**
     * Get enrollment statistics for a course
     *
     * @param int $courseId
     * @return array
     */
    public function getEnrollmentStatistics(int $courseId): array
    {
        $course = Course::findOrFail($courseId);
        
        // Get all enrollments for the course using the courseEnrollments relationship  
        $enrollments = $course->courseEnrollments;
        
        $totalEnrollments = $enrollments->count();
        $activeEnrollments = $enrollments->where('status', 'enrolled')->count();
        $completedEnrollments = $enrollments->where('status', 'completed')->count();
        $droppedEnrollments = $enrollments->where('status', 'dropped')->count();
        $withdrawnEnrollments = $enrollments->where('status', 'withdrawn')->count();
        
        // Calculate average grade for enrollments that have grades
        $gradesCollection = $enrollments->whereNotNull('grade')->pluck('grade');
        $averageGrade = $gradesCollection->isNotEmpty() ? round($gradesCollection->avg(), 2) : null;

        return [
            'total_enrollments' => $totalEnrollments,
            'active_enrollments' => $activeEnrollments,
            'completed_enrollments' => $completedEnrollments,
            'dropped_enrollments' => $droppedEnrollments,
            'withdrawn_enrollments' => $withdrawnEnrollments,
            'average_grade' => $averageGrade,
            'completion_rate' => $totalEnrollments > 0 
                ? round(($completedEnrollments / $totalEnrollments) * 100, 2) 
                : 0
        ];
    }
}