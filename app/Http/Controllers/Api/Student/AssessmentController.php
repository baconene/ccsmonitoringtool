<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentAssessmentResource;
use App\Http\Resources\StudentSkillAssessmentResource;
use App\Models\Student;
use App\Models\Skill;
use App\Services\StudentAssessmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    protected StudentAssessmentService $assessmentService;

    public function __construct(StudentAssessmentService $assessmentService)
    {
        $this->assessmentService = $assessmentService;
    }

    /**
     * Get comprehensive assessment for authenticated student
     * GET /api/student/assessment
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $student = Student::where('user_id', $user->id)->firstOrFail();

            // Calculate assessment
            $assessment = $this->assessmentService->calculateStudentAssessment($student);

            return response()->json(
                new StudentAssessmentResource($assessment),
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve assessment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get assessment for a specific student (admin/instructor only)
     * GET /api/student/{studentId}/assessment
     */
    public function getStudentAssessment(string $studentId): JsonResponse
    {
        try {
            $student = Student::where('id', $studentId)->firstOrFail();

            // Calculate assessment
            $assessment = $this->assessmentService->calculateStudentAssessment($student);

            return response()->json(
                new StudentAssessmentResource($assessment),
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Student not found or assessment failed',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Get skill assessments for authenticated student
     * GET /api/student/skills/assessments
     */
    public function getSkillAssessments(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $student = Student::where('user_id', $user->id)->firstOrFail();

            $assessments = $student->skillAssessments()
                ->with('skill')
                ->orderByDesc('final_score')
                ->get();

            return response()->json([
                'data' => StudentSkillAssessmentResource::collection($assessments),
                'count' => $assessments->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve skill assessments',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get strength areas for authenticated student
     * GET /api/student/strengths
     */
    public function getStrengths(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $student = Student::where('user_id', $user->id)->firstOrFail();

            $assessment = $this->assessmentService->calculateStudentAssessment($student);

            return response()->json([
                'data' => $assessment['strengths'],
                'count' => count($assessment['strengths']),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve strengths',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get weakness areas for authenticated student
     * GET /api/student/weaknesses
     */
    public function getWeaknesses(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $student = Student::where('user_id', $user->id)->firstOrFail();

            $assessment = $this->assessmentService->calculateStudentAssessment($student);

            return response()->json([
                'data' => $assessment['weaknesses'],
                'count' => count($assessment['weaknesses']),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve weaknesses',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get radar chart data for authenticated student
     * GET /api/student/assessment/radar
     */
    public function getRadarData(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $student = Student::where('user_id', $user->id)->firstOrFail();

            $assessment = $this->assessmentService->calculateStudentAssessment($student);

            return response()->json([
                'data' => $assessment['radar_chart'],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve radar data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Recalculate and update all student assessments for a course
     * POST /api/admin/course/{courseId}/recalculate-assessments
     */
    public function recalculateCourseAssessments(string $courseId): JsonResponse
    {
        try {
            $course = \App\Models\Course::findOrFail($courseId);
            $students = $course->students()->get();

            $updated = 0;
            foreach ($students as $student) {
                $this->assessmentService->calculateStudentAssessment($student);
                $updated++;
            }

            return response()->json([
                'message' => "Recalculated assessments for {$updated} students",
                'count' => $updated,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to recalculate assessments',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get assessment comparison between multiple students
     * POST /api/admin/assessment/compare
     */
    public function compareAssessments(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'student_ids' => 'required|array|min:2',
                'student_ids.*' => 'integer',
            ]);

            $studentIds = $request->input('student_ids');
            $assessments = [];

            foreach ($studentIds as $studentId) {
                $student = Student::find($studentId);
                if ($student) {
                    $assessment = $this->assessmentService->calculateStudentAssessment($student);
                    $assessments[] = $assessment;
                }
            }

            return response()->json([
                'data' => $assessments,
                'count' => count($assessments),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to compare assessments',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
