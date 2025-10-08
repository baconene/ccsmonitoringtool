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
        Schema::create('student_assignment_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_activity_id')->constrained()->onDelete('cascade');
            $table->text('submission_content')->nullable();
            $table->json('attachment_files')->nullable(); // Store file paths/URLs
            $table->enum('submission_status', ['draft', 'submitted', 'returned', 'approved'])->default('draft');
            $table->integer('revision_count')->default(0);
            $table->timestamp('due_date')->nullable();
            $table->timestamp('submission_date')->nullable();
            $table->timestamp('grading_date')->nullable();
            $table->text('instructor_comments')->nullable();
            $table->decimal('points_earned', 5, 2)->nullable();
            $table->decimal('points_possible', 5, 2)->nullable();
            $table->json('rubric_scores')->nullable(); // Store rubric-based scoring
            $table->timestamps();

            $table->index(['submission_status', 'due_date']);
            $table->index(['submission_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_assignment_progress');
    }
};
