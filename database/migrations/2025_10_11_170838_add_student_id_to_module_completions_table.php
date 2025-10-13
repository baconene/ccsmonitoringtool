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
        Schema::table('module_completions', function (Blueprint $table) {
            $table->unsignedBigInteger('student_id')->nullable()->after('user_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            
            // Add unique constraint for student_id + module_id + course_id combination
            $table->unique(['student_id', 'module_id', 'course_id'], 'module_completions_student_module_course_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('module_completions', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropUnique('module_completions_student_module_course_unique');
            $table->dropColumn('student_id');
        });
    }
};
