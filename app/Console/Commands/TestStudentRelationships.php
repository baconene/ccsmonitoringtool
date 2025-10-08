<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Student;

class TestStudentRelationships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:student-relationships';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test User-Student model relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing User-Student Relationships...');
        $this->newLine();

        // Test 1: Get a student user
        $user = User::where('email', 'student1@test.com')->first();

        if ($user) {
            $this->line("✓ Found user: {$user->name} ({$user->email})");
            $this->line("  - Role: {$user->role_name}");
            $this->line("  - Grade Level: {$user->grade_level}");
            $this->line("  - Section: {$user->section}");
            
            // Test student relationship
            if ($user->hasStudentRecord()) {
                $this->line("✓ Has student record: Yes");
                $student = $user->student;
                $this->line("  - Student ID: {$student->student_id}");
                $this->line("  - Enrollment Number: {$student->enrollment_number}");
                $this->line("  - Program: {$student->program}");
                $this->line("  - Department: {$student->department}");
                $this->line("  - Status: {$student->status}");
                $this->line("  - Enrollment Date: {$student->enrollment_date}");
                
                // Test reverse relationship
                $userFromStudent = $student->user;
                $this->line("✓ Reverse relationship works: {$userFromStudent->name}");
                
                // Test additional accessors
                $this->line("✓ Student name accessor: {$student->name}");
                $this->line("✓ Student email accessor: {$student->email}");
            } else {
                $this->error("❌ No student record found");
            }
        } else {
            $this->error("❌ User not found");
        }

        $this->newLine();
        $this->info('📊 Summary:');
        $this->line("- Total Users: " . User::count());
        $this->line("- Total Students: " . Student::count());
        $this->line("- Student Users with Records: " . User::whereHas('student')->count());
        $this->line("- Student Role Users: " . User::whereHas('role', function($q) { $q->where('name', 'student'); })->count());

        // Test all students
        $this->newLine();
        $this->info('📋 All Students:');
        Student::with('user')->get()->each(function ($student) {
            $this->line("- {$student->student_id}: {$student->name} ({$student->program})");
        });

        $this->newLine();
        $this->info('✅ Test completed!');
    }
}
