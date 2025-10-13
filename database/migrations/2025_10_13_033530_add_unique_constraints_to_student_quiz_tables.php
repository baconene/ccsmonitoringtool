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
        // Step 1: Remove duplicate student_quiz_progress records
        // Keep the most recent record for each student_id + quiz_id + activity_id combination
        DB::statement("
            DELETE FROM student_quiz_progress
            WHERE id NOT IN (
                SELECT MAX(id)
                FROM student_quiz_progress
                GROUP BY student_id, quiz_id, activity_id
            )
        ");

        // Step 2: Remove duplicate student_quiz_answers records
        // Keep the most recent record for each quiz_progress_id + question_id combination
        DB::statement("
            DELETE FROM student_quiz_answers
            WHERE id NOT IN (
                SELECT MAX(id)
                FROM student_quiz_answers
                GROUP BY quiz_progress_id, question_id
            )
        ");

        // Step 3: Update answer_text from selected_option_id where answer_text is empty
        DB::statement("
            UPDATE student_quiz_answers
            SET answer_text = (
                SELECT option_text
                FROM question_options
                WHERE question_options.id = student_quiz_answers.selected_option_id
            )
            WHERE student_quiz_answers.selected_option_id IS NOT NULL
            AND (student_quiz_answers.answer_text IS NULL OR student_quiz_answers.answer_text = '')
        ");

        // Step 4: Add unique constraint to student_quiz_progress
        // Ensures one progress record per student per quiz per activity
        Schema::table('student_quiz_progress', function (Blueprint $table) {
            $table->unique(['student_id', 'quiz_id', 'activity_id'], 'unique_student_quiz_activity');
        });

        // Step 5: Add unique constraint to student_quiz_answers
        // Ensures one answer per question per quiz progress
        Schema::table('student_quiz_answers', function (Blueprint $table) {
            $table->unique(['quiz_progress_id', 'question_id'], 'unique_progress_question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_quiz_progress', function (Blueprint $table) {
            $table->dropUnique('unique_student_quiz_activity');
        });

        Schema::table('student_quiz_answers', function (Blueprint $table) {
            $table->dropUnique('unique_progress_question');
        });
    }
};
