<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class StudentAssessmentProgress extends Model
{
    protected $fillable = [
        'student_activity_id',
        'assessment_type',
        'assessment_criteria',
        'skill_assessments',
        'proficiency_level',
        'competency_mapping',
        'self_assessment',
        'peer_assessment',
        'instructor_assessment',
        'evidence_artifacts',
        'mastery_level',
        'assessment_date',
        'improvement_areas',
        'strength_areas',
    ];

    protected $casts = [
        'assessment_criteria' => 'array',
        'skill_assessments' => 'array',
        'competency_mapping' => 'array',
        'evidence_artifacts' => 'array',
        'assessment_date' => 'datetime',
        'improvement_areas' => 'array',
        'strength_areas' => 'array',
        'proficiency_level' => 'decimal:2',
    ];

    // Relationships
    public function studentActivity(): BelongsTo
    {
        return $this->belongsTo(StudentActivity::class);
    }

    // Helper methods
    public function recordAssessment(array $assessmentData): void
    {
        $this->update(array_merge($assessmentData, [
            'assessment_date' => Carbon::now(),
        ]));

        // Update parent activity based on mastery level
        $this->updateParentActivityStatus();
    }

    public function updateMasteryLevel(string $level): void
    {
        $this->update([
            'mastery_level' => $level,
            'assessment_date' => Carbon::now(),
        ]);

        $this->updateParentActivityStatus();
    }

    public function addSkillAssessment(string $skill, float $score, string $notes = null): void
    {
        $skills = $this->skill_assessments ?? [];
        
        $skills[$skill] = [
            'score' => $score,
            'notes' => $notes,
            'assessed_at' => Carbon::now()->toISOString(),
        ];

        $this->update(['skill_assessments' => $skills]);
        $this->recalculateProficiencyLevel();
    }

    public function addEvidenceArtifact(array $artifact): void
    {
        $artifacts = $this->evidence_artifacts ?? [];
        $artifacts[] = array_merge($artifact, [
            'uploaded_at' => Carbon::now()->toISOString(),
            'id' => uniqid(),
        ]);

        $this->update(['evidence_artifacts' => $artifacts]);
    }

    public function setImprovementAreas(array $areas): void
    {
        $this->update(['improvement_areas' => $areas]);
    }

    public function setStrengthAreas(array $areas): void
    {
        $this->update(['strength_areas' => $areas]);
    }

    public function isFormativeAssessment(): bool
    {
        return $this->assessment_type === 'formative';
    }

    public function isSummativeAssessment(): bool
    {
        return $this->assessment_type === 'summative';
    }

    public function hasMasteredSkills(): bool
    {
        return in_array($this->mastery_level, ['met', 'exceeded']);
    }

    public function needsImprovement(): bool
    {
        return in_array($this->mastery_level, ['not_met', 'approaching']);
    }

    protected function recalculateProficiencyLevel(): void
    {
        if (!$this->skill_assessments) {
            return;
        }

        $scores = array_column($this->skill_assessments, 'score');
        $averageScore = array_sum($scores) / count($scores);

        $this->update(['proficiency_level' => $averageScore]);
    }

    protected function updateParentActivityStatus(): void
    {
        $status = match($this->mastery_level) {
            'met', 'exceeded' => 'completed',
            'approaching' => 'in_progress',
            'not_met' => 'in_progress',
            default => $this->studentActivity->status,
        };

        $this->studentActivity->update([
            'status' => $status,
            'score' => $this->proficiency_level,
            'percentage_score' => $this->proficiency_level,
        ]);

        if ($status === 'completed') {
            $this->studentActivity->update([
                'completed_at' => Carbon::now(),
            ]);
        }
    }

    public function getOverallGrade(): string
    {
        return match($this->mastery_level) {
            'exceeded' => 'A',
            'met' => 'B', 
            'approaching' => 'C',
            'not_met' => 'D',
            default => 'Not Assessed',
        };
    }
}
