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
        // Drop old document-related tables if they exist
        Schema::dropIfExists('lesson_document');
        Schema::dropIfExists('module_document');
        Schema::dropIfExists('documents');

        // Create new documents table
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('original_name');
            $table->string('file_path');
            $table->bigInteger('file_size')->unsigned();
            $table->string('mime_type');
            $table->string('extension', 10);
            $table->string('document_type', 50); // course, lesson, activity, report, etc.
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('document_type');
            $table->index('uploaded_by');
        });

        // Create course_documents table
        Schema::create('course_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->enum('visibility', ['public', 'students', 'instructors'])->default('students');
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['document_id', 'course_id']);
        });

        // Create lesson_documents table
        Schema::create('lesson_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->enum('visibility', ['public', 'students', 'instructors'])->default('students');
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['document_id', 'lesson_id']);
        });

        // Create activity_documents table
        Schema::create('activity_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->enum('visibility', ['public', 'students', 'instructors'])->default('students');
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['document_id', 'activity_id']);
        });

        // Create report_documents table
        Schema::create('report_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('cascade');
            $table->string('report_type', 50); // progress, final, transcript, etc.
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });

        // Create project_documents table (student submissions)
        Schema::create('project_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->timestamp('submission_date')->nullable();
            $table->enum('status', ['submitted', 'graded', 'returned', 'resubmitted'])->default('submitted');
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->unique(['document_id', 'activity_id', 'student_id']);
        });

        // Create assessment_documents table
        Schema::create('assessment_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('cascade');
            $table->string('document_category', 50); // answer_sheet, rubric, solution, etc.
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();
        });

        // Create student_documents table
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('document_category', 50); // transcript, id, certificate, medical, etc.
            $table->string('academic_year', 20)->nullable();
            $table->boolean('verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        // Create instructor_documents table
        Schema::create('instructor_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('instructors')->onDelete('cascade');
            $table->string('document_category', 50); // certification, resume, license, etc.
            $table->date('expiry_date')->nullable();
            $table->boolean('verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_documents');
        Schema::dropIfExists('student_documents');
        Schema::dropIfExists('assessment_documents');
        Schema::dropIfExists('project_documents');
        Schema::dropIfExists('report_documents');
        Schema::dropIfExists('activity_documents');
        Schema::dropIfExists('lesson_documents');
        Schema::dropIfExists('course_documents');
        Schema::dropIfExists('documents');
    }
};
