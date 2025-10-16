<?php

namespace App\Http\Controllers;

use App\Models\GradeSetting;
use App\Models\CourseGradeSetting;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class GradeSettingsController extends Controller
{
    /**
     * Display the grade settings page
     */
    public function index(Request $request)
    {
        $courseId = $request->query('course_id');
        $course = null;
        $moduleComponents = [];
        $activityTypes = [];
        $hasCustomSettings = false;

        if ($courseId) {
            $course = Course::with('instructor')->find($courseId);
            
            if ($course) {
                // Check if course has custom settings
                $hasCustomSettings = CourseGradeSetting::courseHasCustomSettings($courseId);
                
                if ($hasCustomSettings) {
                    // Get course-specific settings
                    $moduleComponents = CourseGradeSetting::forCourse($courseId)
                        ->moduleComponents()
                        ->get();
                    $activityTypes = CourseGradeSetting::forCourse($courseId)
                        ->activityTypes()
                        ->get();
                } else {
                    // Get global settings as template
                    $globalModuleComponents = GradeSetting::moduleComponents()->get();
                    $globalActivityTypes = GradeSetting::activityTypes()->get();
                    
                    // Transform to course settings format (not saved yet)
                    $moduleComponents = $globalModuleComponents->map(function($setting) use ($courseId) {
                        return [
                            'id' => null, // Not saved yet
                            'course_id' => $courseId,
                            'setting_type' => $setting->setting_type,
                            'setting_key' => $setting->setting_key,
                            'display_name' => $setting->display_name,
                            'weight_percentage' => $setting->weight_percentage,
                            'is_active' => true,
                        ];
                    });
                    
                    $activityTypes = $globalActivityTypes->map(function($setting) use ($courseId) {
                        return [
                            'id' => null, // Not saved yet
                            'course_id' => $courseId,
                            'setting_type' => $setting->setting_type,
                            'setting_key' => $setting->setting_key,
                            'display_name' => $setting->display_name,
                            'weight_percentage' => $setting->weight_percentage,
                            'is_active' => true,
                        ];
                    });
                }
            }
        } else {
            // Show global settings
            $moduleComponents = GradeSetting::moduleComponents()->get();
            $activityTypes = GradeSetting::activityTypes()->get();
        }

        // Get list of courses for search (instructor's courses or all for admin)
        $user = Auth::user();
        if ($user->role === 'admin' || $user->role_name === 'admin') {
            $courses = Course::with('instructor')->orderBy('name')->get();
        } else {
            // Get instructor's courses
            $courses = Course::where('instructor_id', $user->id)
                ->orWhere('created_by', $user->id)
                ->with('instructor')
                ->orderBy('name')
                ->get();
        }

        return Inertia::render('Admin/GradeSettings', [
            'moduleComponents' => $moduleComponents,
            'activityTypes' => $activityTypes,
            'courses' => $courses,
            'selectedCourse' => $course,
            'hasCustomSettings' => $hasCustomSettings,
            'isGlobalSettings' => !$courseId,
        ]);
    }

    /**
     * Update module component weights (global or course-specific)
     */
    public function updateModuleComponents(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'nullable|exists:courses,id',
            'lessons' => 'required|numeric|min:0|max:100',
            'activities' => 'required|numeric|min:0|max:100',
        ]);

        // Validate total equals 100%
        $total = $validated['lessons'] + $validated['activities'];
        if (abs($total - 100) > 0.01) {
            return back()->with('error', 'Module component weights must total 100%');
        }

        $courseId = $validated['course_id'] ?? null;
        $userId = Auth::id();

        if ($courseId) {
            // Update course-specific settings
            CourseGradeSetting::updateOrCreate(
                ['course_id' => $courseId, 'setting_type' => 'module_component', 'setting_key' => 'lessons'],
                [
                    'display_name' => 'Lessons',
                    'weight_percentage' => $validated['lessons'],
                    'is_active' => true,
                    'updated_by' => $userId,
                    'created_by' => $userId,
                ]
            );

            CourseGradeSetting::updateOrCreate(
                ['course_id' => $courseId, 'setting_type' => 'module_component', 'setting_key' => 'activities'],
                [
                    'display_name' => 'Activities',
                    'weight_percentage' => $validated['activities'],
                    'is_active' => true,
                    'updated_by' => $userId,
                    'created_by' => $userId,
                ]
            );

            return back()->with('success', 'Course-specific module component weights updated successfully');
        } else {
            // Update global settings
            GradeSetting::where('setting_type', 'module_component')
                ->where('setting_key', 'lessons')
                ->update([
                    'weight_percentage' => $validated['lessons'],
                    'updated_by' => $userId,
                ]);

            GradeSetting::where('setting_type', 'module_component')
                ->where('setting_key', 'activities')
                ->update([
                    'weight_percentage' => $validated['activities'],
                    'updated_by' => $userId,
                ]);

            return back()->with('success', 'Global module component weights updated successfully');
        }
    }

    /**
     * Update activity type weights (global or course-specific)
     */
    public function updateActivityTypes(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'nullable|exists:courses,id',
            'Quiz' => 'required|numeric|min:0|max:100',
            'Assignment' => 'required|numeric|min:0|max:100',
            'Assessment' => 'required|numeric|min:0|max:100',
            'Exercise' => 'required|numeric|min:0|max:100',
        ]);

        // Validate total equals 100%
        $total = $validated['Quiz'] + $validated['Assignment'] + $validated['Assessment'] + $validated['Exercise'];
        if (abs($total - 100) > 0.01) {
            return back()->with('error', 'Activity type weights must total 100%');
        }

        $courseId = $validated['course_id'] ?? null;
        $userId = Auth::id();

        $activityTypes = [
            'Quiz' => $validated['Quiz'],
            'Assignment' => $validated['Assignment'],
            'Assessment' => $validated['Assessment'],
            'Exercise' => $validated['Exercise'],
        ];

        if ($courseId) {
            // Update course-specific settings
            foreach ($activityTypes as $key => $weight) {
                CourseGradeSetting::updateOrCreate(
                    ['course_id' => $courseId, 'setting_type' => 'activity_type', 'setting_key' => $key],
                    [
                        'display_name' => $key,
                        'weight_percentage' => $weight,
                        'is_active' => true,
                        'updated_by' => $userId,
                        'created_by' => $userId,
                    ]
                );
            }

            return back()->with('success', 'Course-specific activity type weights updated successfully');
        } else {
            // Update global settings
            foreach ($activityTypes as $key => $weight) {
                GradeSetting::where('setting_type', 'activity_type')
                    ->where('setting_key', $key)
                    ->update([
                        'weight_percentage' => $weight,
                        'updated_by' => $userId,
                    ]);
            }

            return back()->with('success', 'Global activity type weights updated successfully');
        }
    }

    /**
     * Reset to defaults (global or copy global to course)
     */
    public function reset(Request $request)
    {
        $courseId = $request->input('course_id');
        $userId = Auth::id();

        if ($courseId) {
            // Copy global settings to course
            CourseGradeSetting::copyGlobalSettingsToCourse($courseId, $userId);
            return back()->with('success', 'Course settings reset to global defaults');
        } else {
            // Reset global settings to hardcoded defaults
            $defaults = [
                'module_component' => [
                    'lessons' => 20,
                    'activities' => 80,
                ],
                'activity_type' => [
                    'Quiz' => 30,
                    'Assignment' => 15,
                    'Assessment' => 35,
                    'Exercise' => 20,
                ],
            ];

            foreach ($defaults['module_component'] as $key => $weight) {
                GradeSetting::where('setting_type', 'module_component')
                    ->where('setting_key', $key)
                    ->update(['weight_percentage' => $weight, 'updated_by' => $userId]);
            }

            foreach ($defaults['activity_type'] as $key => $weight) {
                GradeSetting::where('setting_type', 'activity_type')
                    ->where('setting_key', $key)
                    ->update(['weight_percentage' => $weight, 'updated_by' => $userId]);
            }

            return back()->with('success', 'Global settings reset to factory defaults');
        }
    }

    /**
     * Delete course-specific settings (revert to global)
     */
    public function deleteCourseSettings(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        CourseGradeSetting::forCourse($validated['course_id'])->delete();

        return back()->with('success', 'Course-specific settings deleted. Now using global settings.');
    }
}
