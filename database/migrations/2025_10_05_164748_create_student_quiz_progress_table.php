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
        Schema::create('student_quiz_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_submitted')->default(false);
            $table->integer('completed_questions')->default(0);
            $table->integer('total_questions')->default(0);
            $table->decimal('score', 5, 2)->nullable();
            $table->decimal('percentage_score', 5, 2)->nullable();
            $table->integer('time_spent')->nullable()->comment('Time spent in minutes');
            $table->timestamps();
            
            // Add indexes
            $table->index(['student_id', 'quiz_id']);
            $table->index(['student_id', 'activity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_quiz_progress');
    }
};
