<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use App\Models\CourseEnrollment;
use App\Models\Activity;
use App\Models\StudentActivity;
use App\Models\GradeLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class StudentManagementController extends Controller
{
    /**
     * Display the student management page for instructor
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $instructor = $user->instructor;

        if (!$instructor) {
            abort(403, 'You must be an instructor to access this page.');
        }

        // Get instructor's courses
        $courses = Course::where('instructor_id', $instructor->id)
            ->with(['gradeLevels'])
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'title' => $course->title,
                    'total_students' => $course->courseEnrollments()->count(),
                ];
            });

        // Get all grade levels for filtering
        $gradeLevels = GradeLevel::all()->map(function ($gl) {
            return [
                'id' => $gl->id,
                'name' => $gl->name,
                'display_name' => $gl->display_name,
            ];
        });

        return Inertia::render('Instructor/StudentManagement', [
            'courses' => $courses,
            'gradeLevels' => $gradeLevels,
        ]);
    }

    /**
     * Get students for a specific course with their progress
     */
    public function getStudentsByCourse(Request $request, Course $course)
    {
        $user = auth()->user();
        $instructor = $user->instructor;

        // Verify instructor owns this course
        if ($course->instructor_id !== $instructor->id) {
            abort(403, 'Unauthorized access to this course.');
        }

        $search = $request->input('search', '');
        $gradeLevel = $request->input('grade_level');
        $section = $request->input('section');
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');

        // Get enrolled students with their progress
        $query = CourseEnrollment::with([
            'student.user',
            'student.gradeLevel',
        ])->where('course_id', $course->id);

        $enrollments = $query->get();

        // Map and filter students
        $students = $enrollments->map(function ($enrollment) use ($course) {
            $student = $enrollment->student;
            
            // Get activity progress for this course
            $activities = Activity::where('course_id', $course->id)->get();
            $totalActivities = $activities->count();
            
            $completedActivities = StudentActivity::where('student_id', $student->id)
                ->whereIn('activity_id', $activities->pluck('id'))
                ->where('status', 'completed')
                ->count();

            $submittedActivities = StudentActivity::where('student_id', $student->id)
                ->whereIn('activity_id', $activities->pluck('id'))
                ->where('status', 'submitted')
                ->count();

            // Get average grade
            $avgGrade = StudentActivity::where('student_id', $student->id)
                ->whereIn('activity_id', $activities->pluck('id'))
                ->whereNotNull('grade')
                ->avg('grade');

            return [
                'id' => $student->id,
                'user_id' => $student->user_id,
                'student_id_text' => $student->student_id_text,
                'name' => $student->user->name ?? 'N/A',
                'email' => $student->user->email ?? 'N/A',
                'section' => $student->section,
                'grade_level' => $student->gradeLevel ? [
                    'id' => $student->gradeLevel->id,
                    'name' => $student->gradeLevel->name,
                    'display_name' => $student->gradeLevel->display_name,
                ] : null,
                'enrolled_at' => $enrollment->enrolled_at,
                'course_progress' => round($enrollment->progress ?? 0, 2),
                'is_completed' => $enrollment->is_completed ?? false,
                'total_activities' => $totalActivities,
                'completed_activities' => $completedActivities,
                'submitted_activities' => $submittedActivities,
                'pending_activities' => $totalActivities - $completedActivities - $submittedActivities,
                'average_grade' => $avgGrade ? round($avgGrade, 2) : null,
            ];
        });

        // Apply search filter
        if ($search) {
            $students = $students->filter(function ($student) use ($search) {
                return stripos($student['name'], $search) !== false ||
                       stripos($student['email'], $search) !== false ||
                       stripos($student['student_id_text'], $search) !== false;
            });
        }

        // Apply grade level filter
        if ($gradeLevel) {
            $students = $students->filter(function ($student) use ($gradeLevel) {
                return $student['grade_level'] && $student['grade_level']['id'] == $gradeLevel;
            });
        }

        // Apply section filter
        if ($section) {
            $students = $students->filter(function ($student) use ($section) {
                return $student['section'] === $section;
            });
        }

        // Apply sorting
        $students = $students->sortBy([
            function ($a, $b) use ($sortBy, $sortOrder) {
                $valueA = $a[$sortBy] ?? '';
                $valueB = $b[$sortBy] ?? '';
                
                if ($sortOrder === 'asc') {
                    return $valueA <=> $valueB;
                } else {
                    return $valueB <=> $valueA;
                }
            }
        ])->values();

        return response()->json([
            'students' => $students,
            'course' => [
                'id' => $course->id,
                'name' => $course->name,
                'title' => $course->title,
            ],
        ]);
    }

    /**
     * Get detailed activity progress for a specific student in a course
     */
    public function getStudentActivities(Request $request, Course $course, Student $student)
    {
        $user = auth()->user();
        $instructor = $user->instructor;

        // Verify instructor owns this course
        if ($course->instructor_id !== $instructor->id) {
            abort(403, 'Unauthorized access to this course.');
        }

        // Verify student is enrolled in the course
        $enrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('student_id', $student->id)
            ->first();

        if (!$enrollment) {
            abort(404, 'Student is not enrolled in this course.');
        }

        // Get all activities for the course with student progress
        $activities = Activity::where('course_id', $course->id)
            ->with(['module'])
            ->get()
            ->map(function ($activity) use ($student) {
                $studentActivity = StudentActivity::where('activity_id', $activity->id)
                    ->where('student_id', $student->id)
                    ->first();

                return [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'type' => $activity->type,
                    'description' => $activity->description,
                    'points' => $activity->points,
                    'due_date' => $activity->due_date,
                    'module' => $activity->module ? [
                        'id' => $activity->module->id,
                        'title' => $activity->module->title,
                    ] : null,
                    'student_progress' => $studentActivity ? [
                        'status' => $studentActivity->status,
                        'grade' => $studentActivity->grade,
                        'feedback' => $studentActivity->feedback,
                        'submitted_at' => $studentActivity->submitted_at,
                        'completed_at' => $studentActivity->completed_at,
                        'progress' => $studentActivity->progress,
                    ] : [
                        'status' => 'not_started',
                        'grade' => null,
                        'feedback' => null,
                        'submitted_at' => null,
                        'completed_at' => null,
                        'progress' => 0,
                    ],
                ];
            });

        return response()->json([
            'student' => [
                'id' => $student->id,
                'name' => $student->user->name,
                'email' => $student->user->email,
                'student_id_text' => $student->student_id_text,
                'grade_level' => $student->gradeLevel ? $student->gradeLevel->display_name : null,
            ],
            'course' => [
                'id' => $course->id,
                'name' => $course->name,
                'title' => $course->title,
            ],
            'activities' => $activities,
            'enrollment' => [
                'enrolled_at' => $enrollment->enrolled_at,
                'progress' => $enrollment->progress,
                'is_completed' => $enrollment->is_completed,
            ],
        ]);
    }

    /**
     * Export student report for a course
     */
    public function exportReport(Request $request, Course $course)
    {
        $user = auth()->user();
        $instructor = $user->instructor;

        // Verify instructor owns this course
        if ($course->instructor_id !== $instructor->id) {
            abort(403, 'Unauthorized access to this course.');
        }

        $format = $request->input('format', 'csv'); // csv or excel
        $gradeLevel = $request->input('grade_level');
        $section = $request->input('section');

        // Get enrolled students with their progress
        $query = CourseEnrollment::with([
            'student.user',
            'student.gradeLevel',
        ])->where('course_id', $course->id);

        $enrollments = $query->get();

        // Filter by grade level if specified
        if ($gradeLevel) {
            $enrollments = $enrollments->filter(function ($enrollment) use ($gradeLevel) {
                return $enrollment->student->grade_level_id == $gradeLevel;
            });
        }

        // Filter by section if specified
        if ($section) {
            $enrollments = $enrollments->filter(function ($enrollment) use ($section) {
                return $enrollment->student->section === $section;
            });
        }

        // Prepare data for export
        $data = $enrollments->map(function ($enrollment) use ($course) {
            $student = $enrollment->student;
            
            // Get activity stats
            $activities = Activity::where('course_id', $course->id)->get();
            $totalActivities = $activities->count();
            
            $completedActivities = StudentActivity::where('student_id', $student->id)
                ->whereIn('activity_id', $activities->pluck('id'))
                ->where('status', 'completed')
                ->count();

            $submittedActivities = StudentActivity::where('student_id', $student->id)
                ->whereIn('activity_id', $activities->pluck('id'))
                ->where('status', 'submitted')
                ->count();

            $avgGrade = StudentActivity::where('student_id', $student->id)
                ->whereIn('activity_id', $activities->pluck('id'))
                ->whereNotNull('grade')
                ->avg('grade');

            return [
                'Student ID' => $student->student_id_text,
                'Name' => $student->user->name ?? 'N/A',
                'Email' => $student->user->email ?? 'N/A',
                'Grade Level' => $student->gradeLevel->display_name ?? 'Not Set',
                'Section' => $student->section ?? 'Not Set',
                'Enrolled Date' => $enrollment->enrolled_at,
                'Course Progress (%)' => round($enrollment->progress ?? 0, 2),
                'Completed' => $enrollment->is_completed ? 'Yes' : 'No',
                'Total Activities' => $totalActivities,
                'Completed Activities' => $completedActivities,
                'Submitted Activities' => $submittedActivities,
                'Pending Activities' => $totalActivities - $completedActivities - $submittedActivities,
                'Average Grade' => $avgGrade ? round($avgGrade, 2) : 'N/A',
            ];
        });

        // Generate CSV
        if ($format === 'csv') {
            $filename = 'student_report_' . $course->name . '_' . now()->format('Y-m-d') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                
                // Add headers
                if ($data->count() > 0) {
                    fputcsv($file, array_keys($data->first()));
                }
                
                // Add data
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
                
                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        // For now, only CSV is supported
        return response()->json(['error' => 'Only CSV format is currently supported'], 400);
    }

    /**
     * Get statistics for all courses taught by instructor
     */
    public function getStatistics(Request $request)
    {
        $user = auth()->user();
        $instructor = $user->instructor;

        if (!$instructor) {
            abort(403, 'You must be an instructor to access this page.');
        }

        $courses = Course::where('instructor_id', $instructor->id)->get();
        
        $totalStudents = CourseEnrollment::whereIn('course_id', $courses->pluck('id'))->distinct('student_id')->count('student_id');
        
        $totalEnrollments = CourseEnrollment::whereIn('course_id', $courses->pluck('id'))->count();
        
        $completedEnrollments = CourseEnrollment::whereIn('course_id', $courses->pluck('id'))
            ->where('is_completed', true)
            ->count();
        
        $avgProgress = CourseEnrollment::whereIn('course_id', $courses->pluck('id'))->avg('progress');

        // Get grade level distribution
        $gradeLevelDistribution = DB::table('course_enrollments')
            ->join('students', 'course_enrollments.student_id', '=', 'students.id')
            ->join('grade_levels', 'students.grade_level_id', '=', 'grade_levels.id')
            ->whereIn('course_enrollments.course_id', $courses->pluck('id'))
            ->select('grade_levels.display_name', DB::raw('count(*) as count'))
            ->groupBy('grade_levels.display_name')
            ->get();

        return response()->json([
            'total_students' => $totalStudents,
            'total_enrollments' => $totalEnrollments,
            'completed_enrollments' => $completedEnrollments,
            'average_progress' => round($avgProgress ?? 0, 2),
            'grade_level_distribution' => $gradeLevelDistribution,
        ]);
    }
}
