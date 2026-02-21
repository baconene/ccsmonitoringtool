<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentSkillAssessmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'skill_id' => $this->skill_id,
            'skill_name' => $this->skill->name ?? null,
            'normalized_score' => (float) $this->normalized_score,
            'final_score' => (float) $this->final_score,
            'mastery_level' => $this->mastery_level,
            'status' => $this->getStatusAttribute(),
            'consistency_score' => (float) $this->consistency_score,
            'attempt_count' => $this->attempt_count,
            'improvement_factor' => (float) $this->improvement_factor,
            'days_late' => $this->days_late,
        ];
    }

    private function getStatusAttribute(): string
    {
        return match ($this->mastery_level) {
            'not_met' => 'Not Met',
            'met' => 'Met',
            'exceeds' => 'Exceeds Expectations',
            default => 'Unknown',
        };
    }
}
