<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use App\Services\CourseService;
use App\Services\StudentCourseEnrollmentService;
use App\Traits\DynamicMessageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CourseController extends Controller
{
    use DynamicMessageTrait;

    protected CourseService $courseService;
    protected StudentCourseEnrollmentService $enrollmentService;

    public function __construct(
        CourseService $courseService,
        StudentCourseEnrollmentService $enrollmentService
    ) {
        $this->courseService = $courseService;
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * Display a listing of courses for the course management page.
     */
    public function index(Request $request)
    {
        try {
            // Get filter parameters from request
            $filters = $request->only(['search', 'is_active', 'semester', 'academic_year', 'created_by']);
            $perPage = $request->input('per_page', 15);

            // Get courses using the service
            $courses = $this->courseService->getCourses($filters, $perPage);

            // Fetch all available activities for the instructor/admin
            $availableActivities = \App\Models\Activity::with(['activityType', 'creator', 'quiz.questions'])
                ->where('created_by', auth()->id())
                ->get()
                ->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'title' => $activity->title,
                        'description' => $activity->description,
                        'activity_type_id' => $activity->activity_type_id,
                        'created_by' => $activity->created_by,
                        'created_at' => $activity->created_at,
                        'updated_at' => $activity->updated_at,
                        'passing_percentage' => $activity->passing_percentage,
                        'due_date' => $activity->due_date,
                        'activityType' => $activity->activityType,
                        'creator' => $activity->creator,
                        'question_count' => $activity->quiz ? $activity->quiz->questions->count() : 0,
                        'total_points' => $activity->quiz ? $activity->quiz->questions->sum('points') : 0,
                    ];
                });

            return Inertia::render('CourseManagement', [
                'courses' => $courses->items(),
                'coursesData' => [
                    'current_page' => $courses->currentPage(),
                    'last_page' => $courses->lastPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                    'from' => $courses->firstItem(),
                    'to' => $courses->lastItem(),
                ],
                'availableActivities' => $availableActivities
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching courses', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to load courses. Please try again.']);
        }
    }

    /**
     * Get courses for API (dashboard use).
     */
    public function getCourses()
    {
        $user = Auth::user();
       //update later $instructorId = $user->instructor->id;
        // Get courses for the authenticated instructor with students
        $courses = Course::with(['students.user', 'instructor'])
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title ?: $course->name,
                    'name' => $course->name, // For backward compatibility
                    'description' => $course->description,
                    'instructor_id' => $course->instructor_id,
                    'instructor_name' => $course->instructor->name ?? '',
                    'created_at' => $course->created_at->toISOString(),
                    'updated_at' => $course->updated_at->toISOString(),
                    'students' => $course->students->map(function ($student) use ($course) {
                        return [
                            'id' => $student->id,
                            'student_id' => $student->student_id,
                            'name' => $student->name,
                            'email' => $student->email,
                            'enrollment_number' => $student->enrollment_number,
                            'program' => $student->program,
                            'department' => $student->department,
                            'status' => $student->pivot->status ?? 'enrolled',
                            'enrolled_at' => $student->pivot->enrolled_at ?? null,
                            'grade' => $student->pivot->grade ?? null,
                            'courseId' => $course->id,
                            'courseTitle' => $course->title ?: $course->name,
                        ];
                    })
                ];
            });

        return response()->json($courses);
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'instructor_id' => 'nullable|exists:instructors,id',
            'grade_level' => 'nullable|string|max:50',
            'course_code' => 'nullable|string|max:20|unique:courses',
            'credits' => 'nullable|integer|min:1|max:10',
            'semester' => 'nullable|string|in:Fall,Spring,Summer',
            'academic_year' => 'nullable|string|size:4',
            'is_active' => 'nullable|boolean',
            'enrollment_limit' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'default_modules' => 'nullable|boolean',
            'grade_level_ids' => 'nullable|array',
            'grade_level_ids.*' => 'exists:grade_levels,id',
        ]);

        try {
            $user = auth()->user();
            
            // Set created_by to the authenticated user's ID
            $validated['created_by'] = $user->id;
            
            // Handle instructor_id based on user role
            if ($user->isInstructor()) {
                // For instructors: Get or create instructor record and use instructor model ID
                $instructor = $user->getOrCreateInstructorRecord();
                $validated['instructor_id'] = $instructor->id;
            } elseif ($user->isAdmin()) {
                // For admins: Only set created_by, don't set instructor_id
                // Admin can optionally assign an instructor via the form
                if (!isset($validated['instructor_id'])) {
                    unset($validated['instructor_id']);
                }
            } else {
                // Fallback: use instructor_id from request or leave it null
                if (!isset($validated['instructor_id'])) {
                    unset($validated['instructor_id']);
                }
            }
            
            // Use CourseService for main course creation
            $course = $this->courseService->addCourse($validated);

            // Handle grade level associations if provided (legacy support)
            if (!empty($validated['grade_level_ids'])) {
                $course->gradeLevels()->attach($validated['grade_level_ids']);
            }

            // Load relationships for response
            $course->load('gradeLevels', 'creator', 'modules');

            return response()->json([
                'success' => true,
                'course' => $course,
                'message' => $this->getModelSuccessMessage('created', $course)
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create course', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create course: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $course->load('modules.lessons.documents');
        return response()->json($course);
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'grade_level' => 'nullable|string|max:50',
            'course_code' => 'sometimes|string|max:20|unique:courses,course_code,' . $course->id,
            'credits' => 'nullable|integer|min:1|max:10',
            'semester' => 'nullable|string|in:Fall,Spring,Summer',
            'academic_year' => 'nullable|string|size:4',
            'is_active' => 'nullable|boolean',
            'enrollment_limit' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'grade_level_ids' => 'nullable|array',
            'grade_level_ids.*' => 'exists:grade_levels,id',
        ]);

        try {
            // Use CourseService for main course update
            $course = $this->courseService->editCourse($course->id, $validated);

            // Handle grade level associations if provided (legacy support)
            if (isset($validated['grade_level_ids'])) {
                $course->gradeLevels()->sync($validated['grade_level_ids']);
            }

            // Load relationships for response
            $course->load('gradeLevels', 'creator', 'modules');

            return response()->json([
                'success' => true,
                'message' => $this->getModelSuccessMessage('updated', $course),
                'course' => $course
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update course', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update course: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Enroll student to course using StudentCourseEnrollmentService
     */
    public function enrollStudent(Request $request, $courseId)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'enrolled_at' => 'nullable|date',
            'status' => 'nullable|string|in:enrolled,completed,dropped,withdrawn',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            $result = $this->enrollmentService->enrollStudentToACourse(
                $courseId, 
                $validated['student_id'], 
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'enrollment_type' => $result['enrollment_type'],
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to enroll student', [
                'course_id' => $courseId,
                'student_id' => $validated['student_id'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to enroll student: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get course enrollments using StudentCourseEnrollmentService
     */
    public function getCourseEnrollments(Request $request, $courseId)
    {
        $validated = $request->validate([
            'status' => 'nullable|string|in:enrolled,completed,dropped,withdrawn',
            'enrolled_after' => 'nullable|date',
            'enrolled_before' => 'nullable|date'
        ]);

        try {
            $enrollments = $this->enrollmentService->getCourseEnrollments($courseId, $validated);
            $statistics = $this->enrollmentService->getEnrollmentStatistics($courseId);

            return response()->json([
                'success' => true,
                'enrollments' => $enrollments,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get course enrollments', [
                'course_id' => $courseId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get course enrollments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Request $request, Course $course)
    {
        $validated = $request->validate([
            'force_delete' => 'nullable|boolean'
        ]);

        try {
            $forceDelete = $validated['force_delete'] ?? false;
            
            // Check if course has enrolled students first
            $enrolledStudentsCount = $course->courseEnrollments()->count();
            
            if ($enrolledStudentsCount > 0 && !$forceDelete) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete course '{$course->title}' because it has {$enrolledStudentsCount} enrolled student(s). Please unenroll all students first or use force delete.",
                    'enrolled_students' => $enrolledStudentsCount,
                    'requires_force_delete' => true
                ], 422);
            }
            
            $successMessage = $this->getModelSuccessMessage('deleted', $course);
            
            // Use CourseService for deletion
            $this->courseService->removeCourse($course->id, $forceDelete);

            return response()->json([
                'success' => true,
                'message' => $successMessage
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete course', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete course: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign grade levels to a course.
     */
    public function assignGradeLevels(Request $request, Course $course)
    {
        $validated = $request->validate([
            'grade_level_ids' => 'required|array',
            'grade_level_ids.*' => 'required|integer|exists:grade_levels,id'
        ]);

        try {
            $updatedCourse = $this->courseService->assignGradeLevels($course->id, $validated['grade_level_ids']);

            return response()->json([
                'success' => true,
                'message' => 'Grade levels assigned successfully',
                'course' => $updatedCourse->only(['id', 'title', 'name']),
                'grade_levels' => $updatedCourse->gradeLevels->map(function ($gradeLevel) {
                    return [
                        'id' => $gradeLevel->id,
                        'name' => $gradeLevel->name,
                        'display_name' => $gradeLevel->display_name,
                        'level' => $gradeLevel->level
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to assign grade levels to course', [
                'course_id' => $course->id,
                'grade_level_ids' => $validated['grade_level_ids'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign grade levels: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available grade levels for course assignment.
     */
    public function getAvailableGradeLevels()
    {
        try {
            $gradeLevels = \App\Models\GradeLevel::active()->ordered()->get();
            
            return response()->json([
                'success' => true,
                'grade_levels' => $gradeLevels->map(function ($gradeLevel) {
                    return [
                        'id' => $gradeLevel->id,
                        'name' => $gradeLevel->name,
                        'display_name' => $gradeLevel->display_name,
                        'level' => $gradeLevel->level
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get available grade levels', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get grade levels: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get courses available for a specific grade level.
     */
    public function getCoursesForGradeLevel(Request $request)
    {
        $validated = $request->validate([
            'grade_level_id' => 'required|integer|exists:grade_levels,id',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        try {
            $courses = $this->courseService->getCoursesForGradeLevel(
                $validated['grade_level_id'],
                $request->only(['search', 'is_active', 'semester', 'academic_year']),
                $validated['per_page'] ?? 15
            );

            return response()->json([
                'success' => true,
                'courses' => $courses
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get courses for grade level', [
                'grade_level_id' => $validated['grade_level_id'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get courses: ' . $e->getMessage()
            ], 500);
        }
    }
}
