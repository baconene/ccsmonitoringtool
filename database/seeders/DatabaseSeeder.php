<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Course;
use App\Models\Module;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call RoleSeeder first to create roles
        $this->call([
            RoleSeeder::class,
        ]);

        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $instructorRole = Role::where('name', 'instructor')->first();
        $studentRole = Role::where('name', 'student')->first();

        // Create 1 Admin
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'test.admin@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'role_id' => $adminRole->id,
        ]);
        echo "✓ Created Admin: {$admin->email}\n";

        // Create 4 Instructors
        $instructors = [];
        $instructorNames = ['Dr. Smith', 'Prof. Johnson', 'Dr. Williams', 'Prof. Brown'];
        
        foreach ($instructorNames as $index => $name) {
            $instructor = User::create([
                'name' => $name,
                'email' => 'instructor' . ($index + 1) . '@test.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'role_id' => $instructorRole->id,
            ]);
            $instructors[] = $instructor;
            echo "✓ Created Instructor: {$instructor->email}\n";
        }

        // Create 10 Students with grade levels
        $students = [];
        $gradeLevels = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        $sections = ['Section A', 'Section B', 'Section C'];
        
        for ($i = 1; $i <= 10; $i++) {
            $student = User::create([
                'name' => 'Student ' . $i,
                'email' => 'student' . $i . '@test.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'role_id' => $studentRole->id,
                'grade_level' => $gradeLevels[array_rand($gradeLevels)],
                'section' => $sections[array_rand($sections)],
            ]);
            $students[] = $student;
            echo "✓ Created Student: {$student->email} ({$student->grade_level}, {$student->section})\n";
        }

        // Create 10 Courses with modules
        $courseData = [
            ['name' => 'Introduction to Programming', 'description' => 'Learn the basics of programming', 'grade_level' => 'Grade 9'],
            ['name' => 'Advanced Mathematics', 'description' => 'Advanced math concepts', 'grade_level' => 'Grade 10'],
            ['name' => 'Physics 101', 'description' => 'Introduction to Physics', 'grade_level' => 'Grade 10'],
            ['name' => 'Chemistry Fundamentals', 'description' => 'Basic chemistry principles', 'grade_level' => 'Grade 9'],
            ['name' => 'World History', 'description' => 'Explore world history', 'grade_level' => 'Grade 11'],
            ['name' => 'English Literature', 'description' => 'Study classic literature', 'grade_level' => 'Grade 11'],
            ['name' => 'Biology Basics', 'description' => 'Introduction to Biology', 'grade_level' => 'Grade 8'],
            ['name' => 'Web Development', 'description' => 'Build modern websites', 'grade_level' => 'Grade 12'],
            ['name' => 'Digital Art', 'description' => 'Create digital artwork', 'grade_level' => 'Grade 8'],
            ['name' => 'Music Theory', 'description' => 'Learn music fundamentals', 'grade_level' => 'Grade 7'],
        ];

        foreach ($courseData as $index => $data) {
            // Assign instructor (distribute courses among 4 instructors)
            $instructor = $instructors[$index % 4];

            $course = Course::create([
                'name' => $data['name'],
                'title' => $data['name'],
                'description' => $data['description'],
                'instructor_id' => $instructor->id,
                'grade_level' => $data['grade_level'],
            ]);

            echo "✓ Created Course: {$course->name} (Instructor: {$instructor->name}, Grade: {$course->grade_level})\n";

            // Create 3-5 modules for each course
            $moduleCount = rand(3, 5);
            for ($m = 1; $m <= $moduleCount; $m++) {
                $module = Module::create([
                    'course_id' => $course->id,
                    'name' => "Module {$m}: " . $this->getModuleName($m),
                    'description' => "This is module {$m} for {$course->name}",
                    'sequence' => $m,
                ]);
                echo "  ↳ Created Module: {$module->name}\n";
            }

            // Enroll students to courses based on grade level
            $eligibleStudents = array_filter($students, function($student) use ($course) {
                return $student->grade_level === $course->grade_level;
            });

            // If no exact match, enroll some random students
            if (empty($eligibleStudents)) {
                $eligibleStudents = array_slice($students, 0, rand(2, 4));
            }

            foreach ($eligibleStudents as $student) {
                CourseEnrollment::create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'enrolled_at' => now(),
                    'progress' => rand(0, 100),
                    'is_completed' => false,
                ]);
                echo "  ↳ Enrolled: {$student->name}\n";
            }
        }

        echo "\n";
        echo "════════════════════════════════════════\n";
        echo "✓ Database seeded successfully!\n";
        echo "════════════════════════════════════════\n";
        echo "Admin:       test.admin@test.com / 12345678\n";
        echo "Instructors: instructor1-4@test.com / 12345678\n";
        echo "Students:    student1-10@test.com / 12345678\n";
        echo "════════════════════════════════════════\n";
    }

    private function getModuleName($number): string
    {
        $names = [
            1 => 'Introduction and Fundamentals',
            2 => 'Core Concepts',
            3 => 'Advanced Topics',
            4 => 'Practical Applications',
            5 => 'Final Project and Assessment',
        ];

        return $names[$number] ?? "Module {$number}";
    }
}
