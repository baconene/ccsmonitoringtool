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
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('grade_level');
            $table->string('course_code')->nullable()->after('created_by');
            $table->integer('credits')->default(3)->after('course_code');
            $table->string('semester')->default('Fall')->after('credits');
            $table->string('academic_year')->default('2024')->after('semester');
            $table->boolean('is_active')->default(true)->after('academic_year');
            $table->integer('enrollment_limit')->nullable()->after('is_active');
            $table->date('start_date')->nullable()->after('enrollment_limit');
            $table->date('end_date')->nullable()->after('start_date');
            
            // Add foreign key constraint for created_by
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'created_by',
                'course_code',
                'credits',
                'semester',
                'academic_year',
                'is_active',
                'enrollment_limit',
                'start_date',
                'end_date'
            ]);
        });
    }
};
