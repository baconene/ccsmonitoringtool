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
        Schema::create('student_project_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_activity_id')->constrained()->onDelete('cascade');
            $table->json('project_phases')->nullable(); // Store project milestones/phases
            $table->integer('current_phase')->default(1);
            $table->decimal('overall_progress_percentage', 5, 2)->default(0);
            $table->json('deliverables')->nullable(); // Store deliverable tracking
            $table->json('team_members')->nullable(); // If it's a group project
            $table->text('project_description')->nullable();
            $table->text('project_goals')->nullable();
            $table->timestamp('project_start_date')->nullable();
            $table->timestamp('project_due_date')->nullable();
            $table->timestamp('last_activity_date')->nullable();
            $table->json('resource_usage')->nullable(); // Track resources used
            $table->text('final_submission')->nullable();
            $table->json('presentation_data')->nullable(); // For project presentations
            $table->enum('collaboration_type', ['individual', 'pair', 'group'])->default('individual');
            $table->timestamps();

            $table->index(['current_phase', 'overall_progress_percentage']);
            $table->index(['project_due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_project_progress');
    }
};
