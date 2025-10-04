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
        Schema::table('lessons', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable()->after('id');
            $table->unsignedBigInteger('module_id')->nullable()->after('course_id');
            $table->integer('duration')->default(45)->after('description'); // Duration in minutes
            $table->integer('order')->default(1)->after('duration');
            $table->enum('content_type', ['video', 'text', 'quiz', 'assignment'])->default('text')->after('order');
            
            // Add foreign key constraints
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['module_id']);
            $table->dropColumn(['course_id', 'module_id', 'duration', 'order', 'content_type']);
        });
    }
};
