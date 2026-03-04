<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Student;
use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;
use App\Models\StudentAssignmentAnswer;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssignmentGradingController extends Controller
{
    use AuthorizesRequests;
    /**
     * Get submissions list for an assignment (API endpoint)
     */
    public function submissions(Assignment $assignment)
    {
        // Get all ModuleActivity records for this assignment's activity
        // This will show us all courses that include this activity
        $moduleActivities = \App\Models\ModuleActivity::where('activity_id', $assignment->activity_id)
            ->with(['module.course'])
            ->get();

        if ($moduleActivities->isEmpty()) {
            return response()->json([]);
        }

        // Get all unique courses that contain this activity
        $courses = $moduleActivities->map(fn($ma) => $ma->module->course)->unique('id');

        // Get all enrolled students from ALL courses
        $allEnrolledStudents = collect();
        foreach ($courses as $course) {
            $enrolledUsers = $course->enrolledStudents; // User models
            
            // Convert users to students and add course info
            foreach ($enrolledUsers as $user) {
                $student = Student::where('user_id', $user->id)->first();
                if ($student) {
                    $allEnrolledStudents->push([
                        'student' => $student,
                        'user' => $user,
                        'course' => $course,
                    ]);
                }
            }
        }

        // Remove duplicates based on student_id (student might be in multiple courses)
        $allEnrolledStudents = $allEnrolledStudents->unique(fn($item) => $item['student']->id);

        // Get all StudentActivity records for this assignment's activity
        $studentActivities = StudentActivity::where('activity_id', $assignment->activity_id)
            ->where('activity_type', 'assignment')
            ->get()
            ->keyBy('student_id');

        $submissions = $allEnrolledStudents->map(function ($enrollment) use ($assignment, $studentActivities) {
            $student = $enrollment['student'];
            $user = $enrollment['user'];
            $course = $enrollment['course'];

            // Check if this student has a StudentActivity record
            $studentActivity = $studentActivities->get($student->id);

            // Get the corresponding progress record for additional data
            $progress = StudentActivityProgress::where('activity_id', $assignment->activity_id)
                ->where('student_id', $student->id)
                ->where('activity_type', 'assignment')
                ->first();

            // Calculate status
            $status = 'not_started';
            if ($studentActivity) {
                $status = $studentActivity->status;
            } elseif ($progress) {
                if ($progress->completed_at) {
                    $status = $progress->graded_at ? 'graded' : 'submitted';
                } else {
                    $status = 'in_progress';
                }
            }

            // Calculate progress percentage
            $progressPercent = 0;
            if ($studentActivity) {
                $progressPercent = $studentActivity->percentage_score ?? 0;
            } elseif ($progress) {
                $progressPercent = $progress->progress ?? 0;
            }

            // Only return submission if student has activity record
            if (!$studentActivity) {
                return null;
            }

            return [
                'id' => $studentActivity->id,
                'student_id' => $student->id,
                'student_name' => $user->name,
                'course_id' => $course->id,
                'course_code' => $course->course_code,
                'course_name' => $course->course_name,
                'progress' => $progressPercent,
                'score' => $studentActivity->score,
                'total_score' => $studentActivity->max_score ?? ($assignment->total_score ?? 100),
                'status' => $status,
                'submitted_at' => $studentActivity->submitted_at,
                'graded_at' => $studentActivity->graded_at,
            ];
        })->filter()->values();

        return response()->json($submissions);
    }

    /**
     * View individual student submission for grading
     */
    public function viewSubmission(Assignment $assignment, $studentActivityId)
    {
        // Fetch StudentActivity by ID
        $studentActivity = StudentActivity::findOrFail($studentActivityId);

        // Ensure the assignment belongs to an activity owned by the authenticated instructor
        $this->authorize('grade', $assignment);

        // Load necessary relationships
        $studentActivity->load(['student.user']);
        
        $questions = $assignment->questions()
            ->with('options')
            ->orderBy('order')
            ->get();

        // Fetch answers from StudentAssignmentAnswer table
        $answers = StudentAssignmentAnswer::where('student_id', $studentActivity->student_id)
            ->where('assignment_id', $assignment->id)
            ->with('question.options')
            ->get();

        $student = $studentActivity->student;

        return Inertia::render('Instructor/StudentSubmissionReview', [
            'assignment' => $assignment->load('activity'),
            'progress' => $studentActivity,
            'student' => $student,
            'questions' => $questions,
            'answers' => $answers,
        ]);
    }

    /**
     * Grade an individual question
     */
    public function gradeQuestion(Request $request, Assignment $assignment, $studentActivityId)
    {
        $studentActivity = StudentActivity::findOrFail($studentActivityId);
        
        $this->authorize('grade', $assignment);

        $validated = $request->validate([
            'answer_id' => 'required|exists:student_assignment_answers,id',
            'points_earned' => 'required|numeric|min:0',
            'instructor_feedback' => 'nullable|string',
        ]);

        $answer = StudentAssignmentAnswer::findOrFail($validated['answer_id']);
        
        // Ensure the answer belongs to this student and assignment
        if ($answer->student_id !== $studentActivity->student_id || $answer->assignment_id !== $assignment->id) {
            abort(403, 'Unauthorized');
        }

        $answer->update([
            'points_earned' => $validated['points_earned'],
            'instructor_feedback' => $validated['instructor_feedback'],
        ]);

        return back()->with('success', 'Question graded successfully');
    }

    /**
     * Submit final grade for assignment
     */
    public function submitGrade(Request $request, Assignment $assignment, $studentActivityId)
    {
        $studentActivity = StudentActivity::findOrFail($studentActivityId);
        
        $this->authorize('grade', $assignment);

        $validated = $request->validate([
            'total_score' => 'required|numeric|min:0',
            'instructor_feedback' => 'nullable|string',
            'question_grades' => 'required|array',
        ]);

        // Update individual question grades
        foreach ($validated['question_grades'] as $questionId => $gradeData) {
            $answer = StudentAssignmentAnswer::where('student_id', $studentActivity->student_id)
                ->where('assignment_id', $assignment->id)
                ->whereHas('question', function ($query) use ($questionId) {
                    $query->where('id', $questionId);
                })
                ->first();

            if ($answer) {
                $answer->update([
                    'points_earned' => $gradeData['points'] ?? 0,
                    'instructor_feedback' => $gradeData['feedback'] ?? null,
                ]);
            }
        }

        // Update student activity with final score and status
        $maxScore = $studentActivity->max_score ?? $assignment->total_points ?? 100;
        $percentageScore = $maxScore > 0 
            ? ($validated['total_score'] / $maxScore) * 100 
            : 0;

        $studentActivity->update([
            'score' => $validated['total_score'],
            'percentage_score' => $percentageScore,
            'status' => 'graded',
            'graded_at' => now(),
            'feedback' => $validated['instructor_feedback'],
        ]);

        // Auto-update course enrollment progress
        $enrollment = \App\Models\CourseEnrollment::where('student_id', $studentActivity->student_id)
            ->where('course_id', $studentActivity->course_id)
            ->first();
        
        if ($enrollment) {
            $enrollment->updateProgress();
            $enrollment->checkAndCompleteModules();
        }

        return redirect()
            ->route('activities.manage', $assignment->activity_id)
            ->with('success', 'Grade submitted successfully');
    }
}
