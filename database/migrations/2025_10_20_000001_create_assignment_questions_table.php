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
        // Add assignment_type to assignments table
        Schema::table('assignments', function (Blueprint $table) {
            $table->enum('assignment_type', ['objective', 'file_upload', 'mixed'])->default('objective')->after('document_id');
            $table->integer('total_points')->default(100)->after('assignment_type');
            $table->integer('time_limit')->nullable()->comment('Time limit in minutes')->after('total_points');
            $table->boolean('allow_late_submission')->default(false)->after('time_limit');
            $table->text('instructions')->nullable()->after('description');
        });

        // Create assignment_questions table for objective questions
        Schema::create('assignment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            $table->text('question_text');
            $table->enum('question_type', ['true_false', 'multiple_choice', 'enumeration', 'short_answer'])->default('multiple_choice');
            $table->integer('points')->default(1);
            $table->text('correct_answer')->nullable()->comment('For true/false, enumeration, and short answer');
            $table->json('acceptable_answers')->nullable()->comment('For enumeration with multiple acceptable answers');
            $table->boolean('case_sensitive')->default(false)->comment('For text-based answers');
            $table->integer('order')->default(0)->comment('Question order in assignment');
            $table->text('explanation')->nullable()->comment('Explanation shown after submission');
            $table->timestamps();

            $table->index(['assignment_id', 'order']);
        });

        // Create assignment_question_options table for multiple choice options
        Schema::create('assignment_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_question_id')->constrained('assignment_questions')->onDelete('cascade');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['assignment_question_id', 'order']);
        });

        // Create student_assignment_answers table
        Schema::create('student_assignment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            $table->foreignId('assignment_question_id')->nullable()->constrained('assignment_questions')->onDelete('cascade');
            $table->text('answer_text')->nullable()->comment('For objective questions');
            $table->json('selected_options')->nullable()->comment('For multiple choice - array of option IDs');
            $table->string('file_path')->nullable()->comment('For file uploads');
            $table->string('original_filename')->nullable();
            $table->boolean('is_correct')->nullable()->comment('Auto-graded for objective questions');
            $table->decimal('points_earned', 5, 2)->nullable();
            $table->text('instructor_feedback')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'assignment_id']);
            $table->index(['assignment_question_id']);
        });

        // Update student_assignment_progress table with additional fields
        Schema::table('student_assignment_progress', function (Blueprint $table) {
            $table->integer('total_questions')->default(0)->after('submission_status');
            $table->integer('answered_questions')->default(0)->after('total_questions');
            $table->decimal('auto_graded_score', 5, 2)->nullable()->after('points_earned')->comment('Auto-calculated from objective questions');
            $table->boolean('requires_grading')->default(false)->after('auto_graded_score')->comment('True if has file uploads or subjective questions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_assignment_progress', function (Blueprint $table) {
            $table->dropColumn(['total_questions', 'answered_questions', 'auto_graded_score', 'requires_grading']);
        });

        Schema::dropIfExists('student_assignment_answers');
        Schema::dropIfExists('assignment_question_options');
        Schema::dropIfExists('assignment_questions');

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['assignment_type', 'total_points', 'time_limit', 'allow_late_submission', 'instructions']);
        });
    }
};
