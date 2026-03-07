<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Models\Student;
use App\Models\StudentAssessmentDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentAssessmentDetailController extends Controller
{
    public function indexForCurrentStudent(): JsonResponse
    {
        $student = Auth::user()?->student;

        if (!$student) {
            return response()->json([
                'assessments' => [],
                'message' => 'Student profile not found.',
            ]);
        }

        $assessments = StudentAssessmentDetail::with(['course:id,title,name', 'assessedBy:id,name,email'])
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        $assessments->each(function (StudentAssessmentDetail $assessment): void {
            $assessment->setAttribute('assessed_by', $assessment->assessedBy);
        });

        return response()->json([
            'assessments' => $assessments,
        ]);
    }

    public function index(Student $student): JsonResponse
    {
        $assessments = StudentAssessmentDetail::with(['course:id,title,name', 'assessedBy:id,name,email'])
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        $assessments->each(function (StudentAssessmentDetail $assessment): void {
            $assessment->setAttribute('assessed_by', $assessment->assessedBy);
        });

        return response()->json([
            'assessments' => $assessments,
        ]);
    }

    public function store(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'description' => ['required', 'string', 'max:5000'],
            'course_mostyle' => ['nullable', 'string', 'max:255'],
        ]);

        $isEnrolled = CourseEnrollment::where('student_id', $student->id)
            ->where('course_id', $validated['course_id'])
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'message' => 'Selected course is not enrolled by this student.',
            ], 422);
        }

        $assessment = StudentAssessmentDetail::create([
            'student_id' => $student->id,
            'course_id' => $validated['course_id'],
            'assessed_by_user_id' => Auth::id(),
            'description' => $validated['description'],
            'course_mostyle' => $validated['course_mostyle'] ?? null,
        ])->load(['course:id,title,name', 'assessedBy:id,name,email']);

        $assessment->setAttribute('assessed_by', $assessment->assessedBy);

        return response()->json([
            'message' => 'Assessment detail created successfully.',
            'assessment' => $assessment,
        ], 201);
    }

    public function update(Request $request, Student $student, StudentAssessmentDetail $assessment): JsonResponse
    {
        if ($assessment->student_id !== $student->id) {
            return response()->json([
                'message' => 'Assessment record does not belong to this student.',
            ], 404);
        }

        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'description' => ['required', 'string', 'max:5000'],
            'course_mostyle' => ['nullable', 'string', 'max:255'],
        ]);

        $isEnrolled = CourseEnrollment::where('student_id', $student->id)
            ->where('course_id', $validated['course_id'])
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'message' => 'Selected course is not enrolled by this student.',
            ], 422);
        }

        $assessment->update($validated);

        $updatedAssessment = $assessment->fresh()->load(['course:id,title,name', 'assessedBy:id,name,email']);
        $updatedAssessment->setAttribute('assessed_by', $updatedAssessment->assessedBy);

        return response()->json([
            'message' => 'Assessment detail updated successfully.',
            'assessment' => $updatedAssessment,
        ]);
    }

    public function destroy(Student $student, StudentAssessmentDetail $assessment): JsonResponse
    {
        if ($assessment->student_id !== $student->id) {
            return response()->json([
                'message' => 'Assessment record does not belong to this student.',
            ], 404);
        }

        $assessment->delete();

        return response()->json([
            'message' => 'Assessment detail deleted successfully.',
        ]);
    }
}
