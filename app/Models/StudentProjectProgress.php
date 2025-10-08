<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class StudentProjectProgress extends Model
{
    protected $fillable = [
        'student_activity_id',
        'project_phases',
        'current_phase',
        'overall_progress_percentage',
        'deliverables',
        'team_members',
        'project_description',
        'project_goals',
        'project_start_date',
        'project_due_date',
        'last_activity_date',
        'resource_usage',
        'final_submission',
        'presentation_data',
        'collaboration_type',
    ];

    protected $casts = [
        'project_phases' => 'array',
        'deliverables' => 'array',
        'team_members' => 'array',
        'project_start_date' => 'datetime',
        'project_due_date' => 'datetime',
        'last_activity_date' => 'datetime',
        'resource_usage' => 'array',
        'presentation_data' => 'array',
        'overall_progress_percentage' => 'decimal:2',
    ];

    // Relationships
    public function studentActivity(): BelongsTo
    {
        return $this->belongsTo(StudentActivity::class);
    }

    // Helper methods
    public function updateProgress(int $phase = null, float $percentage = null): void
    {
        $data = ['last_activity_date' => Carbon::now()];

        if ($phase !== null) {
            $data['current_phase'] = $phase;
        }

        if ($percentage !== null) {
            $data['overall_progress_percentage'] = min(100, max(0, $percentage));
        }

        $this->update($data);

        // Update parent activity status
        if ($percentage >= 100) {
            $this->studentActivity->markAsCompleted();
        } elseif ($percentage > 0) {
            $this->studentActivity->markAsStarted();
        }
    }

    public function addDeliverable(array $deliverable): void
    {
        $deliverables = $this->deliverables ?? [];
        $deliverables[] = array_merge($deliverable, [
            'created_at' => Carbon::now()->toISOString(),
            'id' => uniqid(),
        ]);

        $this->update(['deliverables' => $deliverables]);
    }

    public function completeDeliverable(string $deliverableId): void
    {
        $deliverables = $this->deliverables ?? [];
        
        foreach ($deliverables as &$deliverable) {
            if ($deliverable['id'] === $deliverableId) {
                $deliverable['completed_at'] = Carbon::now()->toISOString();
                $deliverable['status'] = 'completed';
                break;
            }
        }

        $this->update(['deliverables' => $deliverables]);
        $this->recalculateProgress();
    }

    public function addTeamMember(array $member): void
    {
        $members = $this->team_members ?? [];
        $members[] = $member;
        $this->update(['team_members' => $members]);
    }

    public function removeTeamMember(string $memberId): void
    {
        $members = $this->team_members ?? [];
        $members = array_filter($members, fn($member) => $member['id'] !== $memberId);
        $this->update(['team_members' => array_values($members)]);
    }

    public function isOverdue(): bool
    {
        return $this->project_due_date && 
               $this->project_due_date < Carbon::now() && 
               $this->overall_progress_percentage < 100;
    }

    public function isGroupProject(): bool
    {
        return in_array($this->collaboration_type, ['pair', 'group']);
    }

    public function getCompletedDeliverablesCount(): int
    {
        if (!$this->deliverables) {
            return 0;
        }

        return count(array_filter($this->deliverables, 
            fn($deliverable) => isset($deliverable['status']) && $deliverable['status'] === 'completed'
        ));
    }

    public function getTotalDeliverablesCount(): int
    {
        return count($this->deliverables ?? []);
    }

    public function recalculateProgress(): void
    {
        $totalDeliverables = $this->getTotalDeliverablesCount();
        
        if ($totalDeliverables === 0) {
            return;
        }

        $completedDeliverables = $this->getCompletedDeliverablesCount();
        $percentage = ($completedDeliverables / $totalDeliverables) * 100;

        $this->updateProgress(null, $percentage);
    }

    public function submitFinalProject(string $submission): void
    {
        $this->update([
            'final_submission' => $submission,
            'overall_progress_percentage' => 100,
        ]);

        $this->studentActivity->update([
            'status' => 'submitted',
            'submitted_at' => Carbon::now(),
        ]);
    }
}
