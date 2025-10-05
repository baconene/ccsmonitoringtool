<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class CourseStudentController extends Controller
{
    /**
     * Show the course student management page.
     */
    public function index(Course $course)
    {
        // Load course with current enrollments and grade levels
        $course->load([
            'enrolledStudents' => function ($query) {
                $query->select('users.id', 'users.name', 'users.email', 'users.grade_level', 'users.section');
            },
            'gradeLevels'
        ]);

        // Get eligible grade level names for this course
        $eligibleGradeLevels = $course->gradeLevels->pluck('name')->toArray();

        // Get all students with matching grade level (if course has grade levels specified)
        $availableStudentsQuery = User::whereHas('role', function ($query) {
                $query->where('name', 'student');
            })
            ->select('id', 'name', 'email', 'grade_level', 'section');

        // Filter by grade levels from course_grade_level pivot table
        if (!empty($eligibleGradeLevels)) {
            $availableStudentsQuery->whereIn('grade_level', $eligibleGradeLevels);
        }

        // Exclude already enrolled students
        $enrolledStudentIds = $course->enrolledStudents->pluck('id')->toArray();
        $availableStudents = $availableStudentsQuery
            ->whereNotIn('id', $enrolledStudentIds)
            ->orderBy('name')
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
            'enrolledStudents' => $course->enrolledStudents->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'grade_level' => $student->grade_level,
                    'section' => $student->section,
                ];
            }),
            'availableStudents' => $availableStudents,
        ]);
    }

    /**
     * Enroll students to a course.
     */
    public function enrollStudents(Request $request, Course $course)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        $studentIds = $request->student_ids;

        // Load course grade levels from pivot table
        $course->load('gradeLevels');
        $eligibleGradeLevels = $course->gradeLevels->pluck('name')->toArray();

        // Verify students match grade level requirement from course_grade_level pivot
        if (!empty($eligibleGradeLevels)) {
            $validStudents = User::whereIn('id', $studentIds)
                ->whereHas('role', function ($query) {
                    $query->where('name', 'student');
                })
                ->whereIn('grade_level', $eligibleGradeLevels)
                ->pluck('id')
                ->toArray();

            if (count($validStudents) !== count($studentIds)) {
                $requiredGrades = implode(', ', $eligibleGradeLevels);
                return redirect()->back()->with('error', "Some students do not match the course grade level requirements ({$requiredGrades}).");
            }
        }

        // Enroll students
        foreach ($studentIds as $studentId) {
            // Check if already enrolled
            $exists = CourseEnrollment::where('course_id', $course->id)
                ->where('user_id', $studentId)
                ->exists();

            if (!$exists) {
                CourseEnrollment::create([
                    'course_id' => $course->id,
                    'user_id' => $studentId,
                    'enrolled_at' => now(),
                    'progress' => 0,
                    'is_completed' => false,
                ]);
            }
        }

        return redirect()->route('courses.manage-students', $course)->with('success', count($studentIds) . ' student(s) enrolled successfully.');
    }

    /**
     * Remove students from a course.
     */
    public function removeStudents(Request $request, Course $course)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        $studentIds = $request->student_ids;

        // Remove enrollments
        CourseEnrollment::where('course_id', $course->id)
            ->whereIn('user_id', $studentIds)
            ->delete();

        return redirect()->route('courses.manage-students', $course)->with('success', count($studentIds) . ' student(s) removed successfully.');
    }

    /**
     * Get eligible students for a course.
     */
    public function getEligibleStudents(Course $course)
    {
        // Load course grade levels from pivot table
        $course->load('gradeLevels');
        $eligibleGradeLevels = $course->gradeLevels->pluck('name')->toArray();

        $query = User::whereHas('role', function ($q) {
                $q->where('name', 'student');
            })
            ->select('id', 'name', 'email', 'grade_level', 'section');

        // Filter by grade levels from course_grade_level pivot table
        if (!empty($eligibleGradeLevels)) {
            $query->whereIn('grade_level', $eligibleGradeLevels);
        }

        // Exclude already enrolled
        $enrolledIds = $course->enrolledStudents()->pluck('users.id')->toArray();
        $students = $query->whereNotIn('id', $enrolledIds)
            ->orderBy('name')
            ->get();

        return response()->json([
            'students' => $students,
        ]);
    }
}
