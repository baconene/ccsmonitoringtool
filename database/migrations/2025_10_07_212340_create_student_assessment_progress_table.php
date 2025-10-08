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
        Schema::create('student_assessment_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_activity_id')->constrained()->onDelete('cascade');
            $table->enum('assessment_type', ['formative', 'summative', 'diagnostic', 'benchmark'])->default('formative');
            $table->json('assessment_criteria')->nullable(); // Store assessment rubrics/criteria
            $table->json('skill_assessments')->nullable(); // Track individual skill evaluations
            $table->decimal('proficiency_level', 3, 2)->nullable(); // 0-100 or custom scale
            $table->json('competency_mapping')->nullable(); // Map to learning competencies
            $table->text('self_assessment')->nullable(); // Student self-evaluation
            $table->text('peer_assessment')->nullable(); // Peer evaluation data
            $table->text('instructor_assessment')->nullable(); // Instructor evaluation
            $table->json('evidence_artifacts')->nullable(); // Links to work samples
            $table->enum('mastery_level', ['not_met', 'approaching', 'met', 'exceeded'])->nullable();
            $table->timestamp('assessment_date')->nullable();
            $table->json('improvement_areas')->nullable(); // Areas for growth
            $table->json('strength_areas')->nullable(); // Areas of strength
            $table->timestamps();

            $table->index(['assessment_type', 'mastery_level']);
            $table->index(['proficiency_level']);
            $table->index(['assessment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_assessment_progress');
    }
};
