<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\ScheduleType;
use App\Models\ScheduleParticipant;
use App\Models\ScheduleActivity;
use App\Models\ScheduleCourse;
use App\Models\ScheduleAdhoc;
use App\Models\User;
use App\Models\Activity;
use App\Models\Course;
use Carbon\Carbon;

class ScheduleSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding sample schedules...');

        // Get schedule types
        $activityType = ScheduleType::where('name', 'activity')->first();
        $courseType = ScheduleType::where('name', 'course')->first();
        $adhocType = ScheduleType::where('name', 'adhoc')->first();

        // Get sample users
        $instructor1 = User::where('role_id', 2)->first(); // Instructor
        $student1 = User::where('role_id', 3)->skip(0)->first();
        $student2 = User::where('role_id', 3)->skip(1)->first();

        if (!$instructor1 || !$student1) {
            $this->command->warn('⚠️  No users found. Please seed users first.');
            return;
        }

        // Get sample activity and course
        $activity = Activity::first();
        $course = Course::first();

        // =====================================================
        // 1. Sample Activity Schedule (Quiz)
        // =====================================================
        $this->command->info('Creating Activity Schedule...');
        
        $activitySchedule = Schedule::create([
            'schedule_type_id' => $activityType->id,
            'title' => 'Mathematics Quiz 1',
            'description' => 'Chapter 1-3 Quiz covering algebra and geometry fundamentals',
            'location' => 'Room 101',
            'from_datetime' => Carbon::now()->addDays(2)->setTime(9, 0),
            'to_datetime' => Carbon::now()->addDays(2)->setTime(10, 0),
            'status' => 'scheduled',
            'created_by' => $instructor1->id,
            'schedulable_type' => $activity ? 'App\\Models\\Activity' : null,
            'schedulable_id' => $activity?->id,
        ]);

        // Add participants
        ScheduleParticipant::create([
            'schedule_id' => $activitySchedule->id,
            'user_id' => $instructor1->id,
            'role_in_schedule' => 'instructor',
            'participation_status' => 'accepted',
        ]);

        ScheduleParticipant::create([
            'schedule_id' => $activitySchedule->id,
            'user_id' => $student1->id,
            'role_in_schedule' => 'student',
            'participation_status' => 'invited',
        ]);

        if ($student2) {
            ScheduleParticipant::create([
                'schedule_id' => $activitySchedule->id,
                'user_id' => $student2->id,
                'role_in_schedule' => 'student',
                'participation_status' => 'accepted',
            ]);
        }

        // Add activity details
        if ($activity) {
            ScheduleActivity::create([
                'schedule_id' => $activitySchedule->id,
                'activity_id' => $activity->id,
                'submission_deadline' => Carbon::now()->addDays(2)->setTime(10, 0),
                'passing_score' => 70.00,
            ]);
        }

        $this->command->info('✓ Activity Schedule created');

        // =====================================================
        // 2. Sample Course Schedule (Lecture)
        // =====================================================
        $this->command->info('Creating Course Schedule...');
        
        $courseSchedule = Schedule::create([
            'schedule_type_id' => $courseType->id,
            'title' => 'Computer Programming 101 - Lecture 5',
            'description' => 'Introduction to Object-Oriented Programming concepts',
            'location' => 'Computer Lab B',
            'from_datetime' => Carbon::now()->addDays(3)->setTime(14, 0),
            'to_datetime' => Carbon::now()->addDays(3)->setTime(16, 0),
            'status' => 'scheduled',
            'created_by' => $instructor1->id,
            'schedulable_type' => $course ? 'App\\Models\\Course' : null,
            'schedulable_id' => $course?->id,
        ]);

        // Add participants
        ScheduleParticipant::create([
            'schedule_id' => $courseSchedule->id,
            'user_id' => $instructor1->id,
            'role_in_schedule' => 'instructor',
            'participation_status' => 'accepted',
        ]);

        ScheduleParticipant::create([
            'schedule_id' => $courseSchedule->id,
            'user_id' => $student1->id,
            'role_in_schedule' => 'student',
            'participation_status' => 'accepted',
        ]);

        if ($student2) {
            ScheduleParticipant::create([
                'schedule_id' => $courseSchedule->id,
                'user_id' => $student2->id,
                'role_in_schedule' => 'student',
                'participation_status' => 'invited',
            ]);
        }

        // Add course details
        if ($course) {
            ScheduleCourse::create([
                'schedule_id' => $courseSchedule->id,
                'course_id' => $course->id,
                'session_number' => 5,
                'topics_covered' => 'Classes, Objects, Inheritance, Polymorphism',
                'required_materials' => 'Laptop, Python IDE installed',
            ]);
        }

        $this->command->info('✓ Course Schedule created');

        // =====================================================
        // 3. Sample Adhoc Schedule (Personal Event)
        // =====================================================
        $this->command->info('Creating Adhoc Schedule...');
        
        $adhocSchedule = Schedule::create([
            'schedule_type_id' => $adhocType->id,
            'title' => 'Department Meeting',
            'description' => 'Monthly faculty meeting to discuss curriculum updates',
            'location' => 'Conference Room A',
            'from_datetime' => Carbon::now()->addDays(1)->setTime(10, 0),
            'to_datetime' => Carbon::now()->addDays(1)->setTime(11, 30),
            'status' => 'scheduled',
            'created_by' => $instructor1->id,
        ]);

        // Add participant (creator only)
        ScheduleParticipant::create([
            'schedule_id' => $adhocSchedule->id,
            'user_id' => $instructor1->id,
            'role_in_schedule' => 'organizer',
            'participation_status' => 'accepted',
        ]);

        // Add adhoc details
        ScheduleAdhoc::create([
            'schedule_id' => $adhocSchedule->id,
            'event_type' => 'meeting',
            'privacy_level' => 'private',
            'reminder_minutes' => 30,
        ]);

        $this->command->info('✓ Adhoc Schedule created');

        // =====================================================
        // 4. Additional Sample Schedules
        // =====================================================
        
        // Tomorrow's class
        $tomorrowSchedule = Schedule::create([
            'schedule_type_id' => $courseType->id,
            'title' => 'Physics 101 - Lab Session',
            'description' => 'Newton\'s Laws experiment',
            'location' => 'Physics Lab',
            'from_datetime' => Carbon::tomorrow()->setTime(10, 0),
            'to_datetime' => Carbon::tomorrow()->setTime(12, 0),
            'status' => 'scheduled',
            'created_by' => $instructor1->id,
        ]);

        ScheduleParticipant::create([
            'schedule_id' => $tomorrowSchedule->id,
            'user_id' => $instructor1->id,
            'role_in_schedule' => 'instructor',
            'participation_status' => 'accepted',
        ]);

        ScheduleParticipant::create([
            'schedule_id' => $tomorrowSchedule->id,
            'user_id' => $student1->id,
            'role_in_schedule' => 'student',
            'participation_status' => 'accepted',
        ]);

        // Next week's exam
        $examType = ScheduleType::where('name', 'exam')->first();
        if ($examType) {
            $examSchedule = Schedule::create([
                'schedule_type_id' => $examType->id,
                'title' => 'Midterm Examination - Mathematics',
                'description' => 'Comprehensive midterm covering chapters 1-5',
                'location' => 'Exam Hall A',
                'from_datetime' => Carbon::now()->addWeek()->setTime(9, 0),
                'to_datetime' => Carbon::now()->addWeek()->setTime(11, 0),
                'status' => 'scheduled',
                'created_by' => $instructor1->id,
            ]);

            ScheduleParticipant::create([
                'schedule_id' => $examSchedule->id,
                'user_id' => $instructor1->id,
                'role_in_schedule' => 'proctor',
                'participation_status' => 'accepted',
            ]);

            ScheduleParticipant::create([
                'schedule_id' => $examSchedule->id,
                'user_id' => $student1->id,
                'role_in_schedule' => 'student',
                'participation_status' => 'invited',
            ]);
        }

        $this->command->info('✅ Sample schedules seeded successfully!');
        $this->command->info('📊 Created ' . Schedule::count() . ' schedules with ' . ScheduleParticipant::count() . ' participants');
    }
}
