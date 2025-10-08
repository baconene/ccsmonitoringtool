<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Course;
use App\Models\Module;
use App\Models\CourseEnrollment;
use App\Models\GradeLevel;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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

        // Disable foreign key checks to avoid constraint errors
        DB::statement('PRAGMA foreign_keys = OFF');

        try {
            // Call seeders in order
            $this->call([
                RoleSeeder::class,
                ActivityTypeSeeder::class,
                GradeLevelSeeder::class,
            ]);

            // Get roles
            $adminRole = Role::where('name', 'admin')->first();
            $instructorRole = Role::where('name', 'instructor')->first();
            $studentRole = Role::where('name', 'student')->first();

            if (!$adminRole || !$instructorRole || !$studentRole) {
                echo "❌ Error: Roles not found. Please ensure RoleSeeder ran successfully.\n";
                DB::statement('PRAGMA foreign_keys = ON');
                return;
            }
        } catch (\Exception $e) {
            echo "❌ Error creating roles: " . $e->getMessage() . "\n";
            DB::statement('PRAGMA foreign_keys = ON');
            return;
        }

        // Create Admin users (1-10@test.com)
        echo "\n👤 Creating Admin Users...\n";
        $admins = [];
        for ($i = 1; $i <= 10; $i++) {
            try {
                $admin = User::updateOrCreate(
                    ['email' => "admin{$i}@test.com"],
                    [
                        'name' => "Admin {$i}",
                        'email_verified_at' => now(),
                        'password' => Hash::make('12345678'),
                        'role_id' => $adminRole->id,
                    ]
                );
                $admins[] = $admin;
                echo "✓ Created/Updated Admin: {$admin->email}\n";
            } catch (\Exception $e) {
                echo "⚠️  Skipped admin{$i}: " . $e->getMessage() . "\n";
                continue;
            }
        }

        // Create Instructor users (1-10@test.com)
        echo "\n👨‍🏫 Creating Instructor Users...\n";
        $instructors = [];
        for ($i = 1; $i <= 10; $i++) {
            try {
                $instructor = User::updateOrCreate(
                    ['email' => "instructor{$i}@test.com"],
                    [
                        'name' => "Instructor {$i}",
                        'email_verified_at' => now(),
                        'password' => Hash::make('12345678'),
                        'role_id' => $instructorRole->id,
                    ]
                );
                $instructors[] = $instructor;
                echo "✓ Created/Updated Instructor: {$instructor->email}\n";
            } catch (\Exception $e) {
                echo "⚠️  Skipped instructor{$i}: " . $e->getMessage() . "\n";
                continue;
            }
        }

        // Create Student users (1-10@test.com)
        echo "\n🎓 Creating Student Users...\n";
        $students = [];
        $allGradeLevels = GradeLevel::where('is_active', true)->pluck('name')->toArray();
        $sections = ['Section A', 'Section B', 'Section C'];
        
        if (empty($allGradeLevels)) {
            echo "⚠️  Warning: No grade levels found. Skipping student creation.\n";
        } else {
            for ($i = 1; $i <= 10; $i++) {
                try {
                    $randomGradeLevel = $allGradeLevels[array_rand($allGradeLevels)];
                    $randomSection = $sections[array_rand($sections)];
                    
                    $student = User::updateOrCreate(
                        ['email' => "student{$i}@test.com"],
                        [
                            'name' => "Student {$i}",
                            'email_verified_at' => now(),
                            'password' => Hash::make('12345678'),
                            'role_id' => $studentRole->id,
                            'grade_level' => $randomGradeLevel,
                            'section' => $randomSection,
                        ]
                    );
                    $students[] = $student;
                    echo "✓ Created/Updated Student: {$student->email} ({$student->grade_level}, {$student->section})\n";
                } catch (\Exception $e) {
                    echo "⚠️  Skipped student{$i}: " . $e->getMessage() . "\n";
                    continue;
                }
            }
        }

        // Create Student records for student users
        $this->call(StudentSeeder::class);

        // Create Instructor records for instructor/admin users
        $this->call(InstructorSeeder::class);

        // Create Courses with modules and activities
        echo "\n📚 Creating Courses with Modules and Activities...\n";
        
        // Get quiz activity type
        $quizActivityType = ActivityType::where('name', 'Quiz')->first();
        
        if (!$quizActivityType) {
            echo "⚠️  Quiz activity type not found. Skipping quiz creation.\n";
        }

        $courseData = [
            ['name' => 'Introduction to Programming', 'description' => 'Learn the basics of programming', 'grade_level' => 'Grade 9'],
            ['name' => 'Advanced Mathematics', 'description' => 'Advanced math concepts', 'grade_level' => 'Grade 10'],
            ['name' => 'Physics 101', 'description' => 'Introduction to Physics', 'grade_level' => 'Grade 10'],
            ['name' => 'Chemistry Fundamentals', 'description' => 'Basic chemistry principles', 'grade_level' => 'Grade 9'],
            ['name' => 'World History', 'description' => 'Explore world history', 'grade_level' => 'Grade 11'],
        ];

        foreach ($courseData as $index => $data) {
            try {
                // Assign instructor (distribute courses among instructors)
                if (empty($instructors)) {
                    echo "⚠️  No instructors available. Skipping course creation.\n";
                    break;
                }
                
                $instructor = $instructors[$index % count($instructors)];

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

                echo "✓ Created/Updated Course: {$course->name} (Instructor: {$instructor->name})\n";

                // Create 3-4 modules for each course
                $moduleCount = rand(3, 4);
                for ($m = 1; $m <= $moduleCount; $m++) {
                    try {
                        $moduleName = "Module {$m}: " . $this->getModuleName($m);
                        $module = Module::updateOrCreate(
                            [
                                'course_id' => $course->id,
                                'sequence' => $m,
                            ],
                            [
                                'name' => $moduleName,
                                'description' => "This is module {$m} for {$course->name}",
                                'module_percentage' => 100 / $moduleCount, // Equal weight distribution
                            ]
                        );
                        echo "  ↳ Module: {$module->name} (Weight: {$module->module_percentage}%)\n";
                        
                        // Create a quiz activity for the first module
                        if ($m === 1 && $quizActivityType) {
                            try {
                                $this->createQuizActivity($course, $module, $instructor, $quizActivityType);
                            } catch (\Exception $e) {
                                echo "    ⚠️  Failed to create quiz: " . $e->getMessage() . "\n";
                            }
                        }
                    } catch (\Exception $e) {
                        echo "  ⚠️  Skipped module {$m}: " . $e->getMessage() . "\n";
                        continue;
                    }
                }

                // Enroll students to courses based on grade level
                $eligibleStudents = array_filter($students, function($student) use ($course) {
                    return $student->grade_level === $course->grade_level;
                });

                // If no exact match, enroll some random students
                if (empty($eligibleStudents) && !empty($students)) {
                    $eligibleStudents = array_slice($students, 0, rand(2, 4));
                }

                foreach ($eligibleStudents as $student) {
                    try {
                        $enrollment = CourseEnrollment::updateOrCreate(
                            [
                                'user_id' => $student->id,
                                'course_id' => $course->id,
                            ],
                            [
                                'enrolled_at' => now(),
                                'progress' => 0,
                                'is_completed' => false,
                            ]
                        );
                    } catch (\Exception $e) {
                        echo "  ⚠️  Failed to enroll {$student->name}: " . $e->getMessage() . "\n";
                        continue;
                    }
                }
                
                $enrolledCount = count($eligibleStudents);
                echo "  ↳ Enrolled {$enrolledCount} student(s)\n";
                
            } catch (\Exception $e) {
                echo "⚠️  Skipped course '{$data['name']}': " . $e->getMessage() . "\n";
                continue;
            }
        }

        // Create student activity data
        $this->call(StudentActivitySeeder::class);

        // Re-enable foreign key checks
        DB::statement('PRAGMA foreign_keys = ON');

        echo "\n════════════════════════════════════════\n";
        echo "✅ Database seeded successfully!\n";
        echo "════════════════════════════════════════\n\n";
        
        echo "� INITIAL CREDENTIALS:\n";
        echo "────────────────────────────────────────\n";
        echo "�📧 Admins:       admin1-10@test.com\n";
        echo "👨‍🏫 Instructors: instructor1-10@test.com\n";
        echo "🎓 Students:    student1-10@test.com\n";
        echo "🔑 Password:    12345678 (for all users)\n\n";
        
        echo "📊 DATABASE SUMMARY:\n";
        echo "────────────────────────────────────────\n";
        echo "Users:          " . User::count() . " total\n";
        echo "Courses:        " . Course::count() . " courses\n";
        echo "Modules:        " . Module::count() . " modules\n";
        echo "Activities:     " . Activity::count() . " activities\n";
        echo "Quizzes:        " . Quiz::count() . " quizzes\n";
        echo "Grade Levels:   " . GradeLevel::count() . " levels\n";
        echo "Enrollments:    " . CourseEnrollment::count() . " enrollments\n";
        echo "════════════════════════════════════════\n";
        echo "💡 Tip: Run 'php artisan migrate:fresh --seed' for fresh DB\n";
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
    
    private function createQuizActivity($course, $module, $instructor, $quizActivityType): void
    {
        // Create quiz activity
        $activity = Activity::updateOrCreate(
            [
                'title' => "{$course->name} - Module {$module->sequence} Quiz",
                'created_by' => $instructor->id,
            ],
            [
                'description' => "Quiz to test your understanding of {$module->name}",
                'activity_type_id' => $quizActivityType->id,
            ]
        );

        // Update activity with passing percentage
        $activity->update(['passing_percentage' => 70]);
        
        // Create quiz
        $quiz = Quiz::updateOrCreate(
            [
                'activity_id' => $activity->id,
            ],
            [
                'created_by' => $instructor->id,
                'title' => $activity->title,
                'description' => $activity->description,
            ]
        );

        // Create sample questions
        $questions = [
            [
                'question_text' => 'What is the main topic of this module?',
                'question_type' => 'multiple-choice',
                'points' => 10,
                'options' => [
                    ['option_text' => 'Core concepts and fundamentals', 'is_correct' => true],
                    ['option_text' => 'Advanced applications', 'is_correct' => false],
                    ['option_text' => 'Final assessments', 'is_correct' => false],
                    ['option_text' => 'Introduction only', 'is_correct' => false],
                ]
            ],
            [
                'question_text' => 'This module builds upon previous knowledge.',
                'question_type' => 'true-false',
                'points' => 5,
                'options' => [
                    ['option_text' => 'True', 'is_correct' => true],
                    ['option_text' => 'False', 'is_correct' => false],
                ]
            ],
        ];

        foreach ($questions as $questionData) {
            $question = Question::updateOrCreate(
                [
                    'quiz_id' => $quiz->id,
                    'question_text' => $questionData['question_text'],
                ],
                [
                    'question_type' => $questionData['question_type'],
                    'points' => $questionData['points'],
                ]
            );

            foreach ($questionData['options'] as $optionData) {
                QuestionOption::updateOrCreate(
                    [
                        'question_id' => $question->id,
                        'option_text' => $optionData['option_text'],
                    ],
                    [
                        'is_correct' => $optionData['is_correct'],
                    ]
                );
            }
        }

        // Attach activity to module
        try {
            DB::table('module_activities')->updateOrInsert(
                [
                    'activity_id' => $activity->id,
                    'module_id' => $module->id,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            echo "    ✓ Quiz Activity: {$activity->title} (Linked to {$module->name})\n";
        } catch (\Exception $e) {
            echo "    ⚠️  Failed to attach quiz to module: " . $e->getMessage() . "\n";
        }
    }
}
