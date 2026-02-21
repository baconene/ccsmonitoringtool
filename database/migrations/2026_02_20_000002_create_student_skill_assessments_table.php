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
        Schema::create('student_skill_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('skill_id');
            $table->decimal('normalized_score', 5, 2)->default(0); // 0-100
            $table->decimal('feedback_score', 5, 2)->nullable(); // Additional feedback weight
            $table->decimal('peer_review_score', 5, 2)->nullable(); // Peer assessment
            $table->integer('attempt_count')->default(1);
            $table->decimal('improvement_factor', 5, 2)->default(1.0); // 1.0 = no improvement bonus
            $table->integer('days_late')->default(0); // For calculating late penalty
            $table->decimal('final_score', 5, 2)->default(0); // Final computed score
            $table->enum('mastery_level', ['not_met', 'met', 'exceeds'])->default('not_met');
            $table->decimal('consistency_score', 5, 2)->nullable(); // For measuring stable performance
            $table->json('assessment_metadata')->nullable(); // Store extra data like source activities
            $table->timestamps();

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');

            $table->foreign('skill_id')
                ->references('id')
                ->on('skills')
                ->onDelete('cascade');

            $table->unique(['student_id', 'skill_id']);
            $table->index('student_id');
            $table->index('skill_id');
            $table->index('mastery_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_skill_assessments');
    }
};
