<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Instructor;

class TestInstructorRelationships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:instructor-relationships';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test User-Instructor model relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing User-Instructor Relationships...');
        $this->newLine();

        // Test 1: Get an instructor user
        $user = User::where('email', 'instructor1@test.com')->first();

        if ($user) {
            $this->line("✓ Found user: {$user->name} ({$user->email})");
            $this->line("  - Role: {$user->role_name}");
            
            // Test instructor relationship
            if ($user->hasInstructorRecord()) {
                $this->line("✓ Has instructor record: Yes");
                $instructor = $user->instructor;
                $this->line("  - Instructor ID: {$instructor->instructor_id}");
                $this->line("  - Employee ID: {$instructor->employee_id}");
                $this->line("  - Full Display Name: {$instructor->full_display_name}");
                $this->line("  - Professional Title: {$instructor->professional_title}");
                $this->line("  - Department: {$instructor->department}");
                $this->line("  - Specialization: {$instructor->specialization}");
                $this->line("  - Office: {$instructor->office_location}");
                $this->line("  - Phone: {$instructor->phone}");
                $this->line("  - Office Hours: {$instructor->office_hours}");
                $this->line("  - Employment Type: {$instructor->employment_type}");
                $this->line("  - Status: {$instructor->status}");
                $this->line("  - Years of Service: {$instructor->years_of_service}");
                $this->line("  - Education Level: {$instructor->education_level}");
                $this->line("  - Experience: {$instructor->years_experience} years");
                
                // Test reverse relationship
                $userFromInstructor = $instructor->user;
                $this->line("✓ Reverse relationship works: {$userFromInstructor->name}");
                
                // Test additional accessors
                $this->line("✓ Instructor name accessor: {$instructor->name}");
                $this->line("✓ Instructor email accessor: {$instructor->email}");
                $this->line("✓ Qualification Summary: {$instructor->qualification_summary}");
                
                // Test methods
                $this->line("✓ Is Active: " . ($instructor->isActive() ? 'Yes' : 'No'));
                $this->line("✓ Is Full Time: " . ($instructor->isFullTime() ? 'Yes' : 'No'));
                
            } else {
                $this->error("❌ No instructor record found");
            }
        } else {
            $this->error("❌ User not found");
        }

        $this->newLine();
        $this->info('📊 Summary:');
        $this->line("- Total Users: " . User::count());
        $this->line("- Total Instructors: " . Instructor::count());
        $this->line("- Instructor Users with Records: " . User::whereHas('instructor')->count());
        $this->line("- Instructor Role Users: " . User::whereHas('role', function($q) { $q->where('name', 'instructor'); })->count());

        // Test all instructors by department
        $this->newLine();
        $this->info('📋 Instructors by Department:');
        Instructor::with('user')
            ->select('department', \DB::raw('count(*) as count'))
            ->groupBy('department')
            ->get()
            ->each(function ($dept) {
                $this->line("- {$dept->department}: {$dept->count} instructors");
            });

        // Test sample instructors
        $this->newLine();
        $this->info('👨‍🏫 Sample Instructors:');
        Instructor::with('user')->take(5)->get()->each(function ($instructor) {
            $this->line("- {$instructor->instructor_id}: {$instructor->full_display_name} ({$instructor->department})");
        });

        $this->newLine();
        $this->info('✅ Test completed!');
    }
}
