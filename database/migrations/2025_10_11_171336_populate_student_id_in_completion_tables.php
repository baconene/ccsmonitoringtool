<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Populate student_id in lesson_completions table
        DB::statement('
            UPDATE lesson_completions 
            SET student_id = (
                SELECT students.id 
                FROM students 
                WHERE students.user_id = lesson_completions.user_id
            )
            WHERE student_id IS NULL
        ');

        // Populate student_id in module_completions table
        DB::statement('
            UPDATE module_completions 
            SET student_id = (
                SELECT students.id 
                FROM students 
                WHERE students.user_id = module_completions.user_id
            )
            WHERE student_id IS NULL
        ');

        // Populate student_id in course_enrollments table if any records are missing it
        DB::statement('
            UPDATE course_enrollments 
            SET student_id = (
                SELECT students.id 
                FROM students 
                WHERE students.user_id = course_enrollments.user_id
            )
            WHERE student_id IS NULL AND user_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear the populated student_id values
        DB::statement('UPDATE lesson_completions SET student_id = NULL');
        DB::statement('UPDATE module_completions SET student_id = NULL');
        // Note: We don't clear course_enrollments student_id as it might have been set independently
    }
};
