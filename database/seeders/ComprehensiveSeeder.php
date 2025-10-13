<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Role;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\StudentQuizProgress;
use App\Models\StudentQuizAnswer;
use App\Models\StudentActivity;
use App\Models\CourseEnrollment;
use App\Models\Document;
use App\Models\GradeLevel;
use Carbon\Carbon;

class ComprehensiveSeeder extends Seeder
{
    public function run(): void
    {
        // Clear all existing data
        $this->command->info('Clearing existing data...');
        $this->clearExistingData();

        // Seed data in proper order (roles, grade_levels, activity_types already seeded by foundation seeders)
        $this->command->info('Seeding users...');
        $this->seedUsers();

        $this->command->info('Seeding students and instructors...');
        $this->seedStudentsAndInstructors();

        $this->command->info('Seeding courses...');
        $this->seedCourses();

        $this->command->info('Seeding modules...');
        $this->seedModules();

        $this->command->info('Seeding lessons with enhanced descriptions...');
        $this->seedLessons();

        $this->command->info('Seeding activities...');
        $this->seedActivities();

        $this->command->info('Seeding quizzes and questions...');
        $this->seedQuizzesAndQuestions();

        $this->command->info('Seeding course enrollments...');
        $this->seedCourseEnrollments();

        $this->command->info('Seeding student activities with progress...');
        $this->seedStudentActivities();

        $this->command->info('Seeding quiz progress and answers...');
        $this->seedQuizProgressAndAnswers();

        $this->command->info('Comprehensive seeding completed successfully!');
    }

    private function clearExistingData(): void
    {
        DB::statement('PRAGMA foreign_keys=OFF');
        
        // Clear in reverse dependency order (keep foundation data: roles, grade_levels, activity_types, question_types)
        DB::table('student_quiz_answers')->delete();
        DB::table('student_quiz_progress')->delete();
        DB::table('student_activities')->delete();
        DB::table('course_enrollments')->delete();
        DB::table('question_options')->delete();
        DB::table('questions')->delete();
        DB::table('quizzes')->delete();
        DB::table('module_activities')->delete();
        DB::table('lesson_module')->delete();
        DB::table('activities')->delete();
        DB::table('lessons')->delete();
        DB::table('modules')->delete();
        DB::table('courses')->delete();
        DB::table('instructors')->delete();
        DB::table('students')->delete();
        DB::table('users')->delete();

        DB::statement('PRAGMA foreign_keys=ON');
    }

