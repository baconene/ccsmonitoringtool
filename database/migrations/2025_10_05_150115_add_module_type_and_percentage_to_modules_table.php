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
        Schema::table('modules', function (Blueprint $table) {
            $table->string('module_type')->nullable()->after('description'); // e.g., "Lessons", "Activities", "Mixed", "Quizzes", "Assignments", "Assessment"
            $table->decimal('module_percentage', 5, 2)->nullable()->after('module_type'); // Percentage weight of the module (0-100)
            $table->string('title')->nullable()->after('id'); // Add title field
            $table->unsignedBigInteger('created_by')->nullable()->after('course_id'); // User ID of the creator
            
            // Add foreign key for created_by
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['module_type', 'module_percentage', 'title', 'created_by']);
        });
    }
};
