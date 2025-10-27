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
        // Drop old progress tables after data has been migrated
        Schema::dropIfExists('student_assignment_progress');
        Schema::dropIfExists('student_quiz_progress');
        Schema::dropIfExists('student_project_progress');
        Schema::dropIfExists('student_assessment_progress');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate tables if needed (would need full schema definitions)
        // This is a destructive migration - rolling back would lose data
        // unless you restore from backup
    }
};
