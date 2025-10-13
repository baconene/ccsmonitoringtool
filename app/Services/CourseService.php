<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseGradeLevel;
use App\Models\User;
use App\Models\Student;
use App\Models\Module;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CourseService
{
    /**
     * Add a new course
     *
     * @param array $courseData
     * @return Course
     * @throws Exception
     */
    public function addCourse(array $courseData): Course
    {
        try {
            DB::beginTransaction();

            // Create the course
            $course = Course::create([
                'title' => $courseData['title'],
                'name' => $courseData['name'] ?? $courseData['title'], // For backward compatibility
                'description' => $courseData['description'],
                'created_by' => $courseData['created_by'],
                'instructor_id' => $courseData['instructor_id'] ?? $courseData['created_by'],
                'course_code' => $courseData['course_code'] ?? $this->generateCourseCode($courseData['title']),
                'credits' => $courseData['credits'] ?? 3,
                'semester' => $courseData['semester'] ?? 'Fall',
                'academic_year' => $courseData['academic_year'] ?? date('Y'),
                'is_active' => $courseData['is_active'] ?? true,
                'enrollment_limit' => $courseData['enrollment_limit'] ?? null,
                'start_date' => $courseData['start_date'] ?? now(),
                'end_date' => $courseData['end_date'] ?? now()->addMonths(4),
            ]);

            // Create default modules if specified
            if (isset($courseData['default_modules']) && $courseData['default_modules']) {
                $this->createDefaultModules($course);
            }

            DB::commit();
            
            Log::info('Course created successfully', [
                'course_id' => $course->id,
                'title' => $course->title,
                'created_by' => $course->created_by
            ]);

            return $course->load(['creator', 'modules']);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create course', [
                'error' => $e->getMessage(),
                'course_data' => $courseData
            ]);
            throw $e;
        }
    }

    /**
     * NOTE: Student enrollment methods have been moved to StudentCourseEnrollmentService
     * This service now focuses only on Course CRUD operations
     */

    /**
     * Edit/Update a course
     *
     * @param int $courseId
     * @param array $updateData
     * @return Course
     * @throws Exception
     */
    public function editCourse(int $courseId, array $updateData): Course
    {
        try {
            DB::beginTransaction();

            $course = Course::findOrFail($courseId);

            // Prepare update data with only fillable fields
            $allowedFields = [
                'title',
                'description', 
                'course_code',
                'credits',
                'semester',
                'academic_year',
                'is_active',
                'enrollment_limit',
                'start_date',
                'end_date'
            ];

            $updateFields = array_intersect_key($updateData, array_flip($allowedFields));

            // Update the course
            $course->update($updateFields);

            // Handle module updates if specified
            if (isset($updateData['modules'])) {
                $this->updateCourseModules($course, $updateData['modules']);
            }

            DB::commit();

            Log::info('Course updated successfully', [
                'course_id' => $courseId,
                'updated_fields' => array_keys($updateFields),
                'title' => $course->title
            ]);

            return $course->load(['creator', 'modules']);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update course', [
                'error' => $e->getMessage(),
                'course_id' => $courseId,
                'update_data' => $updateData
            ]);
            throw $e;
        }
    }

    /**
     * Remove/Delete a course
     *
     * @param int $courseId
     * @param bool $forceDelete
     * @return bool
     * @throws Exception
     */
    public function removeCourse(int $courseId, bool $forceDelete = false): bool
    {
        try {
            DB::beginTransaction();

            $course = Course::findOrFail($courseId);

            // Check if course has enrolled students
            $enrolledStudentsCount = $course->students()->count();
            
            if ($enrolledStudentsCount > 0 && !$forceDelete) {
                throw new Exception("Cannot delete course with enrolled students. Use force delete if necessary.");
            }

            // Store course info for logging before deletion
            $courseInfo = [
                'id' => $course->id,
                'title' => $course->title,
                'enrolled_students' => $enrolledStudentsCount,
                'modules_count' => $course->modules()->count()
            ];

            // Always clean up modules and their dependencies to avoid foreign key constraint errors
            $this->cleanupCourseModules($course);
            
            if ($forceDelete) {
                // Remove all student enrollments (only when force deleting)
                $course->students()->detach();
            }

            // Delete the course
            $course->delete();

            DB::commit();

            Log::info('Course deleted successfully', [
                'course_info' => $courseInfo,
                'force_delete' => $forceDelete
            ]);

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete course', [
                'error' => $e->getMessage(),
                'course_id' => $courseId,
                'force_delete' => $forceDelete
            ]);
            throw $e;
        }
    }

    /**
     * Get course with complete details
     *
     * @param int $courseId
     * @return Course
     */
    public function getCourseDetails(int $courseId): Course
    {
        return Course::with([
            'creator',
            'modules.lessons',
            'modules.activities.activityType',
            'students.user'
        ])->findOrFail($courseId);
    }

    /**
     * Get all courses with pagination and filtering
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCourses(array $filters = [], int $perPage = 15)
    {
        $user = auth()->user();
        
        $query = Course::with(['creator', 'instructor.user', 'modules.activities.activityType', 'modules.lessons', 'gradeLevels'])
            ->withCount(['students']);
        
        // For instructors: show courses they created OR courses they're assigned to teach
        // For admins: show all courses
        if ($user && !$user->isAdmin()) {
            $instructorId = $user->instructor ? $user->instructor->id : null;
            
            $query->where(function($q) use ($user, $instructorId) {
                $q->where('created_by', $user->id); // Courses created by user
                
                if ($instructorId) {
                    $q->orWhere('instructor_id', $instructorId); // Courses assigned to instructor
                }
            });
        }
        // If admin, no filter - show all courses

        // Apply filters
        if (isset($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('course_code', 'like', '%' . $searchTerm . '%')
                  ->orWhere('instructor_id', 'like', '%' . $searchTerm . '%')
                  ->orWhere('created_by', 'like', '%' . $searchTerm . '%')
                  // Search by instructor's user details
                  ->orWhereHas('instructor.user', function($userQuery) use ($searchTerm) {
                      $userQuery->where('name', 'like', '%' . $searchTerm . '%')
                                ->orWhere('email', 'like', '%' . $searchTerm . '%');
                  })
                  // Search by creator's details
                  ->orWhereHas('creator', function($creatorQuery) use ($searchTerm) {
                      $creatorQuery->where('name', 'like', '%' . $searchTerm . '%')
                                   ->orWhere('email', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        if (isset($filters['academic_year'])) {
            $query->where('academic_year', $filters['academic_year']);
        }

        if (isset($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Generate a unique course code
     *
     * @param string $title
     * @return string
     */
    private function generateCourseCode(string $title): string
    {
        // Extract initials from title
        $words = explode(' ', $title);
        $code = '';
        
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $code .= strtoupper($word[0]);
            }
        }

        // Add year and sequence number
        $year = date('y');
        $sequence = Course::whereYear('created_at', date('Y'))->count() + 1;
        
        return $code . $year . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Create default modules for a new course
     *
     * @param Course $course
     * @return void
     */
    private function createDefaultModules(Course $course): void
    {
        $defaultModules = [
            [
                'title' => 'Introduction',
                'description' => 'Course introduction and overview',
                'sequence' => 1,
                'module_type' => 'Lessons'
            ],
            [
                'title' => 'Core Concepts',
                'description' => 'Fundamental concepts and principles',
                'sequence' => 2,
                'module_type' => 'Mixed'
            ],
            [
                'title' => 'Assessment',
                'description' => 'Course assessment and evaluation',
                'sequence' => 3,
                'module_type' => 'Assessment'
            ]
        ];

        foreach ($defaultModules as $moduleData) {
            Module::create([
                'course_id' => $course->id,
                'title' => $moduleData['title'],
                'description' => $moduleData['description'],
                'sequence' => $moduleData['sequence'],
                'module_type' => $moduleData['module_type'],
                'created_by' => $course->created_by,
            ]);
        }
    }



    /**
     * Update course modules
     *
     * @param Course $course
     * @param array $modulesData
     * @return void
     */
    private function updateCourseModules(Course $course, array $modulesData): void
    {
        // This is a placeholder for module update logic
        // Implementation would depend on the specific requirements
        // for how modules should be updated
    }

    /**
     * Cleanup course modules and their dependencies
     *
     * @param Course $course
     * @return void
     */
    private function cleanupCourseModules(Course $course): void
    {
        // Delete related data in proper order to avoid foreign key constraints
        foreach ($course->modules as $module) {
            // Delete activities and their dependencies first
            foreach ($module->activities as $activity) {
                // Delete student activity records
                \App\Models\StudentActivity::where('activity_id', $activity->id)->delete();
                
                // Delete quiz-related data
                if ($activity->quiz) {
                    // Delete student quiz progress and answers
                    $quizProgressIds = \App\Models\StudentQuizProgress::where('quiz_id', $activity->quiz->id)
                        ->pluck('id');
                    \App\Models\StudentQuizAnswer::whereIn('quiz_progress_id', $quizProgressIds)->delete();
                    \App\Models\StudentQuizProgress::where('quiz_id', $activity->quiz->id)->delete();
                    
                    // Delete questions and their options
                    foreach ($activity->quiz->questions as $question) {
                        $question->options()->delete();
                    }
                    $activity->quiz->questions()->delete();
                    $activity->quiz->delete();
                }
                
                // Delete assignment-related data
                if ($activity->assignment) {
                    $activity->assignment->delete();
                }
            }
            
            // Delete module activities
            $module->activities()->delete();
            
            // Delete lessons
            $module->lessons()->delete();
            
            // Delete module completions
            \App\Models\ModuleCompletion::where('module_id', $module->id)->delete();
        }
        
        // Delete modules
        $course->modules()->delete();
        
        // Delete course enrollments
        \App\Models\CourseEnrollment::where('course_id', $course->id)->delete();
    }

    /**
     * Assign grade levels to a course
     *
     * @param int $courseId
     * @param array $gradeLevelIds
     * @return Course
     * @throws Exception
     */
    public function assignGradeLevels(int $courseId, array $gradeLevelIds): Course
    {
        try {
            DB::beginTransaction();

            $course = Course::findOrFail($courseId);
            
            // Sync grade levels (removes old ones and adds new ones)
            $course->gradeLevels()->sync($gradeLevelIds);

            DB::commit();

            Log::info('Grade levels assigned to course', [
                'course_id' => $courseId,
                'grade_level_ids' => $gradeLevelIds
            ]);

            return $course->load(['gradeLevels']);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to assign grade levels to course', [
                'course_id' => $courseId,
                'grade_level_ids' => $gradeLevelIds,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get courses available for a specific grade level
     *
     * @param int $gradeLevelId
     * @param array $filters
     * @param int $perPage
     * @return mixed
     */
    public function getCoursesForGradeLevel(int $gradeLevelId, array $filters = [], int $perPage = 15)
    {
        $query = Course::with(['creator', 'modules', 'gradeLevels'])
            ->whereHas('gradeLevels', function ($q) use ($gradeLevelId) {
                $q->where('grade_level_id', $gradeLevelId);
            })
            ->withCount(['students']);

        // Apply filters
        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('course_code', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        if (isset($filters['academic_year'])) {
            $query->where('academic_year', $filters['academic_year']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Check if a student can enroll in a course based on grade level
     *
     * @param int $courseId
     * @param int $studentId
     * @return bool
     */
    public function canStudentEnrollInCourse(int $courseId, int $studentId): bool
    {
        try {
            $student = Student::findOrFail($studentId);
            
            if (!$student->grade_level_id) {
                // Check if course has any grade level restrictions
                $course = Course::findOrFail($courseId);
                return !$course->gradeLevels()->exists();
            }

            return CourseGradeLevel::canStudentAccessCourse($courseId, $student->grade_level_id);
        } catch (Exception $e) {
            Log::error('Error checking course enrollment eligibility', [
                'course_id' => $courseId,
                'student_id' => $studentId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}