    private function seedUsers(): void
    {
        // Admin users
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'id' => $i,
                'name' => "Admin User $i",
                'email' => "admin$i@example.com",
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Instructor users
        for ($i = 1; $i <= 5; $i++) {
            $userId = $i + 3;
            User::create([
                'id' => $userId,
                'name' => "Dr. Instructor $i",
                'email' => "instructor$i@example.com",
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Student users
        for ($i = 1; $i <= 15; $i++) {
            $userId = $i + 8;
            
            User::create([
                'id' => $userId,
                'name' => "Student User $i",
                'email' => "student$i@example.com",
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedStudentsAndInstructors(): void
    {
        // Create instructor records
        for ($i = 1; $i <= 5; $i++) {
            $userId = $i + 3;
            Instructor::create([
                'instructor_id' => "INST" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'user_id' => $userId,
                'employee_id' => "EMP" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'title' => fake()->randomElement(['Dr.', 'Prof.', 'Mr.', 'Ms.']),
                'department' => fake()->randomElement(['Mathematics', 'Science', 'English', 'History', 'Computer Science']),
                'specialization' => fake()->randomElement(['Algebra', 'Physics', 'Literature', 'World History', 'Programming']),
                'bio' => fake()->paragraphs(2, true),
                'office_location' => fake()->address(),
                'phone' => fake()->phoneNumber(),
                'office_hours' => 'Mon-Fri 9:00-17:00',
                'hire_date' => fake()->dateTimeBetween('-5 years', '-1 year'),
                'employment_type' => 'full-time',
                'status' => 'active',
                'salary' => fake()->numberBetween(50000, 100000),
                'education_level' => 'PhD',
                'years_experience' => rand(5, 20),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create student records
        $gradeLevels = GradeLevel::active()->get();
        
        for ($i = 1; $i <= 15; $i++) {
            $userId = $i + 8;
            $randomGradeLevel = $gradeLevels->isNotEmpty() ? $gradeLevels->random() : null;
            $gradeId = $randomGradeLevel ? $randomGradeLevel->id : null;
            $section = chr(65 + rand(0, 4)); // A-E
            
            Student::create([
                'id' => $i,
                'student_id_text' => Student::generateStudentIdText(),
                'user_id' => $userId,
                'grade_level_id' => $gradeId,
                'section' => $section,
                'enrollment_number' => "ENR-" . date('Y') . "-" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'academic_year' => '2024-2025',
                'program' => fake()->randomElement(['Computer Science', 'Mathematics', 'Physics', 'Chemistry', 'Biology']),
                'department' => fake()->randomElement(['STEM', 'Liberal Arts', 'Sciences']),
                'enrollment_date' => fake()->dateTimeBetween('-2 years', 'now'),
                'status' => 'active',
                'metadata' => [
                    'created_by_seeder' => true,
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedCourses(): void
    {
        // Get instructors
        $instructor1 = Instructor::where('user_id', 4)->first(); // First instructor (user_id = 1+3)
        $instructor2 = Instructor::where('user_id', 5)->first(); // Second instructor (user_id = 2+3)
        $instructor3 = Instructor::where('user_id', 6)->first(); // Third instructor (user_id = 3+3)
        
        $courses = [
            [
                'id' => 1,
                'name' => 'Advanced Mathematics',
                'title' => 'Advanced Mathematics and Statistics',
                'description' => 'Comprehensive mathematics course covering advanced topics including calculus, statistics, and mathematical modeling.',
                'instructor_id' => $instructor1->id, // Instructor model ID
                'created_by' => $instructor1->user_id, // User ID for created_by
                'instructor_name' => 'Dr. Instructor 1',
                'duration' => 120,
                'grade_level_id' => 15, // Grade 10 (Sophomore)
            ],
            [
                'id' => 2,
                'name' => 'Physics Fundamentals',
                'title' => 'Introduction to Physics',
                'description' => 'Fundamental physics concepts including mechanics, thermodynamics, and electromagnetic theory.',
                'instructor_id' => $instructor2->id, // Instructor model ID
                'created_by' => $instructor2->user_id, // User ID for created_by
                'instructor_name' => 'Dr. Instructor 2',
                'duration' => 90,
                'grade_level_id' => 14, // Grade 9 (Freshman)
            ],
            [
                'id' => 3,
                'name' => 'Computer Programming',
                'title' => 'Introduction to Computer Programming',
                'description' => 'Learn programming fundamentals with hands-on coding exercises and project-based learning.',
                'instructor_id' => $instructor3->id, // Instructor model ID
                'created_by' => $instructor3->user_id, // User ID for created_by
                'instructor_name' => 'Dr. Instructor 3',
                'duration' => 150,
                'grade_level_id' => 16, // Grade 11 (Junior)
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course + ['created_at' => now(), 'updated_at' => now()]);
        }
    }

    private function seedModules(): void
    {
        $modules = [
            // Advanced Mathematics modules
            [
                'id' => 1,
                'course_id' => 1,
                'name' => 'Algebra Fundamentals',
                'title' => 'Algebra Fundamentals',
                'description' => 'Basic algebraic concepts and operations',
                'sequence' => 1,
                'module_type' => 'theory',
                'completion_percentage' => 0,
                'created_by' => 4,
            ],
            [
                'id' => 2,
                'course_id' => 1,
                'name' => 'Calculus Introduction',
                'title' => 'Introduction to Calculus',
                'description' => 'Differential and integral calculus basics',
                'sequence' => 2,
                'module_type' => 'theory',
                'completion_percentage' => 0,
                'created_by' => 4,
            ],
            [
                'id' => 3,
                'course_id' => 1,
                'name' => 'Statistics and Probability',
                'title' => 'Statistics and Probability Theory',
                'description' => 'Statistical analysis and probability theory',
                'sequence' => 3,
                'module_type' => 'practical',
                'completion_percentage' => 0,
                'created_by' => 4,
            ],
            // Physics modules
            [
                'id' => 4,
                'course_id' => 2,
                'name' => 'Classical Mechanics',
                'title' => 'Classical Mechanics',
                'description' => 'Newton\'s laws and mechanical systems',
                'sequence' => 1,
                'module_type' => 'theory',
                'completion_percentage' => 0,
                'created_by' => 5,
            ],
            [
                'id' => 5,
                'course_id' => 2,
                'name' => 'Thermodynamics',
                'title' => 'Thermodynamics',
                'description' => 'Heat, energy, and thermodynamic processes',
                'sequence' => 2,
                'module_type' => 'theory',
                'completion_percentage' => 0,
                'created_by' => 5,
            ],
            // Programming modules
            [
                'id' => 6,
                'course_id' => 3,
                'name' => 'Programming Basics',
                'title' => 'Programming Fundamentals',
                'description' => 'Variables, functions, and control structures',
                'sequence' => 1,
                'module_type' => 'practical',
                'completion_percentage' => 0,
                'created_by' => 6,
            ],
            [
                'id' => 7,
                'course_id' => 3,
                'name' => 'Object-Oriented Programming',
                'title' => 'Object-Oriented Programming',
                'description' => 'Classes, objects, and OOP principles',
                'sequence' => 2,
                'module_type' => 'practical',
                'completion_percentage' => 0,
                'created_by' => 6,
            ],
        ];

        foreach ($modules as $module) {
            Module::create($module + ['created_at' => now(), 'updated_at' => now()]);
        }
    }

    private function seedLessons(): void
    {
        $loremText1 = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.\n\nExcepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.";
        
        $lessons = [
            // Algebra Fundamentals lessons
            [
                'id' => 1,
                'title' => 'Introduction to Variables',
                'description' => $loremText1,
                'course_id' => 1,
                'module_id' => 1,
                'duration' => 45,
                'order' => 1,
                'content_type' => 'video',
            ],
            [
                'id' => 2,
                'title' => 'Linear Equations',
                'description' => "Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.\n\nUt enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?",
                'course_id' => 1,
                'module_id' => 1,
                'duration' => 60,
                'order' => 2,
                'content_type' => 'video',
            ],
            [
                'id' => 3,
                'title' => 'Quadratic Functions',
                'description' => $loremText1,
                'course_id' => 1,
                'module_id' => 1,
                'duration' => 75,
                'order' => 3,
                'content_type' => 'text',
            ],
            // Calculus lessons
            [
                'id' => 4,
                'title' => 'Limits and Continuity',
                'description' => "At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.\n\nEt harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.",
                'course_id' => 1,
                'module_id' => 2,
                'duration' => 90,
                'order' => 1,
                'content_type' => 'video',
            ],
            [
                'id' => 5,
                'title' => 'Derivatives',
                'description' => $loremText1,
                'course_id' => 1,
                'module_id' => 2,
                'duration' => 80,
                'order' => 2,
                'content_type' => 'video',
            ],
            // Statistics lessons
            [
                'id' => 6,
                'title' => 'Descriptive Statistics',
                'description' => "Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.\n\nSed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.",
                'course_id' => 1,
                'module_id' => 3,
                'duration' => 70,
                'order' => 1,
                'content_type' => 'text',
            ],
            // Physics lessons
            [
                'id' => 7,
                'title' => 'Newton\'s Laws of Motion',
                'description' => $loremText1,
                'course_id' => 2,
                'module_id' => 4,
                'duration' => 85,
                'order' => 1,
                'content_type' => 'video',
            ],
            [
                'id' => 8,
                'title' => 'Force and Acceleration',
                'description' => "Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt.\n\nUt labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse.",
                'course_id' => 2,
                'module_id' => 4,
                'duration' => 75,
                'order' => 2,
                'content_type' => 'video',
            ],
            // Programming lessons
            [
                'id' => 9,
                'title' => 'Variables and Data Types',
                'description' => $loremText1,
                'course_id' => 3,
                'module_id' => 6,
                'duration' => 60,
                'order' => 1,
                'content_type' => 'video',
            ],
            [
                'id' => 10,
                'title' => 'Control Structures',
                'description' => "Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.\n\nTemporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur.",
                'course_id' => 3,
                'module_id' => 6,
                'duration' => 90,
                'order' => 2,
                'content_type' => 'video',
            ],
        ];

        foreach ($lessons as $lesson) {
            Lesson::create($lesson + ['created_at' => now(), 'updated_at' => now()]);
        }
    }

    private function seedActivities(): void
    {
        // Get activity types from database
        $quizType = DB::table('activity_types')->where('name', 'Quiz')->first();
        $assignmentType = DB::table('activity_types')->where('name', 'Assignment')->first();
        $assessmentType = DB::table('activity_types')->where('name', 'Assessment')->first();
        $exerciseType = DB::table('activity_types')->where('name', 'Exercise')->first();
        
        $activities = [
            // Math activities
            [
                'id' => 1,
                'title' => 'Algebra Basics Quiz',
                'description' => 'Test your understanding of basic algebraic concepts',
                'activity_type_id' => $quizType->id,
                'created_by' => 4,
                'passing_percentage' => 70,
                'due_date' => now()->addDays(7),
            ],
            [
                'id' => 2,
                'title' => 'Linear Equations Practice',
                'description' => 'Solve various linear equation problems',
                'activity_type_id' => $assignmentType->id,
                'created_by' => 4,
                'passing_percentage' => 75,
                'due_date' => now()->addDays(10),
            ],
            [
                'id' => 3,
                'title' => 'Calculus Fundamentals Quiz',
                'description' => 'Assessment of calculus concepts including limits and derivatives',
                'activity_type_id' => $quizType->id,
                'created_by' => 4,
                'passing_percentage' => 80,
                'due_date' => now()->addDays(5),
            ],
            [
                'id' => 4,
                'title' => 'Statistics Project',
                'description' => 'Analyze real-world data using statistical methods',
                'activity_type_id' => $assignmentType->id, // project/assignment
                'created_by' => 4,
                'passing_percentage' => 85,
                'due_date' => now()->addDays(14),
            ],
            // Physics activities
            [
                'id' => 5,
                'title' => 'Physics Mechanics Quiz',
                'description' => 'Test on Newton\'s laws and mechanical systems',
                'activity_type_id' => $quizType->id,
                'created_by' => 5,
                'passing_percentage' => 75,
                'due_date' => now()->addDays(6),
            ],
            [
                'id' => 6,
                'title' => 'Thermodynamics Assignment',
                'description' => 'Problems involving heat transfer and energy',
                'activity_type_id' => $assignmentType->id,
                'created_by' => 5,
                'passing_percentage' => 70,
                'due_date' => now()->addDays(8),
            ],
            // Programming activities
            [
                'id' => 7,
                'title' => 'Programming Basics Quiz',
                'description' => 'Quiz on variables, functions, and basic programming concepts',
                'activity_type_id' => $quizType->id,
                'created_by' => 6,
                'passing_percentage' => 80,
                'due_date' => now()->addDays(4),
            ],
            [
                'id' => 8,
                'title' => 'OOP Programming Project',
                'description' => 'Create a small application using object-oriented principles',
                'activity_type_id' => $assignmentType->id, // project/assignment
                'created_by' => 6,
                'passing_percentage' => 85,
                'due_date' => now()->addDays(21),
            ],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity + ['created_at' => now(), 'updated_at' => now()]);
        }

        // Link activities to modules
        $moduleActivities = [
            ['module_id' => 1, 'activity_id' => 1, 'order' => 1],
            ['module_id' => 1, 'activity_id' => 2, 'order' => 2],
            ['module_id' => 2, 'activity_id' => 3, 'order' => 1],
            ['module_id' => 3, 'activity_id' => 4, 'order' => 1],
            ['module_id' => 4, 'activity_id' => 5, 'order' => 1],
            ['module_id' => 5, 'activity_id' => 6, 'order' => 1],
            ['module_id' => 6, 'activity_id' => 7, 'order' => 1],
            ['module_id' => 7, 'activity_id' => 8, 'order' => 1],
        ];

        foreach ($moduleActivities as $ma) {
            DB::table('module_activities')->insert($ma + ['created_at' => now(), 'updated_at' => now()]);
        }
    }

    private function seedQuizzesAndQuestions(): void
    {
        // Create quizzes for quiz activities
        $quizActivities = [1, 3, 5, 7]; // Activities that are quizzes
        
        foreach ($quizActivities as $activityId) {
            $activity = Activity::find($activityId);
            
            $quiz = Quiz::create([
                'activity_id' => $activityId,
                'created_by' => $activity->created_by,
                'title' => $activity->title,
                'description' => $activity->description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create questions for each quiz
            $this->createQuestionsForQuiz($quiz->id, $activityId);
        }
    }

    private function createQuestionsForQuiz($quizId, $activityId): void
    {
        $questionsData = [
            1 => [ // Algebra Basics Quiz
                [
                    'question_text' => 'What is the value of x in the equation 2x + 5 = 15?',
                    'points' => 5,
                    'options' => [
                        ['option_text' => '5', 'is_correct' => true],
                        ['option_text' => '10', 'is_correct' => false],
                        ['option_text' => '7.5', 'is_correct' => false],
                        ['option_text' => '2.5', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'Simplify: 3(x + 4) - 2x',
                    'points' => 7,
                    'options' => [
                        ['option_text' => 'x + 12', 'is_correct' => true],
                        ['option_text' => '5x + 12', 'is_correct' => false],
                        ['option_text' => 'x + 4', 'is_correct' => false],
                        ['option_text' => '3x + 2', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'What is the slope of the line y = 2x - 3?',
                    'points' => 3,
                    'options' => [
                        ['option_text' => '2', 'is_correct' => true],
                        ['option_text' => '-3', 'is_correct' => false],
                        ['option_text' => '1', 'is_correct' => false],
                        ['option_text' => '0', 'is_correct' => false],
                    ],
                ],
            ],
            3 => [ // Calculus Fundamentals Quiz
                [
                    'question_text' => 'What is the derivative of x²?',
                    'points' => 8,
                    'options' => [
                        ['option_text' => '2x', 'is_correct' => true],
                        ['option_text' => 'x²', 'is_correct' => false],
                        ['option_text' => '2', 'is_correct' => false],
                        ['option_text' => 'x', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'What is the limit of (x² - 1)/(x - 1) as x approaches 1?',
                    'points' => 10,
                    'options' => [
                        ['option_text' => '2', 'is_correct' => true],
                        ['option_text' => '1', 'is_correct' => false],
                        ['option_text' => '0', 'is_correct' => false],
                        ['option_text' => 'undefined', 'is_correct' => false],
                    ],
                ],
            ],
            5 => [ // Physics Mechanics Quiz
                [
                    'question_text' => 'What is Newton\'s second law of motion?',
                    'points' => 6,
                    'options' => [
                        ['option_text' => 'F = ma', 'is_correct' => true],
                        ['option_text' => 'F = m/a', 'is_correct' => false],
                        ['option_text' => 'F = a/m', 'is_correct' => false],
                        ['option_text' => 'F = m + a', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'If an object has a mass of 10 kg and acceleration of 5 m/s², what is the force?',
                    'points' => 8,
                    'options' => [
                        ['option_text' => '50 N', 'is_correct' => true],
                        ['option_text' => '15 N', 'is_correct' => false],
                        ['option_text' => '2 N', 'is_correct' => false],
                        ['option_text' => '0.5 N', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'What is the unit of acceleration?',
                    'points' => 4,
                    'options' => [
                        ['option_text' => 'm/s²', 'is_correct' => true],
                        ['option_text' => 'm/s', 'is_correct' => false],
                        ['option_text' => 'kg⋅m/s²', 'is_correct' => false],
                        ['option_text' => 'N', 'is_correct' => false],
                    ],
                ],
            ],
            7 => [ // Programming Basics Quiz
                [
                    'question_text' => 'Which of the following is a valid variable name in most programming languages?',
                    'points' => 4,
                    'options' => [
                        ['option_text' => 'myVariable', 'is_correct' => true],
                        ['option_text' => '2variable', 'is_correct' => false],
                        ['option_text' => 'my-variable', 'is_correct' => false],
                        ['option_text' => 'class', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'What does the following code do? if (x > 0) { print("positive"); }',
                    'points' => 6,
                    'options' => [
                        ['option_text' => 'Prints "positive" if x is greater than 0', 'is_correct' => true],
                        ['option_text' => 'Always prints "positive"', 'is_correct' => false],
                        ['option_text' => 'Prints "positive" if x is 0', 'is_correct' => false],
                        ['option_text' => 'Creates a syntax error', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'What is a function in programming?',
                    'points' => 8,
                    'options' => [
                        ['option_text' => 'A reusable block of code that performs a specific task', 'is_correct' => true],
                        ['option_text' => 'A variable that stores data', 'is_correct' => false],
                        ['option_text' => 'A way to comment code', 'is_correct' => false],
                        ['option_text' => 'A type of loop', 'is_correct' => false],
                    ],
                ],
            ],
        ];

        if (isset($questionsData[$activityId])) {
            foreach ($questionsData[$activityId] as $questionData) {
                $question = Question::create([
                    'quiz_id' => $quizId,
                    'question_text' => $questionData['question_text'],
                    'question_type' => 'multiple_choice',
                    'points' => $questionData['points'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($questionData['options'] as $optionData) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $optionData['is_correct'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    private function seedCourseEnrollments(): void
    {
        // Enroll all students in all courses
        for ($studentId = 1; $studentId <= 15; $studentId++) {
            $userId = $studentId + 8; // Student user IDs start at 9
            
            for ($courseId = 1; $courseId <= 3; $courseId++) {
                CourseEnrollment::create([
                    'user_id' => $userId,
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'instructor_id' => $courseId + 3, // Instructor IDs 4, 5, 6
                    'enrolled_at' => fake()->dateTimeBetween('-3 months', '-1 month'),
                    'progress' => rand(10, 95),
                    'is_completed' => rand(0, 1),
                    'completed_at' => rand(0, 1) ? fake()->dateTimeBetween('-1 month', 'now') : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedStudentActivities(): void
    {
        // Create student activities for each enrolled student
        for ($studentId = 1; $studentId <= 15; $studentId++) {
            // Math course activities (Course 1, Modules 1-3)
            $this->createStudentActivity($studentId, 1, 1, 1, 'quiz'); // Algebra Quiz
            $this->createStudentActivity($studentId, 1, 1, 2, 'assignment'); // Linear Equations
            $this->createStudentActivity($studentId, 1, 2, 3, 'quiz'); // Calculus Quiz
            $this->createStudentActivity($studentId, 1, 3, 4, 'project'); // Statistics Project
            
            // Physics course activities (Course 2, Modules 4-5)
            $this->createStudentActivity($studentId, 2, 4, 5, 'quiz'); // Physics Quiz
            $this->createStudentActivity($studentId, 2, 5, 6, 'assignment'); // Thermodynamics
            
            // Programming course activities (Course 3, Modules 6-7)
            $this->createStudentActivity($studentId, 3, 6, 7, 'quiz'); // Programming Quiz
            $this->createStudentActivity($studentId, 3, 7, 8, 'project'); // OOP Project
        }
    }

    private function createStudentActivity($studentId, $courseId, $moduleId, $activityId, $activityType): void
    {
        $statuses = ['not_started', 'in_progress', 'completed', 'submitted'];
        $status = $statuses[array_rand($statuses)];
        
        $maxScore = rand(80, 100);
        $score = null;
        $percentageScore = null;
        $startedAt = null;
        $completedAt = null;
        $submittedAt = null;
        
        if ($status !== 'not_started') {
            $startedAt = fake()->dateTimeBetween('-2 months', '-1 week');
            $score = rand(60, $maxScore);
            $percentageScore = ($score / $maxScore) * 100;
            
            if (in_array($status, ['completed', 'submitted'])) {
                $completedAt = fake()->dateTimeBetween($startedAt, 'now');
                if ($status === 'submitted') {
                    $submittedAt = $completedAt;
                }
            }
        }

        StudentActivity::create([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'module_id' => $moduleId,
            'activity_id' => $activityId,
            'activity_type' => $activityType,
            'status' => $status,
            'score' => $score,
            'max_score' => $maxScore,
            'percentage_score' => $percentageScore,
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
            'submitted_at' => $submittedAt,
            'graded_at' => $submittedAt ? fake()->dateTimeBetween($submittedAt, 'now') : null,
            'progress_data' => json_encode(['progress' => rand(0, 100)]),
            'feedback' => rand(0, 1) ? fake()->sentence(10) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedQuizProgressAndAnswers(): void
    {
        $quizActivities = [1, 3, 5, 7]; // Quiz activity IDs
        
        // Create quiz progress for students who have started/completed quiz activities
        for ($studentId = 1; $studentId <= 15; $studentId++) {
            foreach ($quizActivities as $activityId) {
                $studentActivity = StudentActivity::where('student_id', $studentId)
                    ->where('activity_id', $activityId)
                    ->first();
                
                if ($studentActivity && $studentActivity->status !== 'not_started') {
                    $this->createQuizProgress($studentId, $activityId, $studentActivity);
                }
            }
        }
    }

    private function createQuizProgress($studentId, $activityId, $studentActivity): void
    {
        $quiz = Quiz::where('activity_id', $activityId)->first();
        if (!$quiz) return;

        $questions = Question::where('quiz_id', $quiz->id)->get();
        if ($questions->isEmpty()) return;

        // Create or update quiz progress (only one record per student per quiz per activity)
        $isCompleted = in_array($studentActivity->status, ['completed', 'submitted']);
        
        $startedAt = $studentActivity->started_at 
            ? Carbon::parse($studentActivity->started_at)
            : fake()->dateTimeBetween('-1 month', '-1 week');
        
        $progress = StudentQuizProgress::updateOrCreate(
            [
                'student_id' => $studentId,
                'quiz_id' => $quiz->id,
                'activity_id' => $activityId,
            ],
            [
                'started_at' => $startedAt,
                'last_accessed_at' => $isCompleted 
                    ? fake()->dateTimeBetween($startedAt, 'now')
                    : fake()->dateTimeBetween($startedAt, 'now'),
                'is_completed' => $isCompleted,
                'is_submitted' => $isCompleted,
                'completed_questions' => $isCompleted ? $questions->count() : rand(0, $questions->count()),
                'total_questions' => $questions->count(),
                'time_spent' => rand(10, 45) * 60, // 10-45 minutes in seconds
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

            $totalScore = 0;
            $totalPossible = $questions->sum('points');

        // Create or update answers for this quiz
        foreach ($questions as $question) {
            $options = QuestionOption::where('question_id', $question->id)->get();
            $selectedOption = $options->random();
            
            $isCorrect = $selectedOption->is_correct;
            $pointsEarned = $isCorrect ? $question->points : 0;
            $totalScore += $pointsEarned;

            StudentQuizAnswer::updateOrCreate(
                [
                    'quiz_progress_id' => $progress->id,
                    'question_id' => $question->id,
                ],
                [
                    'student_id' => $studentId,
                    'selected_option_id' => $selectedOption->id,
                    'answer_text' => $selectedOption->option_text, // Populate answer_text from option
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                    'answered_at' => fake()->dateTimeBetween($startedAt, $startedAt->copy()->addHour()),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Update progress with calculated scores
        $percentageScore = $totalPossible > 0 ? ($totalScore / $totalPossible) * 100 : 0;
        
        $progress->update([
            'score' => $totalScore,
            'percentage_score' => $percentageScore,
        ]);
    }
}