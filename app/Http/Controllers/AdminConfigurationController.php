<?php

namespace App\Http\Controllers;

use App\Models\GradeLevel;
use App\Models\ActivityType;
use App\Models\QuestionType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class AdminConfigurationController extends Controller
{
    /**
     * Display the admin configuration page
     */
    public function index()
    {
        $gradeLevels = GradeLevel::orderBy('level')->get();
        $activityTypes = ActivityType::orderBy('name')->get();
        $questionTypes = QuestionType::orderBy('type')->get();

        return Inertia::render('Admin/AdminConfiguration', [
            'gradeLevels' => $gradeLevels,
            'activityTypes' => $activityTypes,
            'questionTypes' => $questionTypes,
        ]);
    }

    // ===================== GRADE LEVELS =====================

    /**
     * Store a new grade level
     */
    public function storeGradeLevel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:grade_levels',
            'display_name' => 'required|string|max:255',
            'level' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {
            GradeLevel::create($validator->validated());
            return back()->with('success', 'Grade level created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create grade level: ' . $e->getMessage()]);
        }
    }

    /**
     * Update a grade level
     */
    public function updateGradeLevel(Request $request, GradeLevel $gradeLevel)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:grade_levels,name,' . $gradeLevel->id,
            'display_name' => 'required|string|max:255',
            'level' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {
            $gradeLevel->update($validator->validated());
            return back()->with('success', 'Grade level updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update grade level: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a grade level
     */
    public function destroyGradeLevel(GradeLevel $gradeLevel)
    {
        try {
            // Check if grade level is used in courses
            if ($gradeLevel->courses()->exists()) {
                return back()->withErrors(['error' => 'Cannot delete grade level that is assigned to courses.']);
            }

            $gradeLevel->delete();
            return back()->with('success', 'Grade level deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete grade level: ' . $e->getMessage()]);
        }
    }

    // ===================== ACTIVITY TYPES =====================

    /**
     * Store a new activity type
     */
    public function storeActivityType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:activity_types',
            'description' => 'nullable|string|max:1000',
            'model' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {
            ActivityType::create($validator->validated());
            return back()->with('success', 'Activity type created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create activity type: ' . $e->getMessage()]);
        }
    }

    /**
     * Update an activity type
     */
    public function updateActivityType(Request $request, ActivityType $activityType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:activity_types,name,' . $activityType->id,
            'description' => 'nullable|string|max:1000',
            'model' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {
            $activityType->update($validator->validated());
            return back()->with('success', 'Activity type updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update activity type: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete an activity type
     */
    public function destroyActivityType(ActivityType $activityType)
    {
        try {
            // Check if activity type is used in activities
            if ($activityType->activities()->exists()) {
                return back()->withErrors(['error' => 'Cannot delete activity type that is used in activities.']);
            }

            $activityType->delete();
            return back()->with('success', 'Activity type deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete activity type: ' . $e->getMessage()]);
        }
    }

    // ===================== QUESTION TYPES =====================

    /**
     * Store a new question type
     */
    public function storeQuestionType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255|unique:question_types',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {
            QuestionType::create($validator->validated());
            return back()->with('success', 'Question type created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create question type: ' . $e->getMessage()]);
        }
    }

    /**
     * Update a question type
     */
    public function updateQuestionType(Request $request, QuestionType $questionType)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255|unique:question_types,type,' . $questionType->id,
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {
            $questionType->update($validator->validated());
            return back()->with('success', 'Question type updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update question type: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a question type
     */
    public function destroyQuestionType(QuestionType $questionType)
    {
        try {
            $questionType->delete();
            return back()->with('success', 'Question type deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete question type: ' . $e->getMessage()]);
        }
    }
}
