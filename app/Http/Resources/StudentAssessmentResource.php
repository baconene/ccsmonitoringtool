<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentAssessmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'student_id' => $this['student_id'],
            'student_name' => $this['student_name'],
            'overall_score' => (float) $this['overall_score'],
            'readiness_level' => $this['readiness_level'],
            'assessment_date' => $this['assessment_date'],
            'courses' => $this->transformCourses($this['courses']),
            'strengths' => $this['strengths'],
            'weaknesses' => $this['weaknesses'],
            'radar_chart' => $this['radar_chart'],
            'summary' => [
                'total_courses' => count($this['courses']),
                'strengths_count' => count($this['strengths']),
                'weaknesses_count' => count($this['weaknesses']),
                'average_skill_score' => $this->calculateAverageSkillScore(),
            ],
        ];
    }

    /**
     * Transform courses array
     */
    private function transformCourses(array $courses): array
    {
        return array_map(fn($course) => [
            'course_id' => $course['course_id'],
            'course_name' => $course['course_name'],
            'score' => (float) $course['score'],
            'modules' => array_map(fn($module) => [
                'module_id' => $module['module_id'],
                'module_name' => $module['module_name'],
                'score' => (float) $module['score'],
                'skills' => array_map(fn($skill) => [
                    'skill_id' => $skill['skill_id'],
                    'skill_name' => $skill['skill_name'],
                    'score' => (float) $skill['score'],
                    'mastery_level' => $skill['mastery_level'],
                ], $module['skills']),
            ], $course['modules']),
        ], $courses);
    }

    /**
     * Calculate average skill score
     */
    private function calculateAverageSkillScore(): float
    {
        $allSkores = [];

        foreach ($this['courses'] as $course) {
            foreach ($course['modules'] as $module) {
                foreach ($module['skills'] as $skill) {
                    $allSkores[] = $skill['score'];
                }
            }
        }

        if (empty($allSkores)) {
            return 0;
        }

        return round(array_sum($allSkores) / count($allSkores), 2);
    }
}
