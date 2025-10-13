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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            
            // Core schedule information
            $table->foreignId('schedule_type_id')->constrained('schedule_types')->restrictOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable()->comment('Room number, building, or online link');
            
            // Date and time
            $table->dateTime('from_datetime');
            $table->dateTime('to_datetime');
            $table->boolean('is_all_day')->default(false);
            
            // Recurring events support
            $table->boolean('is_recurring')->default(false);
            $table->text('recurrence_rule')->nullable()->comment('iCal RRULE format for recurring events');
            
            // Status tracking
            $table->enum('status', ['scheduled', 'cancelled', 'completed', 'in_progress'])->default('scheduled');
            
            // Creator tracking
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            
            // Polymorphic relationship to schedulable entities (Activity, Course, etc.)
            $table->string('schedulable_type')->nullable();
            $table->unsignedBigInteger('schedulable_id')->nullable();
            
            // Metadata for future extensibility
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes(); // Soft delete support
            
            // Indexes for performance
            $table->index('from_datetime');
            $table->index('to_datetime');
            $table->index('status');
            $table->index(['schedulable_type', 'schedulable_id']);
            $table->index(['from_datetime', 'to_datetime'], 'idx_date_range');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
