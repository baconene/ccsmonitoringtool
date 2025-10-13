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
        Schema::table('student_quiz_progress', function (Blueprint $table) {
            // Drop the existing foreign key constraint to users
            $table->dropForeign(['student_id']);
            
            // Add foreign key constraint to students table
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_quiz_progress', function (Blueprint $table) {
            // Drop the foreign key constraint to students
            $table->dropForeign(['student_id']);
            
            // Add back foreign key constraint to users table
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
