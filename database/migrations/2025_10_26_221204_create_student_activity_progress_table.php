<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_activity_progress', function (Blueprint $table) {
            $table->id();
            
            // Core fields
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->string('activity_type'); // assignment, quiz, project, assessment
            $table->foreignId('student_activity_id')->nullable()->constrained('student_activities')->onDelete('cascade');
            
            // Status fields
            $table->string('status')->default('not_started'); // not_started, in_progress, submitted, graded, completed
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            
            // Scoring fields
            $table->decimal('score', 8, 2)->nullable();
            $table->decimal('max_score', 8, 2)->nullable();
            $table->decimal('percentage_score', 5, 2)->nullable();
            $table->decimal('points_earned', 8, 2)->nullable();
            $table->decimal('points_possible', 8, 2)->nullable();
            
            // Progress tracking
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->integer('answered_questions')->nullable();
            $table->integer('total_questions')->nullable();
            $table->integer('completed_questions')->nullable();
            $table->integer('current_phase')->nullable();
            
            // Content fields
            $table->longText('submission_content')->nullable();
            $table->json('attachment_files')->nullable();
            $table->longText('final_submission')->nullable();
            
            // Assessment fields
            $table->text('instructor_comments')->nullable();
            $table->text('feedback')->nullable();
            $table->json('rubric_scores')->nullable();
            
            // Type-specific JSON fields (for flexibility)
            $table->json('quiz_data')->nullable(); // quiz answers, attempts, etc
            $table->json('assignment_data')->nullable(); // submission details, revisions
            $table->json('project_data')->nullable(); // phases, deliverables, team members
            $table->json('assessment_data')->nullable(); // skills, competencies, mastery levels
            
            // Additional metadata
            $table->timestamp('due_date')->nullable();
            $table->timestamp('submission_date')->nullable();
            $table->timestamp('grading_date')->nullable();
            $table->integer('time_spent')->nullable(); // in seconds
            $table->integer('revision_count')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_submitted')->default(false);
            $table->boolean('requires_grading')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['student_id', 'activity_id']);
            $table->index(['activity_id', 'activity_type']);
            $table->index('status');
            $table->index('submitted_at');
            $table->index('graded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_activity_progress');
    }
};
