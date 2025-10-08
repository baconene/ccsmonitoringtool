<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\GradeCalculatorService;
use App\Models\Student;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use League\Csv\Writer;
use Illuminate\Http\JsonResponse;

class GradeController extends Controller
{
    public function __construct(
        private GradeCalculatorService $gradeCalculator
    ) {}

    /**
     * Display student's own grade report
     */
    public function studentReport(Request $request): Response
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return Inertia::render('Error', [
                'message' => 'Student record not found. Please contact your administrator.'
            ]);
        }

        $courseId = $request->get('course_id');
        
        // Get available courses for this student using CourseEnrollment model
        $availableCourses = Course::whereHas('enrollments', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get(['id', 'title', 'description']);

        $courseGrades = null;
        $completeReport = null;
        $selectedCourse = null;

        if ($courseId) {
            // Verify student is enrolled in the requested course using CourseEnrollment
            $courseExists = Course::whereHas('enrollments', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('id', $courseId)->exists();
            
            if (!$courseExists) {
                return Inertia::render('Error', [
                    'message' => 'Access denied. You are not enrolled in this course.'
                ]);
            }
            
            // Get grades for specific course
            $selectedCourse = $courseId;
            $courseGrades = $this->gradeCalculator->calculateStudentCourseGrades($student->user_id, $courseId);
        } else {
            // Get complete report for all courses
            $completeReport = $this->gradeCalculator->getStudentSummary($student->user_id);
        }

        return Inertia::render('Student/Report', [
            'availableCourses' => $availableCourses,
            'courseGrades' => $courseGrades,
            'completeReport' => $completeReport,
            'selectedCourse' => $selectedCourse,
        ]);
    }

    /**
     * Display student's specific course report
     */
    public function studentCourseReport(Request $request, int $studentId, int $courseId): Response
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return Inertia::render('Error', [
                'message' => 'Student record not found. Please contact your administrator.'
            ]);
        }

        // Ensure the student can only access their own reports
        if ($student->id !== $studentId) {
            return Inertia::render('Error', [
                'message' => 'Access denied. You can only view your own reports.'
            ]);
        }

        // Verify student is enrolled in the requested course using CourseEnrollment
        $courseExists = Course::whereHas('enrollments', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('id', $courseId)->exists();
        
        if (!$courseExists) {
            return Inertia::render('Error', [
                'message' => 'Access denied. You are not enrolled in this course.'
            ]);
        }

        // Get available courses for navigation using CourseEnrollment
        $availableCourses = Course::whereHas('enrollments', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get(['id', 'title', 'description']);

        // Get grades for the specific course
        $courseGrades = $this->gradeCalculator->calculateStudentCourseGrades($student->user_id, $courseId);

        return Inertia::render('Student/Report', [
            'availableCourses' => $availableCourses,
            'courseGrades' => $courseGrades,
            'completeReport' => null,
            'selectedCourse' => $courseId,
        ]);
    }

    /**
     * Display instructor's course grade reports
     */
    public function instructorReport(Request $request): Response
    {
        $user = Auth::user();
        
        // Check if user is instructor or admin
        if (!$user->isInstructor() && !$user->isAdmin()) {
            return Inertia::render('Error', [
                'message' => 'Access denied. You must be an instructor or admin to view this page.'
            ]);
        }

        $courseId = $request->get('course_id');
        
        // Get courses the instructor teaches (or all courses for admin)
        $coursesQuery = Course::with(['modules', 'students']);
        
        if ($user->isInstructor() && !$user->isAdmin()) {
            $coursesQuery->where('instructor_id', $user->id);
        }
        
        $courses = $coursesQuery->get();
        
        $selectedCourse = null;
        $courseGrades = null;
        
        if ($courseId && $courses->contains('id', $courseId)) {
            $selectedCourse = $courses->firstWhere('id', $courseId);
            $courseGrades = $this->gradeCalculator->calculateCourseStudentGrades($courseId);
        }

        return Inertia::render('Instructor/Report', [
            'teachingCourses' => $courses->map(function($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'students_count' => $course->students->count(),
                    'modules_count' => $course->modules->count(),
                ];
            }),
            'courseReport' => $courseGrades,
            'selectedCourse' => $courseId,
            'pageTitle' => 'Grade Reports',
        ]);
    }

    /**
     * Get detailed grade data for AJAX requests (student)
     */
    public function getStudentGradeData(Request $request, int $courseId): JsonResponse
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();
        
        $grades = $this->gradeCalculator->getCachedStudentGrades($student->id, $courseId);
        
        return response()->json($grades);
    }

    /**
     * Get instructor grade data for AJAX requests
     */
    public function getInstructorGradeData(Request $request, int $courseId): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user->isInstructor() && !$user->isAdmin()) {
            abort(403, 'Access denied');
        }

        $course = Course::findOrFail($courseId);
        
        if ($user->isInstructor() && !$user->isAdmin() && $course->instructor_id !== $user->id) {
            abort(403, 'Access denied');
        }

        $courseGrades = $this->gradeCalculator->calculateCourseStudentGrades($courseId);
        
        return response()->json($courseGrades);
    }

    /**
     * Export student's grade report as PDF
     */
    public function exportStudentPDF(Request $request, int $courseId = null)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        if ($courseId) {
            // Export single course
            $grades = $this->gradeCalculator->getCachedStudentGrades($student->id, $courseId);
            $course = Course::findOrFail($courseId);
            
            $data = [
                'grades' => $grades,
                'type' => 'single_course',
                'title' => "Grade Report - {$course->title}",
                'student_name' => $student->user->name,
                'generated_at' => now(),
            ];
            
            $pdf = Pdf::loadView('pdf.student-grade-report', $data);
            
            return $pdf->download("{$student->student_id}_{$course->title}_grades.pdf");
        } else {
            // Export all courses
            $summary = $this->gradeCalculator->getStudentSummary($student->user_id);
            
            $data = [
                'summary' => $summary,
                'type' => 'all_courses',
                'title' => 'Complete Grade Report',
                'student_name' => $student->user->name,
                'generated_at' => now(),
            ];
            
            $pdf = Pdf::loadView('pdf.student-complete-report', $data);
            
            return $pdf->download("{$student->student_id}_complete_grades.pdf");
        }
    }

    /**
     * Export student's grade report as CSV
     */
    public function exportStudentCSV(Request $request, int $courseId = null): StreamedResponse
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        return response()->streamDownload(function () use ($student, $courseId) {
            $csv = Writer::createFromString('');
            
            // Add BOM for proper UTF-8 handling in Excel
            $csv->insertOne(["\xEF\xBB\xBF"]);
            
            if ($courseId) {
                // Export single course
                $grades = $this->gradeCalculator->getCachedStudentGrades($student->id, $courseId);
                $this->addCourseDataToCSV($csv, $grades);
                $filename = "{$student->student_id}_{$grades['course']['title']}_grades.csv";
            } else {
                // Export all courses
                $summary = $this->gradeCalculator->getStudentSummary($student->user_id);

                $csv->insertOne(['Student Grade Report - All Courses']);
                $csv->insertOne(['Student Name', $summary['student']['name']]);
                $csv->insertOne(['Student ID', $summary['student']['student_id']]);
                $csv->insertOne(['Overall GPA', $summary['overall_gpa']]);
                $csv->insertOne(['Generated', now()->format('Y-m-d H:i:s')]);
                $csv->insertOne([]);

                foreach ($summary['courses'] as $courseGrades) {
                    $this->addCourseDataToCSV($csv, $courseGrades);
                    $csv->insertOne([]); // Empty row between courses
                }
                
                $filename = "{$summary['student']['student_id']}_complete_grades.csv";
            }

            echo $csv->toString();
        }, $filename ?? 'grades.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . ($filename ?? 'grades.csv') . '"'
        ]);
    }

    /**
     * Export instructor's course grades as PDF
     */
    public function exportInstructorPDF(Request $request, int $courseId)
    {
        $user = Auth::user();
        
        if (!$user->isInstructor() && !$user->isAdmin()) {
            abort(403, 'Access denied');
        }

        $course = Course::findOrFail($courseId);
        
        // Check if instructor has access to this course
        if ($user->isInstructor() && !$user->isAdmin() && $course->instructor_id !== $user->id) {
            abort(403, 'Access denied');
        }

        $courseGrades = $this->gradeCalculator->calculateCourseStudentGrades($courseId);
        
        $data = [
            'courseGrades' => $courseGrades,
            'instructor' => $user,
            'title' => "Course Grade Report - {$course->title}",
            'generated_at' => now(),
        ];
        
        $pdf = Pdf::loadView('pdf.instructor-course-report', $data);
        
        return $pdf->download("course_{$course->id}_{$course->title}_grades.pdf");
    }

    /**
     * Export instructor's course grades as CSV
     */
    public function exportInstructorCSV(Request $request, int $courseId): StreamedResponse
    {
        $user = Auth::user();
        
        if (!$user->isInstructor() && !$user->isAdmin()) {
            abort(403, 'Access denied');
        }

        $course = Course::findOrFail($courseId);
        
        // Check if instructor has access to this course
        if ($user->isInstructor() && !$user->isAdmin() && $course->instructor_id !== $user->id) {
            abort(403, 'Access denied');
        }

        $courseGrades = $this->gradeCalculator->calculateCourseStudentGrades($courseId);

        return response()->streamDownload(function () use ($courseGrades) {
            $csv = Writer::createFromString('');
            
            // Add BOM for proper UTF-8 handling in Excel
            $csv->insertOne(["\xEF\xBB\xBF"]);
            
            // Course header
            $csv->insertOne(['Course Grade Report']);
            $csv->insertOne(['Course', $courseGrades['course']['title']]);
            $csv->insertOne(['Generated', $courseGrades['generated_at']->format('Y-m-d H:i:s')]);
            $csv->insertOne([]);
            
            // Class statistics
            $stats = $courseGrades['class_statistics'];
            $csv->insertOne(['Class Statistics']);
            $csv->insertOne(['Total Students', $stats['total_students']]);
            $csv->insertOne(['Average Grade', $stats['average_grade'] . '%']);
            $csv->insertOne(['Highest Grade', $stats['highest_grade'] . '%']);
            $csv->insertOne(['Lowest Grade', $stats['lowest_grade'] . '%']);
            $csv->insertOne(['Passing Students', $stats['passing_count']]);
            $csv->insertOne(['Failing Students', $stats['failing_count']]);
            $csv->insertOne(['Completion Rate', $stats['completion_rate'] . '%']);
            $csv->insertOne([]);
            
            // Student grades header
            $csv->insertOne([
                'Rank',
                'Student Name',
                'Student Email',
                'Overall Grade',
                'Letter Grade',
                'Completion Status',
                'Completed Modules',
                'Total Modules',
                'Activities Completed',
                'Total Activities'
            ]);
            
            // Student data
            foreach ($courseGrades['students'] as $index => $student) {
                $csv->insertOne([
                    $index + 1,
                    $student['student_name'],
                    $student['student_email'],
                    $student['overall_grade'] . '%',
                    $student['overall_letter_grade'],
                    ucfirst(str_replace('_', ' ', $student['completion_status'])),
                    $student['completed_modules'],
                    $student['module_count'],
                    $student['activity_summary']['completed'],
                    $student['activity_summary']['total']
                ]);
            }

            echo $csv->toString();
        }, "course_{$course->id}_{$course->title}_grades.csv", [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="course_' . $course->id . '_grades.csv"'
        ]);
    }

    /**
     * Add course data to CSV writer
     */
    private function addCourseDataToCSV(Writer $csv, array $grades): void
    {
        // Course header
        $csv->insertOne(['Course Grade Report']);
        $csv->insertOne(['Student', $grades['student']['name']]);
        $csv->insertOne(['Course', $grades['course']['title']]);
        $csv->insertOne(['Overall Grade', $grades['overall_grade'] . '%']);
        $csv->insertOne(['Letter Grade', $grades['overall_letter_grade']]);
        $csv->insertOne(['Status', ucfirst(str_replace('_', ' ', $grades['completion_status']))]);
        $csv->insertOne(['Generated', $grades['generated_at']->format('Y-m-d H:i:s')]);
        $csv->insertOne([]);
        
        // Module headers
        $csv->insertOne([
            'Module',
            'Module Type',
            'Module Score',
            'Letter Grade',
            'Status',
            'Activities Completed'
        ]);
        
        // Module data
        foreach ($grades['modules'] as $module) {
            $csv->insertOne([
                $module['module_title'],
                $module['module_type'],
                $module['module_score'] . '%',
                $module['module_letter_grade'],
                ucfirst(str_replace('_', ' ', $module['completion_status'])),
                collect($module['activities'])->where('is_completed', true)->count() . '/' . count($module['activities'])
            ]);
        }
        
        $csv->insertOne([]);
        
        // Activity details header
        $csv->insertOne([
            'Module',
            'Activity',
            'Type',
            'Score',
            'Max Score',
            'Percentage',
            'Letter Grade',
            'Status',
            'Due Date',
            'Submitted'
        ]);
        
        // Activity data
        foreach ($grades['modules'] as $module) {
            foreach ($module['activities'] as $activity) {
                $csv->insertOne([
                    $module['module_title'],
                    $activity['activity_title'],
                    $activity['activity_type'],
                    $activity['score'],
                    $activity['max_score'],
                    $activity['percentage_score'] . '%',
                    $activity['letter_grade'],
                    $activity['is_completed'] ? 'Completed' : 'Not Completed',
                    $activity['due_date'] ? $activity['due_date']->format('Y-m-d') : 'No due date',
                    $activity['submitted_at'] ? $activity['submitted_at']->format('Y-m-d H:i') : 'Not submitted'
                ]);
            }
        }
    }

    /**
     * Export student's single course report as PDF
     */
    public function studentReportPDF(Request $request)
    {
        $courseId = $request->query('course_id');
        return $this->exportStudentPDF($request, $courseId);
    }

    /**
     * Export student's complete report as PDF
     */
    public function studentCompleteReportPDF(Request $request)
    {
        return $this->exportStudentPDF($request, null);
    }

    /**
     * Export student's single course report as CSV
     */
    public function studentReportCSV(Request $request)
    {
        $courseId = $request->query('course_id');
        return $this->exportStudentCSV($request, $courseId);
    }

    /**
     * Export student's complete report as CSV
     */
    public function studentCompleteReportCSV(Request $request)
    {
        return $this->exportStudentCSV($request, null);
    }

    /**
     * Export specific student course report as PDF
     */
    public function studentCourseReportPDF(Request $request, int $studentId, int $courseId)
    {
        // For now, redirect to the general export method
        // In a full implementation, you might want separate logic for viewing other students' reports
        return $this->exportStudentPDF($request, $courseId);
    }

    /**
     * Export specific student course report as CSV
     */
    public function studentCourseReportCSV(Request $request, int $studentId, int $courseId)
    {
        // For now, redirect to the general export method
        // In a full implementation, you might want separate logic for viewing other students' reports
        return $this->exportStudentCSV($request, $courseId);
    }
}