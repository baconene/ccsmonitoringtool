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
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->string('instructor_id')->unique(); // Custom instructor ID
            $table->unsignedBigInteger('user_id'); // Reference to users table
            $table->string('employee_id')->nullable()->unique(); // Employee identification number
            $table->string('title')->nullable(); // Dr., Prof., Mr., Ms., etc.
            $table->string('department'); // Department they belong to
            $table->string('specialization')->nullable(); // Area of expertise
            $table->text('bio')->nullable(); // Biography/description
            $table->string('office_location')->nullable(); // Office room number/location
            $table->string('phone')->nullable(); // Contact phone number
            $table->string('office_hours')->nullable(); // Office hours schedule
            $table->date('hire_date')->nullable(); // Date of hiring
            $table->enum('employment_type', ['full-time', 'part-time', 'adjunct', 'visiting'])->default('full-time');
            $table->enum('status', ['active', 'inactive', 'on-leave', 'retired'])->default('active');
            $table->decimal('salary', 10, 2)->nullable(); // Salary information (optional)
            $table->string('education_level')->nullable(); // PhD, Masters, Bachelors, etc.
            $table->string('certifications')->nullable(); // Professional certifications
            $table->integer('years_experience')->nullable(); // Years of teaching experience
            $table->json('metadata')->nullable(); // Additional instructor-specific data
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index(['status', 'department']);
            $table->index(['employment_type', 'hire_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructors');
    }
};
