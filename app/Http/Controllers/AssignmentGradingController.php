<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Student;
use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;
use App\Models\StudentAssignmentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AssignmentGradingController extends Controller
{
    /**
     * Show list of submissions for grading
     */
    public function index(Assignment $assignment)
    {
        $activity = $assignment->activity;
        $courseId = $activity->modules()->first()->course_id ?? null;

        if (!$courseId) {
            return redirect()->back()->with('error', 'Assignment not associated with a course');
        }

        // Get all student submissions
        $submissions = Student::whereHas('courseStudents', function ($query) use ($courseId) {
            $query->where('course_id', $courseId)
                  ->where('status', 'enrolled');
        })
        ->with(['user'])
        ->get()
        ->map(function ($student) use ($activity, $assignment) {
            $studentActivity = StudentActivity::where('student_id', $student->id)
                ->where('activity_id', $activity->id)
                ->first();

            if (!$studentActivity) {
                return null;
            }

            $progress = StudentActivityProgress::where('student_activity_id', $studentActivity->id)
                ->where('activity_type', 'assignment')
                ->first();

            if (!$progress) {
                return null;
            }

            // Get answer count
            $answerCount = StudentAssignmentAnswer::where('student_id', $student->id)
                ->where('assignment_id', $assignment->id)
                ->count();

            // Check if has file upload
            $hasFileUpload = StudentAssignmentAnswer::where('student_id', $student->id)
                ->where('assignment_id', $assignment->id)
                ->whereNotNull('file_path')
                ->exists();

            return [
                'student_id' => $student->id,
                'student_name' => $student->user->name,
                'student_number' => $student->student_number,
                'status' => $studentActivity->status,
                'submission_status' => $progress->submission_status,
                'submitted_at' => $studentActivity->submitted_at,
                'graded_at' => $studentActivity->graded_at,
                'score' => $studentActivity->score,
                'percentage_score' => $studentActivity->percentage_score,
                'auto_graded_score' => $progress->auto_graded_score,
                'requires_grading' => $progress->requires_grading,
                'answered_questions' => $progress->answered_questions,
                'total_questions' => $progress->total_questions,
                'has_file_upload' => $hasFileUpload,
                'answer_count' => $answerCount,
                'needs_attention' => $progress->submission_status === 'submitted' && $progress->requires_grading,
            ];
        })
        ->filter() // Remove nulls
        ->values();

        return Inertia::render('ActivityManagement/Assignment/AssignmentGrading', [
            'assignment' => $assignment->load(['activity', 'questions']),
            'submissions' => $submissions,
            'stats' => [
                'total_students' => $submissions->count(),
                'submitted' => $submissions->where('submission_status', 'submitted')->count(),
                'graded' => $submissions->where('status', 'graded')->count(),
                'pending_grading' => $submissions->where('needs_attention', true)->count(),
                'not_started' => $submissions->where('status', 'not_started')->count(),
            ],
        ]);
    }

    /**
     * Show individual student submission for grading
     */
    public function show(Assignment $assignment, Student $student)
    {
        $activity = $assignment->activity;

        $studentActivity = StudentActivity::where('student_id', $student->id)
            ->where('activity_id', $activity->id)
            ->firstOrFail();

        $progress = StudentActivityProgress::where('student_activity_id', $studentActivity->id)
            ->where('activity_type', 'assignment')
            ->firstOrFail();

        // Check if submitted
        if ($progress->submission_status !== 'submitted' && $studentActivity->status !== 'graded') {
            return redirect()->back()->with('error', 'Student has not submitted this assignment yet');
        }

        // Load assignment with questions
        $assignmentData = Assignment::with([
            'questions.options',
            'activity'
        ])->find($assignment->id);

        // Get all student answers
        $studentAnswers = StudentAssignmentAnswer::where('student_id', $student->id)
            ->where('assignment_id', $assignment->id)
            ->with('question')
            ->get();

        // Format answers by question
        $questionAnswers = $assignmentData->questions->map(function ($question) use ($studentAnswers) {
            $answer = $studentAnswers->firstWhere('assignment_question_id', $question->id);
            
            $selectedOptions = null;
            if ($answer && $answer->selected_options) {
                $selectedOptions = $question->options
                    ->whereIn('id', $answer->selected_options)
                    ->pluck('option_text', 'id');
            }

            return [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'question_type_display' => $question->question_type_display,
                'points' => $question->points,
                'correct_answer' => $question->correct_answer,
                'acceptable_answers' => $question->acceptable_answers,
                'explanation' => $question->explanation,
                'options' => $question->options->map(fn($opt) => [
                    'id' => $opt->id,
                    'option_text' => $opt->option_text,
                    'is_correct' => $opt->is_correct,
                ]),
                'student_answer' => $answer ? [
                    'id' => $answer->id,
                    'answer_text' => $answer->answer_text,
                    'selected_options' => $selectedOptions,
                    'selected_option_ids' => $answer->selected_options,
                    'is_correct' => $answer->is_correct,
                    'points_earned' => $answer->points_earned,
                    'instructor_feedback' => $answer->instructor_feedback,
                    'answered_at' => $answer->answered_at,
                ] : null,
            ];
        });

        // Get file upload
        $fileUpload = $studentAnswers->firstWhere('file_path', '!=', null);

        return Inertia::render('ActivityManagement/Assignment/GradeSubmission', [
            'assignment' => $assignmentData,
            'student' => [
                'id' => $student->id,
                'name' => $student->user->name,
                'student_number' => $student->student_number,
                'email' => $student->user->email,
            ],
            'studentActivity' => $studentActivity,
            'progress' => $progress,
            'questionAnswers' => $questionAnswers,
            'fileUpload' => $fileUpload ? [
                'id' => $fileUpload->id,
                'file_url' => $fileUpload->file_url,
                'original_filename' => $fileUpload->original_filename,
                'points_earned' => $fileUpload->points_earned,
                'instructor_feedback' => $fileUpload->instructor_feedback,
                'answered_at' => $fileUpload->answered_at,
            ] : null,
            'summary' => [
                'total_points' => $assignment->total_points,
                'auto_graded_score' => $progress->auto_graded_score,
                'current_score' => $studentActivity->score,
                'requires_grading' => $progress->requires_grading,
                'submitted_at' => $studentActivity->submitted_at,
                'graded_at' => $studentActivity->graded_at,
            ],
        ]);
    }

    /**
     * Grade student submission
     */
    public function grade(Request $request, Assignment $assignment, Student $student)
    {
        $validated = $request->validate([
            'question_grades' => 'nullable|array',
            'question_grades.*.answer_id' => 'required|exists:student_assignment_answers,id',
            'question_grades.*.points_earned' => 'required|numeric|min:0',
            'question_grades.*.feedback' => 'nullable|string',
            'file_upload_grade' => 'nullable|array',
            'file_upload_grade.answer_id' => 'required|exists:student_assignment_answers,id',
            'file_upload_grade.points_earned' => 'required|numeric|min:0',
            'file_upload_grade.feedback' => 'nullable|string',
            'overall_feedback' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $activity = $assignment->activity;
            $studentActivity = StudentActivity::where('student_id', $student->id)
                ->where('activity_id', $activity->id)
                ->firstOrFail();

            $progress = StudentActivityProgress::where('student_activity_id', $studentActivity->id)
                ->where('activity_type', 'assignment')
                ->firstOrFail();

            // Update individual question grades if provided
            if (!empty($validated['question_grades'])) {
                foreach ($validated['question_grades'] as $gradeData) {
                    StudentAssignmentAnswer::where('id', $gradeData['answer_id'])
                        ->update([
                            'points_earned' => $gradeData['points_earned'],
                            'instructor_feedback' => $gradeData['feedback'] ?? null,
                        ]);
                }
            }

            // Update file upload grade if provided
            if (!empty($validated['file_upload_grade'])) {
                StudentAssignmentAnswer::where('id', $validated['file_upload_grade']['answer_id'])
                    ->update([
                        'points_earned' => $validated['file_upload_grade']['points_earned'],
                        'instructor_feedback' => $validated['file_upload_grade']['feedback'] ?? null,
                    ]);
            }

            // Calculate total score
            $totalScore = StudentAssignmentAnswer::where('student_id', $student->id)
                ->where('assignment_id', $assignment->id)
                ->sum('points_earned');

            $percentage = $assignment->total_points > 0 ? ($totalScore / $assignment->total_points) * 100 : 0;

            // Update progress with final graded scores so unified progress table stays authoritative
            $progress->update([
                'points_earned' => $totalScore,
                'score' => $totalScore,
                'max_score' => $assignment->total_points,
                'points_possible' => $assignment->total_points,
                'percentage_score' => $percentage,
                'grading_date' => now(),
                'graded_at' => now(),
                'status' => 'graded',
                'instructor_comments' => $validated['overall_feedback'] ?? $progress->instructor_comments,
            ]);

            // Update student activity
            $studentActivity->update([
                'status' => 'completed', // Mark as completed after grading
                'score' => $totalScore,
                'max_score' => $assignment->total_points,
                'percentage_score' => $percentage,
                'completed_at' => now(),
                'graded_at' => now(),
                'feedback' => $validated['overall_feedback'] ?? null,
            ]);

            // Update course progress and auto-complete modules
            $enrollment = \App\Models\CourseEnrollment::where('student_id', $student->id)
                ->where('course_id', $studentActivity->course_id)
                ->first();
            
            if ($enrollment) {
                $enrollment->updateProgress();
                $enrollment->checkAndCompleteModules();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Assignment graded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to grade assignment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to grade assignment: ' . $e->getMessage());
        }
    }

    /**
     * Return graded assignment to student (mark as reviewed)
     */
    public function returnToStudent(Assignment $assignment, Student $student)
    {
        try {
            $activity = $assignment->activity;
            $studentActivity = StudentActivity::where('student_id', $student->id)
                ->where('activity_id', $assignment->activity_id)
                ->firstOrFail();

            $progress = StudentActivityProgress::where('student_activity_id', $studentActivity->id)
                ->where('activity_type', 'assignment')
                ->firstOrFail();

            $progress->update([
                'submission_status' => 'approved',
            ]);

            return redirect()->back()->with('success', 'Assignment returned to student');
        } catch (\Exception $e) {
            Log::error('Failed to return assignment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to return assignment');
        }
    }

    /**
     * Bulk grade multiple submissions
     */
    public function bulkGrade(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'action' => 'required|in:approve_all,reset_grades',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['student_ids'] as $studentId) {
                $student = Student::find($studentId);
                $studentActivity = StudentActivity::where('student_id', $student->id)
                    ->where('activity_id', $assignment->activity_id)
                    ->first();

                if (!$studentActivity) continue;

                if ($validated['action'] === 'approve_all') {
                    // Auto-grade objective questions and approve
                    $progress = StudentActivityProgress::where('student_activity_id', $studentActivity->id)
                        ->where('activity_type', 'assignment')
                        ->first();
                    
                    if ($progress) {
                        $totalScore = $progress->auto_graded_score ?? 0;
                        $percentage = $assignment->total_points > 0 ? ($totalScore / $assignment->total_points) * 100 : 0;

                        $studentActivity->update([
                            'status' => 'graded',
                            'score' => $totalScore,
                            'percentage_score' => $percentage,
                            'graded_at' => now(),
                        ]);

                        $progress->update([
                            'submission_status' => 'approved',
                            'points_earned' => $totalScore,
                            'grading_date' => now(),
                        ]);
                    }
                } elseif ($validated['action'] === 'reset_grades') {
                    $studentActivity->update([
                        'status' => 'submitted',
                        'score' => null,
                        'percentage_score' => null,
                        'graded_at' => null,
                        'feedback' => null,
                    ]);
                }
            }

            DB::commit();

            $message = $validated['action'] === 'approve_all' 
                ? 'Assignments approved successfully' 
                : 'Grades reset successfully';

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to bulk grade: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process bulk action');
        }
    }
}
