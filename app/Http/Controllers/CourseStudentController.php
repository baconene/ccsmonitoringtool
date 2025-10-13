<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Student;
use App\Models\CourseEnrollment;
use App\Services\StudentCourseEnrollmentService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseStudentController extends Controller
{
    protected StudentCourseEnrollmentService $enrollmentService;

    public function __construct(StudentCourseEnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * Show the course student management page.
     */
    public function index(Course $course)
    {
        // Load course with current enrollments and grade levels
        $course->load([
            'students' => function ($query) {
                $query->with(['user.role', 'gradeLevel']);
            },
            'gradeLevels'
        ]);

        // Get eligible grade level IDs for this course
        $eligibleGradeLevelIds = $course->gradeLevels->pluck('id')->toArray();

        // Get all students with matching grade level (if course has grade levels specified)
        $availableStudentsQuery = Student::with(['user.role', 'gradeLevel'])
            ->whereHas('user.role', function ($query) {
                $query->where('name', 'student');
            });

        // Filter by grade levels from course_grade_level pivot table
        if (!empty($eligibleGradeLevelIds)) {
            $availableStudentsQuery->whereIn('grade_level_id', $eligibleGradeLevelIds);
        }

        // Exclude already enrolled students
        $enrolledStudentIds = $course->students->pluck('id')->toArray();
        $availableStudents = $availableStudentsQuery
            ->whereNotIn('id', $enrolledStudentIds)
            ->get();

        return Inertia::render('Course/ManageStudents', [
            'course' => [
                'id' => $course->id,
                'name' => $course->name,
                'title' => $course->title,
                'description' => $course->description,
                'grade_levels' => $course->gradeLevels->map(function ($gl) {
                    return [
                        'id' => $gl->id,
                        'name' => $gl->name,
                        'display_name' => $gl->display_name,
                    ];
                }),
            ],
            'enrolledStudents' => $course->courseEnrollments()->with(['student.user.role', 'student.gradeLevel'])->get()->map(function ($enrollment) {
                return [
                    'id' => $enrollment->student->id,
                    'student_id_text' => $enrollment->student->student_id_text,
                    'name' => $enrollment->student->user->name ?? 'N/A',
                    'email' => $enrollment->student->user->email ?? 'N/A',
                    'grade_level' => $enrollment->student->gradeLevel->display_name ?? 'Not Set',
                    'progress' => $enrollment->progress ?? 0,
                    'enrolled_at' => $enrollment->enrolled_at,
                    'is_completed' => $enrollment->is_completed ?? false,
                ];
            })->toArray(),
            'availableStudents' => $availableStudents->map(function ($student) {
                return [
                    'id' => $student->id,
                    'student_id_text' => $student->student_id_text,
                    'name' => $student->user->name ?? 'N/A',
                    'email' => $student->user->email ?? 'N/A',
                    'grade_level' => $student->gradeLevel->display_name ?? 'Not Set',
                ];
            })->toArray(),
        ]);
    }

    /**
     * Enroll students to a course using StudentCourseEnrollmentService.
     */
    public function enrollStudents(Request $request, Course $course)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $studentIds = $request->student_ids;
        $successCount = 0;
        $errors = [];

        try {
            // Load course grade levels for validation
            $course->load('gradeLevels');
            $eligibleGradeLevelIds = $course->gradeLevels->pluck('id')->toArray();

            // Verify students match grade level requirement
            if (!empty($eligibleGradeLevelIds)) {
                $validStudents = Student::whereIn('id', $studentIds)
                    ->whereIn('grade_level_id', $eligibleGradeLevelIds)
                    ->where('status', 'active')
                    ->pluck('id')
                    ->toArray();

                if (count($validStudents) !== count($studentIds)) {
                    $eligibleGradeNames = $course->gradeLevels->pluck('display_name')->join(', ');
                    return redirect()->back()->with('error', "Some students do not match the course grade level requirements ({$eligibleGradeNames}).");
                }
            }

            // Process each student enrollment using the service
            foreach ($studentIds as $studentId) {
                try {
                    $student = Student::with('user')->findOrFail($studentId);
                    
                    // Use the enrollment service
                    $result = $this->enrollmentService->enrollStudentToACourse(
                        $course->id,
                        $student->id,
                        [
                            'enrolled_at' => now(),
                        ]
                    );

                    if ($result['success']) {
                        $successCount++;
                    }

                } catch (\Exception $e) {
                    $studentName = $student->user->name ?? "Student #{$studentId}";
                    $errors[] = "Failed to enroll {$studentName}: " . $e->getMessage();
                    Log::warning('Student enrollment failed', [
                        'course_id' => $course->id,
                        'student_id' => $studentId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Prepare response message
            if ($successCount > 0 && empty($errors)) {
                return redirect()->route('courses.manage-students', $course)
                    ->with('success', "{$successCount} student(s) enrolled successfully.");
            } elseif ($successCount > 0 && !empty($errors)) {
                $errorMessage = implode('; ', $errors);
                return redirect()->route('courses.manage-students', $course)
                    ->with('warning', "{$successCount} student(s) enrolled successfully. Some errors occurred: {$errorMessage}");
            } else {
                $errorMessage = implode('; ', $errors);
                return redirect()->route('courses.manage-students', $course)
                    ->with('error', "Failed to enroll students: {$errorMessage}");
            }

        } catch (\Exception $e) {
            Log::error('Bulk student enrollment failed', [
                'course_id' => $course->id,
                'student_ids' => $studentIds,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('courses.manage-students', $course)
                ->with('error', 'Failed to enroll students: ' . $e->getMessage());
        }
    }

    /**
     * Remove students from a course using StudentCourseEnrollmentService.
     */
    public function removeStudents(Request $request, Course $course)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $studentIds = $request->student_ids;
        $successCount = 0;
        $errors = [];

        try {
            // Process each student removal using the service
            foreach ($studentIds as $studentId) {
                try {
                    $student = Student::with('user')->findOrFail($studentId);
                    
                    // Use the enrollment service to remove enrollment
                    $result = $this->enrollmentService->removeStudentEnrollment(
                        $course->id,
                        $student->id,
                        'withdrawn'
                    );

                    if ($result) {
                        $successCount++;
                    }

                } catch (\Exception $e) {
                    $studentName = $student->user->name ?? "Student #{$studentId}";
                    $errors[] = "Failed to remove {$studentName}: " . $e->getMessage();
                    Log::warning('Student removal failed', [
                        'course_id' => $course->id,
                        'student_id' => $studentId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Prepare response message
            if ($successCount > 0 && empty($errors)) {
                return redirect()->route('courses.manage-students', $course)
                    ->with('success', "{$successCount} student(s) removed successfully.");
            } elseif ($successCount > 0 && !empty($errors)) {
                $errorMessage = implode('; ', $errors);
                return redirect()->route('courses.manage-students', $course)
                    ->with('warning', "{$successCount} student(s) removed successfully. Some errors occurred: {$errorMessage}");
            } else {
                $errorMessage = implode('; ', $errors);
                return redirect()->route('courses.manage-students', $course)
                    ->with('error', "Failed to remove students: {$errorMessage}");
            }

        } catch (\Exception $e) {
            Log::error('Bulk student removal failed', [
                'course_id' => $course->id,
                'student_ids' => $studentIds,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('courses.manage-students', $course)
                ->with('error', 'Failed to remove students: ' . $e->getMessage());
        }
    }

    /**
     * Get eligible students for a course.
     */
    public function getEligibleStudents(Course $course)
    {
        // Load course grade levels
        $course->load('gradeLevels');
        $eligibleGradeLevelIds = $course->gradeLevels->pluck('id')->toArray();

        $query = Student::with(['user.role', 'gradeLevel'])
            ->whereHas('user.role', function ($q) {
                $q->where('name', 'student');
            });

        // Filter by grade levels from course_grade_level pivot table
        if (!empty($eligibleGradeLevelIds)) {
            $query->whereIn('grade_level_id', $eligibleGradeLevelIds);
        }

        // Exclude already enrolled students
        $enrolledStudentIds = $course->students->pluck('id')->toArray();
        $students = $query->whereNotIn('id', $enrolledStudentIds)
            ->orderBy('id')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'student_id_text' => $student->student_id_text ?? 'N/A',
                    'name' => $student->user->name ?? 'N/A',
                    'email' => $student->user->email ?? 'N/A',
                    'grade_level' => $student->gradeLevel->display_name ?? 'Not Set',
                ];
            });

        return response()->json([
            'students' => $students,
        ]);
    }

    /**
     * Get enrollment statistics for a course using StudentCourseEnrollmentService.
     */
    public function getEnrollmentStatistics(Course $course)
    {
        try {
            $statistics = $this->enrollmentService->getEnrollmentStatistics($course->id);
            
            return response()->json([
                'success' => true,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get enrollment statistics', [
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get enrollment statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get course enrollments using StudentCourseEnrollmentService.
     */
    public function getCourseEnrollments(Request $request, Course $course)
    {
        $validated = $request->validate([
            'status' => 'nullable|string|in:enrolled,completed,dropped,withdrawn',
            'enrolled_after' => 'nullable|date',
            'enrolled_before' => 'nullable|date'
        ]);

        try {
            $enrollments = $this->enrollmentService->getCourseEnrollments($course->id, $validated);
            
            return response()->json([
                'success' => true,
                'enrollments' => $enrollments
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get course enrollments', [
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get course enrollments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update enrollment status for a student using StudentCourseEnrollmentService.
     */
    public function updateEnrollmentStatus(Request $request, Course $course, User $studentUser)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:enrolled,completed,dropped,withdrawn',
            'grade' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            $student = $studentUser->student;
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'User does not have a student record'
                ], 400);
            }

            $result = $this->enrollmentService->updateEnrollmentStatus(
                $course->id,
                $student->id,
                $validated['status'],
                [
                    'grade' => $validated['grade'] ?? null,
                    'notes' => $validated['notes'] ?? null
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Enrollment status updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update enrollment status', [
                'course_id' => $course->id,
                'student_user_id' => $studentUser->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update enrollment status: ' . $e->getMessage()
            ], 500);
        }
    }
}
