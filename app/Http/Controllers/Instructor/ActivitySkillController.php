<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ActivitySkillController extends Controller
{
    /**
     * List skills attached to an activity and available skills from its modules.
     */
    public function index(Activity $activity): JsonResponse
    {
        $this->authorizeForActivity($activity);

        $activity->load(['skills', 'modules.skills']);

        $attached = $activity->skills->map(function (Skill $skill) {
            return [
                'id' => $skill->id,
                'name' => $skill->name,
                'module_id' => $skill->module_id,
                'module_title' => optional($skill->module)->title ?? optional($skill->module)->description,
                'difficulty_level' => $skill->difficulty_level,
                'weight' => (float) $skill->pivot->weight,
            ];
        });

        $available = $activity->modules
            ->flatMap(fn ($module) => $module->skills)
            ->unique('id')
            ->values()
            ->map(function (Skill $skill) {
                return [
                    'id' => $skill->id,
                    'name' => $skill->name,
                    'module_id' => $skill->module_id,
                    'module_title' => optional($skill->module)->title ?? optional($skill->module)->description,
                    'difficulty_level' => $skill->difficulty_level,
                ];
            });

        return response()->json([
            'attached' => $attached,
            'available' => $available,
        ]);
    }

    /**
     * Attach or update a skill for an activity.
     */
    public function store(Request $request, Activity $activity): JsonResponse
    {
        $this->authorizeForActivity($activity);

        $data = $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'weight' => 'nullable|numeric|min:0|max:100',
        ]);

        $skill = Skill::findOrFail($data['skill_id']);

        // Ensure the skill belongs to one of the activity's modules
        if (!$activity->modules()->where('modules.id', $skill->module_id)->exists()) {
            abort(422, 'Skill must belong to a module that includes this activity.');
        }

        $weight = $data['weight'] ?? 1.0;

        $activity->skills()->syncWithoutDetaching([
            $skill->id => ['weight' => $weight],
        ]);

        return $this->index($activity);
    }

    /**
     * Update pivot weight for a skill attached to an activity.
     */
    public function update(Request $request, Activity $activity, Skill $skill): JsonResponse
    {
        $this->authorizeForActivity($activity);

        $data = $request->validate([
            'weight' => 'required|numeric|min:0|max:100',
        ]);

        if (!$activity->skills()->where('skills.id', $skill->id)->exists()) {
            abort(404, 'Skill is not attached to this activity.');
        }

        $activity->skills()->updateExistingPivot($skill->id, ['weight' => $data['weight']]);

        return $this->index($activity);
    }

    /**
     * Detach a skill from an activity.
     */
    public function destroy(Activity $activity, Skill $skill): JsonResponse
    {
        $this->authorizeForActivity($activity);

        $activity->skills()->detach($skill->id);

        return $this->index($activity);
    }

    /**
     * Ensure the authenticated user can manage the given activity.
     */
    protected function authorizeForActivity(Activity $activity): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return;
        }

        $instructor = $user->instructor;

        // Activity may belong to multiple modules/courses; ensure at least one
        // of them is owned by this instructor.
        $ownsAnyCourse = $activity->modules()
            ->whereHas('course', function ($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id ?? null);
            })
            ->exists();

        if (!$ownsAnyCourse) {
            abort(403, 'You are not allowed to manage skills for this activity.');
        }
    }
}
