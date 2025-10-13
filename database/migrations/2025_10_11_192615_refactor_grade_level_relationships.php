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
        // Remove grade_level column from courses table
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('grade_level');
        });

        // Add grade_level_id to students table
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('grade_level_id')->nullable()->after('user_id');
            $table->foreign('grade_level_id')->references('id')->on('grade_levels')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back grade_level column to courses table
        Schema::table('courses', function (Blueprint $table) {
            $table->string('grade_level')->nullable()->after('description');
        });

        // Remove grade_level_id from students table
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['grade_level_id']);
            $table->dropColumn('grade_level_id');
        });
    }
};
