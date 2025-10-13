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
        // Activity-specific schedule details
        Schema::create('schedule_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->unique()->constrained('schedules')->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->dateTime('submission_deadline')->nullable();
            $table->decimal('passing_score', 5, 2)->nullable();
            $table->timestamps();
        });
        
        // Course-specific schedule details
        Schema::create('schedule_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->unique()->constrained('schedules')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->integer('session_number')->nullable()->comment('Lecture 1, 2, 3, etc.');
            $table->text('topics_covered')->nullable();
            $table->text('required_materials')->nullable();
            $table->timestamps();
        });
        
        // Adhoc schedule details (personal events, meetings, etc.)
        Schema::create('schedule_adhoc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->unique()->constrained('schedules')->cascadeOnDelete();
            $table->string('event_type', 50)->nullable()->comment('meeting, appointment, reminder, personal');
            $table->enum('privacy_level', ['public', 'private', 'confidential'])->default('private');
            $table->integer('reminder_minutes')->nullable()->comment('Minutes before event to send reminder');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_adhoc');
        Schema::dropIfExists('schedule_courses');
        Schema::dropIfExists('schedule_activities');
    }
};
