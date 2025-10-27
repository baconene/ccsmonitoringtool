<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate StudentAssignmentProgress data
        if (Schema::hasTable('student_assignment_progress')) {
            DB::table('student_assignment_progress')->orderBy('id')->chunk(100, function ($records) {
                foreach ($records as $record) {
                    DB::table('student_activity_progress')->insert([
                        'student_activity_id' => $record->student_activity_id,
                        'activity_id' => DB::table('student_activities')
                            ->where('id', $record->student_activity_id)
                            ->value('activity_id'),
                        'student_id' => DB::table('student_activities')
                            ->where('id', $record->student_activity_id)
                            ->value('student_id'),
                        'activity_type' => 'assignment',
                        'status' => $record->submission_status ?? 'not_started',
                        'submission_content' => $record->submission_content,
                        'attachment_files' => $record->attachment_files,
                        'points_earned' => $record->points_earned,
                        'points_possible' => $record->points_possible,
                        'score' => $record->points_earned,
                        'max_score' => $record->points_possible,
                        'percentage_score' => $record->points_possible > 0 
                            ? ($record->points_earned / $record->points_possible) * 100 
                            : null,
                        'instructor_comments' => $record->instructor_comments,
                        'feedback' => $record->instructor_comments,
                        'rubric_scores' => $record->rubric_scores,
                        'due_date' => $record->due_date,
                        'submission_date' => $record->submission_date,
                        'submitted_at' => $record->submission_date,
                        'grading_date' => $record->grading_date,
                        'graded_at' => $record->grading_date,
                        'revision_count' => $record->revision_count ?? 0,
                        'total_questions' => $record->total_questions,
                        'answered_questions' => $record->answered_questions,
                        'requires_grading' => $record->requires_grading ?? false,
                        'is_submitted' => in_array($record->submission_status, ['submitted', 'approved']),
                        'assignment_data' => json_encode([
                            'auto_graded_score' => $record->auto_graded_score ?? null,
                        ]),
                        'created_at' => $record->created_at,
                        'updated_at' => $record->updated_at,
                    ]);
                }
            });
        }

        // Migrate StudentQuizProgress data
        if (Schema::hasTable('student_quiz_progress')) {
            DB::table('student_quiz_progress')->orderBy('id')->chunk(100, function ($records) {
                foreach ($records as $record) {
                    DB::table('student_activity_progress')->insert([
                        'student_id' => $record->student_id,
                        'activity_id' => $record->activity_id,
                        'activity_type' => 'quiz',
                        'status' => $record->is_completed && $record->is_submitted ? 'completed' : 
                                   ($record->started_at ? 'in_progress' : 'not_started'),
                        'started_at' => $record->started_at,
                        'last_accessed_at' => $record->last_accessed_at,
                        'completed_at' => $record->is_completed ? $record->updated_at : null,
                        'submitted_at' => $record->is_submitted ? $record->updated_at : null,
                        'is_completed' => $record->is_completed ?? false,
                        'is_submitted' => $record->is_submitted ?? false,
                        'completed_questions' => $record->completed_questions,
                        'total_questions' => $record->total_questions,
                        'score' => $record->score,
                        'percentage_score' => $record->percentage_score,
                        'time_spent' => $record->time_spent,
                        'quiz_data' => json_encode([
                            'quiz_id' => $record->quiz_id,
                        ]),
                        'created_at' => $record->created_at,
                        'updated_at' => $record->updated_at,
                    ]);
                }
            });
        }

        // Migrate StudentProjectProgress data
        if (Schema::hasTable('student_project_progress')) {
            DB::table('student_project_progress')->orderBy('id')->chunk(100, function ($records) {
                foreach ($records as $record) {
                    $deliverables = json_decode($record->deliverables, true) ?? [];
                    $completedCount = count(array_filter($deliverables, fn($d) => ($d['status'] ?? '') === 'completed'));
                    $totalCount = count($deliverables);
                    
                    DB::table('student_activity_progress')->insert([
                        'student_activity_id' => $record->student_activity_id,
                        'activity_id' => DB::table('student_activities')
                            ->where('id', $record->student_activity_id)
                            ->value('activity_id'),
                        'student_id' => DB::table('student_activities')
                            ->where('id', $record->student_activity_id)
                            ->value('student_id'),
                        'activity_type' => 'project',
                        'status' => $record->overall_progress_percentage >= 100 ? 'completed' : 
                                   ($record->overall_progress_percentage > 0 ? 'in_progress' : 'not_started'),
                        'progress_percentage' => $record->overall_progress_percentage ?? 0,
                        'current_phase' => $record->current_phase,
                        'started_at' => $record->project_start_date,
                        'due_date' => $record->project_due_date,
                        'last_accessed_at' => $record->last_activity_date,
                        'final_submission' => $record->final_submission,
                        'project_data' => json_encode([
                            'phases' => json_decode($record->project_phases, true),
                            'deliverables' => $deliverables,
                            'team_members' => json_decode($record->team_members, true),
                            'description' => $record->project_description,
                            'goals' => $record->project_goals,
                            'resource_usage' => json_decode($record->resource_usage, true),
                            'presentation_data' => json_decode($record->presentation_data, true),
                            'collaboration_type' => $record->collaboration_type,
                        ]),
                        'created_at' => $record->created_at,
                        'updated_at' => $record->updated_at,
                    ]);
                }
            });
        }

        // Migrate StudentAssessmentProgress data
        if (Schema::hasTable('student_assessment_progress')) {
            DB::table('student_assessment_progress')->orderBy('id')->chunk(100, function ($records) {
                foreach ($records as $record) {
                    DB::table('student_activity_progress')->insert([
                        'student_activity_id' => $record->student_activity_id,
                        'activity_id' => DB::table('student_activities')
                            ->where('id', $record->student_activity_id)
                            ->value('activity_id'),
                        'student_id' => DB::table('student_activities')
                            ->where('id', $record->student_activity_id)
                            ->value('student_id'),
                        'activity_type' => 'assessment',
                        'status' => in_array($record->mastery_level, ['met', 'exceeded']) ? 'completed' : 'in_progress',
                        'score' => $record->proficiency_level,
                        'percentage_score' => $record->proficiency_level,
                        'graded_at' => $record->assessment_date,
                        'assessment_data' => json_encode([
                            'assessment_type' => $record->assessment_type,
                            'criteria' => json_decode($record->assessment_criteria, true),
                            'skill_assessments' => json_decode($record->skill_assessments, true),
                            'proficiency_level' => $record->proficiency_level,
                            'competency_mapping' => json_decode($record->competency_mapping, true),
                            'self_assessment' => $record->self_assessment,
                            'peer_assessment' => $record->peer_assessment,
                            'instructor_assessment' => $record->instructor_assessment,
                            'evidence_artifacts' => json_decode($record->evidence_artifacts, true),
                            'mastery_level' => $record->mastery_level,
                            'improvement_areas' => json_decode($record->improvement_areas, true),
                            'strength_areas' => json_decode($record->strength_areas, true),
                        ]),
                        'created_at' => $record->created_at,
                        'updated_at' => $record->updated_at,
                    ]);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear all migrated data
        DB::table('student_activity_progress')->truncate();
    }
};
