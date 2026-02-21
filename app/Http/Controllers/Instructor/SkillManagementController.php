<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SkillManagementController extends Controller
{
    /**
     * List skills for a given module (instructor/admin only).
     */
    public function index(Module $module): JsonResponse
    {
        $this->authorizeForModule($module);

        $skills = $module->skills()
            ->orderBy('id')
            ->get()
            ->map(function (Skill $skill) {
                return [
                    'id' => $skill->id,
                    'module_id' => $skill->module_id,
                    'name' => $skill->name,
                    'description' => $skill->description,
                    'difficulty_level' => $skill->difficulty_level,
                    'weight' => (float) $skill->weight,
                    'competency_threshold' => (float) $skill->competency_threshold,
                    'bloom_level' => $skill->bloom_level,
                    'tags' => $skill->tags ?? [],
                ];
            });

        return response()->json([
            'skills' => $skills,
        ]);
    }

    /**
     * Create a new skill for a module.
     */
    public function store(Request $request, Module $module): JsonResponse
    {
        $this->authorizeForModule($module);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty_level' => 'required|string|in:basic,intermediate,advanced,expert',
            'weight' => 'nullable|numeric|min:0|max:100',
            'competency_threshold' => 'nullable|numeric|min:0|max:100',
            'bloom_level' => 'nullable|string|in:remember,understand,apply,analyze,evaluate,create',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        $skill = Skill::create(array_merge($data, [
            'module_id' => $module->id,
        ]));

        return response()->json([
            'message' => 'Skill created successfully.',
            'skill' => $skill,
        ], 201);
    }

    /**
     * Update an existing skill.
     */
    public function update(Request $request, Skill $skill): JsonResponse
    {
        $this->authorizeForModule($skill->module);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'difficulty_level' => 'sometimes|required|string|in:basic,intermediate,advanced,expert',
            'weight' => 'nullable|numeric|min:0|max:100',
            'competency_threshold' => 'nullable|numeric|min:0|max:100',
            'bloom_level' => 'nullable|string|in:remember,understand,apply,analyze,evaluate,create',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        $skill->update($data);

        return response()->json([
            'message' => 'Skill updated successfully.',
            'skill' => $skill->fresh(),
        ]);
    }

    /**
     * Delete a skill.
     */
    public function destroy(Skill $skill): JsonResponse
    {
        $this->authorizeForModule($skill->module);

        $skill->delete();

        return response()->json([
            'message' => 'Skill deleted successfully.',
        ]);
    }

    /**
     * Ensure the authenticated user can manage the given module.
     */
    protected function authorizeForModule(Module $module): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        // Admins can manage any module
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return;
        }

        $instructor = $user->instructor;

        if (!$instructor || $module->course_id !== $instructor->courses()->where('id', $module->course_id)->value('id')) {
            // Fallback strict check: course must belong to instructor
            if ($module->course->instructor_id !== ($instructor->id ?? null)) {
                abort(403, 'You are not allowed to manage skills for this module.');
            }
        }
    }
}
