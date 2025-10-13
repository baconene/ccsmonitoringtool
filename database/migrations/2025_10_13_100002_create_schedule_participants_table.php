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
        Schema::create('schedule_participants', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Role in this specific schedule (can differ from user's global role)
            $table->string('role_in_schedule', 50)->comment('instructor, student, organizer, attendee, etc.');
            
            // Participation status tracking
            $table->enum('participation_status', [
                'invited',
                'accepted',
                'declined',
                'tentative',
                'attended',
                'absent'
            ])->default('invited');
            
            // Response and attendance tracking
            $table->dateTime('response_datetime')->nullable();
            $table->dateTime('attended_at')->nullable();
            $table->text('notes')->nullable()->comment('Special notes for this participant');
            
            $table->timestamps();
            
            // Ensure a user can only be added once per schedule
            $table->unique(['schedule_id', 'user_id'], 'unique_schedule_user');
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('schedule_id');
            $table->index('participation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_participants');
    }
};
