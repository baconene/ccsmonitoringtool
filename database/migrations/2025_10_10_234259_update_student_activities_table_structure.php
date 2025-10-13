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
        Schema::table('student_activities', function (Blueprint $table) {
            // Drop existing foreign key constraint and indexes
            $table->dropForeign(['user_id']);
            $table->dropIndex('unique_student_activity');
            $table->dropIndex('student_activities_user_id_module_id_status_index');
            
            // Rename user_id to student_id
            $table->renameColumn('user_id', 'student_id');
        });
        
        // Add new foreign key constraint and indexes in a separate schema call
        Schema::table('student_activities', function (Blueprint $table) {
            // Add foreign key constraint to students table
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            
            // Recreate indexes with new column name
            $table->unique(['student_id', 'activity_id', 'module_id'], 'unique_student_activity');
            $table->index(['student_id', 'module_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_activities', function (Blueprint $table) {
            // Drop new foreign key constraint and indexes
            $table->dropForeign(['student_id']);
            $table->dropIndex('unique_student_activity');
            $table->dropIndex('student_activities_student_id_module_id_status_index');
            
            // Rename back to user_id
            $table->renameColumn('student_id', 'user_id');
        });
        
        // Add back old foreign key constraint and indexes
        Schema::table('student_activities', function (Blueprint $table) {
            // Add back foreign key constraint to users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Recreate old indexes
            $table->unique(['user_id', 'activity_id', 'module_id'], 'unique_student_activity');
            $table->index(['user_id', 'module_id', 'status']);
        });
    }
};
