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
        Schema::table('student_quiz_answers', function (Blueprint $table) {
            // Drop the old foreign key constraint if it exists
            $table->dropForeign(['quiz_progress_id']);
            
            // Rename column to reference student_activity_progress
            $table->renameColumn('quiz_progress_id', 'activity_progress_id');
        });
        
        Schema::table('student_quiz_answers', function (Blueprint $table) {
            // Add new foreign key to student_activity_progress
            $table->foreign('activity_progress_id')
                ->references('id')
                ->on('student_activity_progress')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_quiz_answers', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['activity_progress_id']);
            
            // Rename back to original
            $table->renameColumn('activity_progress_id', 'quiz_progress_id');
        });
        
        // Note: We can't restore the old foreign key because student_quiz_progress table no longer exists
    }
};
