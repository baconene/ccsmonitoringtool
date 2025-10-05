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
        echo "\n════════════════════════════════════════\n";
        echo "🌱 Starting Database Seeding...\n";
        echo "════════════════════════════════════════\n\n";

        try {
            // Call RoleSeeder first to create roles
            $this->call([
                RoleSeeder::class,
            ]);

            // Get roles
            $adminRole = Role::where('name', 'admin')->first();
            $instructorRole = Role::where('name', 'instructor')->first();
            $studentRole = Role::where('name', 'student')->first();

            if (!$adminRole || !$instructorRole || !$studentRole) {
                echo "❌ Error: Roles not found. Please ensure RoleSeeder ran successfully.\n";
                return;
            }
        } catch (\Exception $e) {
            echo "❌ Error creating roles: " . $e->getMessage() . "\n";
            return;
        }

        try {
            // Create 1 Admin (or update if exists)
            $admin = User::updateOrCreate(
                ['email' => 'test.admin@test.com'],
                [
                    'name' => 'Test Admin',
                    'email_verified_at' => now(),
                    'password' => Hash::make('12345678'),
                    'role_id' => $adminRole->id,
                ]
            );
            echo "✓ Created/Updated Admin: {$admin->email}\n";
        } catch (\Exception $e) {
            echo "❌ Error creating admin: " . $e->getMessage() . "\n";
        }

        // Create 4 Instructors (or update if exists)
        $instructors = [];
        $instructorNames = ['Dr. Smith', 'Prof. Johnson', 'Dr. Williams', 'Prof. Brown'];
        
        foreach ($instructorNames as $index => $name) {
            try {
                $instructor = User::updateOrCreate(
                    ['email' => 'instructor' . ($index + 1) . '@test.com'],
                    [
                        'name' => $name,
                        'email_verified_at' => now(),
                        'password' => Hash::make('12345678'),
                        'role_id' => $instructorRole->id,
                    ]
                );
                $instructors[] = $instructor;
                echo "✓ Created/Updated Instructor: {$instructor->email}\n";
            } catch (\Exception $e) {
                echo "❌ Error creating instructor {$name}: " . $e->getMessage() . "\n";
            }
        }

        // Create 10 Students with grade levels (or update if exists)
        $students = [];
        $gradeLevels = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        $sections = ['Section A', 'Section B', 'Section C'];
        
        for ($i = 1; $i <= 10; $i++) {
            try {
                $student = User::updateOrCreate(
                    ['email' => 'student' . $i . '@test.com'],
                    [
                        'name' => 'Student ' . $i,
                        'email_verified_at' => now(),
                        'password' => Hash::make('12345678'),
                        'role_id' => $studentRole->id,
                        'grade_level' => $gradeLevels[array_rand($gradeLevels)],
                        'section' => $sections[array_rand($sections)],
                    ]
                );
                $students[] = $student;
                echo "✓ Created/Updated Student: {$student->email} ({$student->grade_level}, {$student->section})\n";
            } catch (\Exception $e) {
                echo "❌ Error creating student {$i}: " . $e->getMessage() . "\n";
            }
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

            $course = Course::updateOrCreate(
                [
                    'name' => $data['name'],
                    'instructor_id' => $instructor->id,
                ],
                [
                    'title' => $data['name'],
                    'description' => $data['description'],
                    'grade_level' => $data['grade_level'],
                ]
            );

            echo "✓ Created/Updated Course: {$course->name} (Instructor: {$instructor->name}, Grade: {$course->grade_level})\n";

            // Create 3-5 modules for each course (or update if exists)
            $moduleCount = rand(3, 5);
            for ($m = 1; $m <= $moduleCount; $m++) {
                $moduleName = "Module {$m}: " . $this->getModuleName($m);
                $module = Module::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'sequence' => $m,
                    ],
                    [
                        'name' => $moduleName,
                        'description' => "This is module {$m} for {$course->name}",
                    ]
                );
                echo "  ↳ Created/Updated Module: {$module->name}\n";
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
                $enrollment = CourseEnrollment::updateOrCreate(
                    [
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'enrolled_at' => now(),
                        'progress' => rand(0, 100),
                        'is_completed' => false,
                    ]
                );
                echo "  ↳ Enrolled/Updated: {$student->name}\n";
            }
        }

        echo "\n════════════════════════════════════════\n";
        echo "✅ Database seeded successfully!\n";
        echo "════════════════════════════════════════\n";
        echo "📧 Admin:       test.admin@test.com / 12345678\n";
        echo "👨‍🏫 Instructors: instructor1-4@test.com / 12345678\n";
        echo "🎓 Students:    student1-10@test.com / 12345678\n";
        echo "════════════════════════════════════════\n";
        echo "💡 Tip: Run 'php artisan migrate:fresh --seed' to reset\n";
        echo "════════════════════════════════════════\n\n";
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
