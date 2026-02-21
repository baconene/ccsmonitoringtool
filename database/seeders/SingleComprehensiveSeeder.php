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
use App\Models\Assignment;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentQuestionOption;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionType;
use App\Models\AssignmentType;
use App\Models\StudentActivityProgress;
use App\Models\StudentQuizAnswer;
use App\Models\StudentAssignmentAnswer;
use App\Models\StudentActivity;
use App\Models\CourseEnrollment;
use App\Models\GradeLevel;
use App\Enums\ScheduleTypeEnum;
use Carbon\Carbon;
use Database\Seeders\SkillSeeder;
use Database\Seeders\StudentSkillAssessmentSeeder;

class SingleComprehensiveSeeder extends Seeder
{
    /**
     * Get faker instance
     */
    private function faker()
    {
        return \Illuminate\Container\Container::getInstance()->make(\Faker\Generator::class);
    }

    public function run(): void
    {
        $this->command->info('🌱 Starting comprehensive database seeding...');
        
        // Clear all existing data
        $this->command->info('Clearing existing data...');
        $this->clearExistingData();

        // 1. Foundation Data
        $this->command->info('Seeding foundation data...');
        $this->seedRoles();
        $this->seedGradeLevels();
        $this->seedActivityTypes();
        $this->seedScheduleTypes();
        
        $this->command->info('✅ Foundation data seeded successfully!');

        // 2. Users and Related Data
        $this->command->info('Seeding users...');
        $this->seedUsers();

        $this->command->info('Seeding students and instructors...');
        $this->seedStudentsAndInstructors();

        // 3. Course Content
        $this->command->info('Seeding courses...');
        $this->seedCourses();

        $this->command->info('Seeding modules...');
        $this->seedModules();

        $this->command->info('Seeding lessons...');
        $this->seedLessons();

        $this->command->info('Seeding activities...');
        $this->seedActivities();

        $this->command->info('Seeding skills and linking activities to skills...');
        $this->call(SkillSeeder::class);

        $this->command->info('Seeding quizzes and questions...');
        $this->seedQuizzesAndQuestions();

        $this->command->info('Seeding assignments and questions...');
        $this->seedAssignmentsAndQuestions();

        // 4. Enrollments and Progress
        $this->command->info('Seeding course enrollments...');
        $this->seedCourseEnrollments();

        $this->command->info('Seeding student activities with progress...');
        $this->seedStudentActivities();

        $this->command->info('Seeding quiz progress and answers...');
        $this->seedQuizProgressAndAnswers();

        $this->command->info('Seeding assignment progress and answers...');
        $this->seedAssignmentProgressAndAnswers();

        $this->command->info('Recalculating course completion statuses...');
        $this->recalculateCourseCompletions();

        // 5. Skill Assessments
        $this->command->info('Seeding student skill assessments...');
        $this->call(StudentSkillAssessmentSeeder::class);

        $this->command->info('🎉 Comprehensive database seeding completed successfully!');
    }

    private function clearExistingData(): void
    {
        DB::statement('PRAGMA foreign_keys=OFF');
        
        // Clear in reverse dependency order
        DB::table('student_assignment_answers')->delete();
        DB::table('student_quiz_answers')->delete();
        DB::table('student_activity_progress')->delete();
        DB::table('student_activities')->delete();
        DB::table('student_skill_assessments')->delete();
        DB::table('schedule_participants')->delete();
        DB::table('schedules')->delete();
        DB::table('course_student')->delete();
        DB::table('course_enrollments')->delete();
        DB::table('assignment_question_options')->delete();
        DB::table('assignment_questions')->delete();
        DB::table('assignments')->delete();
        DB::table('question_options')->delete();
        DB::table('questions')->delete();
        DB::table('quizzes')->delete();
        DB::table('skill_activities')->delete();
        DB::table('module_activities')->delete();
        DB::table('lesson_module')->delete();
        DB::table('skills')->delete();
        DB::table('activities')->delete();
        DB::table('lessons')->delete();
        DB::table('modules')->delete();
        DB::table('courses')->delete();
        DB::table('instructors')->delete();
        DB::table('students')->delete();
        DB::table('users')->delete();
        
        // Clear foundation data
        DB::table('assignment_types')->delete();
        DB::table('question_types')->delete();
        DB::table('schedule_types')->delete();
        DB::table('activity_types')->delete();
        DB::table('grade_levels')->delete();
        DB::table('roles')->delete();

        // Reset auto-increment sequences for SQLite
        DB::table('sqlite_sequence')->delete();

        DB::statement('PRAGMA foreign_keys=ON');
    }

    // ===== FOUNDATION DATA SEEDERS =====

    private function seedRoles(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'System administrator with full access to all features',
                'is_active' => true,
            ],
            [
                'name' => 'instructor',
                'display_name' => 'Instructor',
                'description' => 'Course instructor who can manage courses, assignments, and students',
                'is_active' => true,
            ],
            [
                'name' => 'student',
                'display_name' => 'Student',
                'description' => 'Student who can access courses and complete assignments',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(['name' => $roleData['name']], $roleData);
            $this->command->info("✓ Role: {$roleData['name']}");
        }
    }

    private function seedGradeLevels(): void
    {
        // Year Levels (1-5) - Primary
        $yearLevels = [
            ['name' => 'Year 1', 'display_name' => 'Year 1 (Primary)', 'level' => 1, 'is_active' => true],
            ['name' => 'Year 2', 'display_name' => 'Year 2 (Primary)', 'level' => 2, 'is_active' => true],
            ['name' => 'Year 3', 'display_name' => 'Year 3 (Primary)', 'level' => 3, 'is_active' => true],
            ['name' => 'Year 4', 'display_name' => 'Year 4 (Primary)', 'level' => 4, 'is_active' => true],
            ['name' => 'Year 5', 'display_name' => 'Year 5 (Primary)', 'level' => 5, 'is_active' => true],
        ];

        // Grade Levels (1-12) - Standard K-12
        $gradeLevels = [
            // Elementary
            ['name' => 'Grade 1', 'display_name' => 'Grade 1 (Elementary)', 'level' => 6, 'is_active' => true],
            ['name' => 'Grade 2', 'display_name' => 'Grade 2 (Elementary)', 'level' => 7, 'is_active' => true],
            ['name' => 'Grade 3', 'display_name' => 'Grade 3 (Elementary)', 'level' => 8, 'is_active' => true],
            ['name' => 'Grade 4', 'display_name' => 'Grade 4 (Elementary)', 'level' => 9, 'is_active' => true],
            ['name' => 'Grade 5', 'display_name' => 'Grade 5 (Elementary)', 'level' => 10, 'is_active' => true],
            ['name' => 'Grade 6', 'display_name' => 'Grade 6 (Elementary)', 'level' => 11, 'is_active' => true],
            // Middle School
            ['name' => 'Grade 7', 'display_name' => 'Grade 7 (Middle School)', 'level' => 12, 'is_active' => true],
            ['name' => 'Grade 8', 'display_name' => 'Grade 8 (Middle School)', 'level' => 13, 'is_active' => true],
            // High School
            ['name' => 'Grade 9', 'display_name' => 'Grade 9 (Freshman)', 'level' => 14, 'is_active' => true],
            ['name' => 'Grade 10', 'display_name' => 'Grade 10 (Sophomore)', 'level' => 15, 'is_active' => true],
            ['name' => 'Grade 11', 'display_name' => 'Grade 11 (Junior)', 'level' => 16, 'is_active' => true],
            ['name' => 'Grade 12', 'display_name' => 'Grade 12 (Senior)', 'level' => 17, 'is_active' => true],
        ];

        foreach (array_merge($yearLevels, $gradeLevels) as $levelData) {
            GradeLevel::updateOrCreate(['name' => $levelData['name']], $levelData);
            $this->command->info("✓ Grade Level: {$levelData['name']}");
        }
    }

    private function seedActivityTypes(): void
    {
        // Activity Types with model class paths
        $activityTypes = [
            [
                'name' => 'Quiz',
                'description' => 'Interactive quiz with multiple questions for knowledge assessment',
                'model' => 'App\\Models\\Quiz'
            ],
            [
                'name' => 'Assignment',
                'description' => 'Assignment with document submission and grading',
                'model' => 'App\\Models\\Assignment'
            ],
            [
                'name' => 'Assessment',
                'description' => 'Comprehensive assessment to evaluate student competency',
                'model' => 'App\\Models\\Assessment'
            ],
            [
                'name' => 'Exercise',
                'description' => 'Practice exercise for skill development and reinforcement',
                'model' => 'App\\Models\\Exercise'
            ],
        ];

        foreach ($activityTypes as $type) {
            ActivityType::updateOrCreate(['name' => $type['name']], $type);
            $this->command->info("✓ Activity Type: {$type['name']}");
        }

        // Question Types
        $questionTypes = [
            ['type' => 'multiple-choice', 'description' => 'Question with multiple answer options'],
            ['type' => 'true-false', 'description' => 'True or False question'],
            ['type' => 'short-answer', 'description' => 'Short text answer question'],
            ['type' => 'enumeration', 'description' => 'List-based answer question'],
        ];

        foreach ($questionTypes as $type) {
            QuestionType::updateOrCreate(['type' => $type['type']], $type);
            $this->command->info("✓ Question Type: {$type['type']}");
        }

        // Assignment Types
        $assignmentTypes = [
            ['type' => 'homework'],
            ['type' => 'project'],
            ['type' => 'essay'],
            ['type' => 'research'],
        ];

        foreach ($assignmentTypes as $type) {
            AssignmentType::updateOrCreate(['type' => $type['type']], $type);
            $this->command->info("✓ Assignment Type: {$type['type']}");
        }
    }

    private function seedScheduleTypes(): void
    {
        foreach (ScheduleTypeEnum::cases() as $type) {
            $data = $type->toSeederArray();
            
            $exists = DB::table('schedule_types')->where('name', $type->value)->exists();
            
            if ($exists) {
                DB::table('schedule_types')
                    ->where('name', $type->value)
                    ->update([
                        'description' => $data['description'],
                        'color' => $data['color'],
                        'icon' => $data['icon'],
                        'is_active' => $data['is_active'],
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('schedule_types')->insert($data);
            }
            
            $this->command->info("✓ Schedule Type: {$type->label()}");
        }
    }

    // ===== USER DATA SEEDERS =====

    private function seedUsers(): void
    {
        // Admin users (3)
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

        // Instructor users (5)
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

        // Student users (15)
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
        // Create instructor records (5)
        for ($i = 1; $i <= 5; $i++) {
            $userId = $i + 3;
            Instructor::create([
                'instructor_id' => "INST" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'user_id' => $userId,
                'employee_id' => "EMP" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'title' => $this->faker()->randomElement(['Dr.', 'Prof.', 'Mr.', 'Ms.']),
                'department' => $this->faker()->randomElement(['Mathematics', 'Science', 'English', 'History', 'Computer Science']),
                'specialization' => $this->faker()->randomElement(['Algebra', 'Physics', 'Literature', 'World History', 'Programming']),
                'bio' => $this->faker()->paragraphs(2, true),
                'office_location' => $this->faker()->address(),
                'phone' => $this->faker()->phoneNumber(),
                'office_hours' => 'Mon-Fri 9:00-17:00',
                'hire_date' => $this->faker()->dateTimeBetween('-5 years', '-1 year'),
                'employment_type' => 'full-time',
                'status' => 'active',
                'salary' => $this->faker()->numberBetween(50000, 100000),
                'education_level' => 'PhD',
                'years_experience' => rand(5, 20),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create student records (15)
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
                'program' => $this->faker()->randomElement(['Computer Science', 'Mathematics', 'Physics', 'Chemistry', 'Biology']),
                'department' => $this->faker()->randomElement(['STEM', 'Liberal Arts', 'Sciences']),
                'enrollment_date' => $this->faker()->dateTimeBetween('-2 years', 'now'),
                'status' => 'active',
                'metadata' => ['created_by_seeder' => true],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // ===== COURSE CONTENT SEEDERS =====

    private function seedCourses(): void 
    {
        $instructor1 = Instructor::where('user_id', 4)->first();
        $instructor2 = Instructor::where('user_id', 5)->first();
        $instructor3 = Instructor::where('user_id', 6)->first();
        
        $courses = [
            [
                'id' => 1,
                'name' => 'Advanced Mathematics',
                'title' => 'Advanced Mathematics and Statistics',
                'description' => 'Comprehensive mathematics course covering advanced topics including calculus, statistics, and mathematical modeling.',
                'instructor_id' => $instructor1->id,
                'created_by' => $instructor1->user_id,
                'instructor_name' => 'Dr. Instructor 1',
                'duration' => 120,
                'grade_level_id' => 15,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonths(4)->toDateString(),
            ],
            [
                'id' => 2,
                'name' => 'Physics Fundamentals',
                'title' => 'Introduction to Physics',
                'description' => 'Fundamental physics concepts including mechanics, thermodynamics, and electromagnetic theory.',
                'instructor_id' => $instructor2->id,
                'created_by' => $instructor2->user_id,
                'instructor_name' => 'Dr. Instructor 2',
                'duration' => 90,
                'grade_level_id' => 14,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonths(3)->toDateString(),
            ],
            [
                'id' => 3,
                'name' => 'Computer Programming',
                'title' => 'Introduction to Computer Programming',
                'description' => 'Learn programming fundamentals with hands-on coding exercises and project-based learning.',
                'instructor_id' => $instructor3->id,
                'created_by' => $instructor3->user_id,
                'instructor_name' => 'Dr. Instructor 3',
                'duration' => 150,
                'grade_level_id' => 16,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonths(5)->toDateString(),
            ],
        ];

        foreach ($courses as $courseData) {
            $course = Course::create($courseData + ['created_at' => now(), 'updated_at' => now()]);
            $this->createCourseSchedule($course);
        }
    }

    private function seedModules(): void
    {
        $modules = [
            // Math modules (Course 1)
            ['id' => 1, 'course_id' => 1, 'name' => 'Algebra Fundamentals', 'title' => 'Algebra Fundamentals', 'description' => 'Basic algebraic concepts and operations', 'sequence' => 1, 'module_type' => 'theory', 'completion_percentage' => 0, 'created_by' => 4],
            ['id' => 2, 'course_id' => 1, 'name' => 'Calculus Introduction', 'title' => 'Introduction to Calculus', 'description' => 'Differential and integral calculus basics', 'sequence' => 2, 'module_type' => 'theory', 'completion_percentage' => 0, 'created_by' => 4],
            ['id' => 3, 'course_id' => 1, 'name' => 'Statistics and Probability', 'title' => 'Statistics and Probability Theory', 'description' => 'Statistical analysis and probability theory', 'sequence' => 3, 'module_type' => 'practical', 'completion_percentage' => 0, 'created_by' => 4],
            
            // Physics modules (Course 2)
            ['id' => 4, 'course_id' => 2, 'name' => 'Classical Mechanics', 'title' => 'Classical Mechanics', 'description' => 'Newton\'s laws and mechanical systems', 'sequence' => 1, 'module_type' => 'theory', 'completion_percentage' => 0, 'created_by' => 5],
            ['id' => 5, 'course_id' => 2, 'name' => 'Thermodynamics', 'title' => 'Thermodynamics', 'description' => 'Heat, energy, and thermodynamic processes', 'sequence' => 2, 'module_type' => 'theory', 'completion_percentage' => 0, 'created_by' => 5],
            
            // Programming modules (Course 3)
            ['id' => 6, 'course_id' => 3, 'name' => 'Programming Basics', 'title' => 'Programming Fundamentals', 'description' => 'Variables, functions, and control structures', 'sequence' => 1, 'module_type' => 'practical', 'completion_percentage' => 0, 'created_by' => 6],
            ['id' => 7, 'course_id' => 3, 'name' => 'Object-Oriented Programming', 'title' => 'Object-Oriented Programming', 'description' => 'Classes, objects, and OOP principles', 'sequence' => 2, 'module_type' => 'practical', 'completion_percentage' => 0, 'created_by' => 6],
        ];

        foreach ($modules as $module) {
            Module::create($module + ['created_at' => now(), 'updated_at' => now()]);
        }
    }

    private function seedLessons(): void
    {
        $loremText1 = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.\n\nExcepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.";
        
        $loremText2 = "Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.\n\nUt enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?";
        
        $lessons = [
            // Algebra lessons
            ['id' => 1, 'title' => 'Introduction to Variables', 'description' => $loremText1, 'course_id' => 1, 'module_id' => 1, 'duration' => 45, 'order' => 1, 'content_type' => 'video'],
            ['id' => 2, 'title' => 'Linear Equations', 'description' => $loremText2, 'course_id' => 1, 'module_id' => 1, 'duration' => 60, 'order' => 2, 'content_type' => 'video'],
            ['id' => 3, 'title' => 'Quadratic Functions', 'description' => $loremText1, 'course_id' => 1, 'module_id' => 1, 'duration' => 75, 'order' => 3, 'content_type' => 'text'],
            
            // Calculus lessons
            ['id' => 4, 'title' => 'Limits and Continuity', 'description' => $loremText2, 'course_id' => 1, 'module_id' => 2, 'duration' => 90, 'order' => 1, 'content_type' => 'video'],
            ['id' => 5, 'title' => 'Derivatives', 'description' => $loremText1, 'course_id' => 1, 'module_id' => 2, 'duration' => 80, 'order' => 2, 'content_type' => 'video'],
            
            // Statistics lessons
            ['id' => 6, 'title' => 'Descriptive Statistics', 'description' => $loremText2, 'course_id' => 1, 'module_id' => 3, 'duration' => 70, 'order' => 1, 'content_type' => 'text'],
            
            // Physics lessons
            ['id' => 7, 'title' => 'Newton\'s Laws of Motion', 'description' => $loremText1, 'course_id' => 2, 'module_id' => 4, 'duration' => 85, 'order' => 1, 'content_type' => 'video'],
            ['id' => 8, 'title' => 'Force and Acceleration', 'description' => $loremText2, 'course_id' => 2, 'module_id' => 4, 'duration' => 75, 'order' => 2, 'content_type' => 'video'],
            
            // Programming lessons
            ['id' => 9, 'title' => 'Variables and Data Types', 'description' => $loremText1, 'course_id' => 3, 'module_id' => 6, 'duration' => 60, 'order' => 1, 'content_type' => 'video'],
            ['id' => 10, 'title' => 'Control Structures', 'description' => $loremText2, 'course_id' => 3, 'module_id' => 6, 'duration' => 90, 'order' => 2, 'content_type' => 'video'],
        ];

        foreach ($lessons as $lesson) {
            Lesson::create($lesson + ['created_at' => now(), 'updated_at' => now()]);
        }
    }

    private function seedActivities(): void
    {
        $quizType = DB::table('activity_types')->where('name', 'Quiz')->first();
        $assignmentType = DB::table('activity_types')->where('name', 'Assignment')->first();
        
        $activities = [
            ['id' => 1, 'title' => 'Algebra Basics Quiz', 'description' => 'Test your understanding of basic algebraic concepts', 'activity_type_id' => $quizType->id, 'created_by' => 4, 'passing_percentage' => 70, 'due_date' => now()->addDays(7)],
            ['id' => 2, 'title' => 'Linear Equations Practice', 'description' => 'Solve various linear equation problems', 'activity_type_id' => $assignmentType->id, 'created_by' => 4, 'passing_percentage' => 75, 'due_date' => now()->addDays(10)],
            ['id' => 3, 'title' => 'Calculus Fundamentals Quiz', 'description' => 'Assessment of calculus concepts including limits and derivatives', 'activity_type_id' => $quizType->id, 'created_by' => 4, 'passing_percentage' => 80, 'due_date' => now()->addDays(5)],
            ['id' => 4, 'title' => 'Statistics Project', 'description' => 'Analyze real-world data using statistical methods', 'activity_type_id' => $assignmentType->id, 'created_by' => 4, 'passing_percentage' => 85, 'due_date' => now()->addDays(14)],
            ['id' => 5, 'title' => 'Physics Mechanics Quiz', 'description' => 'Test on Newton\'s laws and mechanical systems', 'activity_type_id' => $quizType->id, 'created_by' => 5, 'passing_percentage' => 75, 'due_date' => now()->addDays(6)],
            ['id' => 6, 'title' => 'Thermodynamics Assignment', 'description' => 'Problems involving heat transfer and energy', 'activity_type_id' => $assignmentType->id, 'created_by' => 5, 'passing_percentage' => 70, 'due_date' => now()->addDays(8)],
            ['id' => 7, 'title' => 'Programming Basics Quiz', 'description' => 'Quiz on variables, functions, and basic programming concepts', 'activity_type_id' => $quizType->id, 'created_by' => 6, 'passing_percentage' => 80, 'due_date' => now()->addDays(4)],
            ['id' => 8, 'title' => 'OOP Programming Project', 'description' => 'Create a small application using object-oriented principles', 'activity_type_id' => $assignmentType->id, 'created_by' => 6, 'passing_percentage' => 85, 'due_date' => now()->addDays(21)],
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
            if (!DB::table('module_activities')->where('module_id', $ma['module_id'])->where('activity_id', $ma['activity_id'])->exists()) {
                DB::table('module_activities')->insert($ma + ['created_at' => now(), 'updated_at' => now()]);
            }
        }
    }

    private function seedQuizzesAndQuestions(): void
    {
        $quizActivities = [1, 3, 5, 7];
        
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

            $this->createQuestionsForQuiz($quiz->id, $activityId);
        }
    }

    private function createQuestionsForQuiz($quizId, $activityId): void
    {
        $questionsData = [
            1 => [
                ['question_text' => 'What is the value of x in the equation 2x + 5 = 15?', 'points' => 5, 'options' => [
                    ['option_text' => '5', 'is_correct' => true], ['option_text' => '10', 'is_correct' => false],
                    ['option_text' => '7.5', 'is_correct' => false], ['option_text' => '2.5', 'is_correct' => false],
                ]],
                ['question_text' => 'Simplify: 3(x + 4) - 2x', 'points' => 7, 'options' => [
                    ['option_text' => 'x + 12', 'is_correct' => true], ['option_text' => '5x + 12', 'is_correct' => false],
                    ['option_text' => 'x + 4', 'is_correct' => false], ['option_text' => '3x + 2', 'is_correct' => false],
                ]],
                ['question_text' => 'What is the slope of the line y = 2x - 3?', 'points' => 3, 'options' => [
                    ['option_text' => '2', 'is_correct' => true], ['option_text' => '-3', 'is_correct' => false],
                    ['option_text' => '1', 'is_correct' => false], ['option_text' => '0', 'is_correct' => false],
                ]],
            ],
            3 => [
                ['question_text' => 'What is the derivative of x²?', 'points' => 8, 'options' => [
                    ['option_text' => '2x', 'is_correct' => true], ['option_text' => 'x²', 'is_correct' => false],
                    ['option_text' => '2', 'is_correct' => false], ['option_text' => 'x', 'is_correct' => false],
                ]],
                ['question_text' => 'What is the limit of (x² - 1)/(x - 1) as x approaches 1?', 'points' => 10, 'options' => [
                    ['option_text' => '2', 'is_correct' => true], ['option_text' => '1', 'is_correct' => false],
                    ['option_text' => '0', 'is_correct' => false], ['option_text' => 'undefined', 'is_correct' => false],
                ]],
            ],
            5 => [
                ['question_text' => 'What is Newton\'s second law of motion?', 'points' => 6, 'options' => [
                    ['option_text' => 'F = ma', 'is_correct' => true], ['option_text' => 'F = m/a', 'is_correct' => false],
                    ['option_text' => 'F = a/m', 'is_correct' => false], ['option_text' => 'F = m + a', 'is_correct' => false],
                ]],
                ['question_text' => 'If an object has a mass of 10 kg and acceleration of 5 m/s², what is the force?', 'points' => 8, 'options' => [
                    ['option_text' => '50 N', 'is_correct' => true], ['option_text' => '15 N', 'is_correct' => false],
                    ['option_text' => '2 N', 'is_correct' => false], ['option_text' => '0.5 N', 'is_correct' => false],
                ]],
                ['question_text' => 'What is the unit of acceleration?', 'points' => 4, 'options' => [
                    ['option_text' => 'm/s²', 'is_correct' => true], ['option_text' => 'm/s', 'is_correct' => false],
                    ['option_text' => 'kg⋅m/s²', 'is_correct' => false], ['option_text' => 'N', 'is_correct' => false],
                ]],
            ],
            7 => [
                ['question_text' => 'Which of the following is a valid variable name in most programming languages?', 'points' => 4, 'options' => [
                    ['option_text' => 'myVariable', 'is_correct' => true], ['option_text' => '2variable', 'is_correct' => false],
                    ['option_text' => 'my-variable', 'is_correct' => false], ['option_text' => 'class', 'is_correct' => false],
                ]],
                ['question_text' => 'What does the following code do? if (x > 0) { print("positive"); }', 'points' => 6, 'options' => [
                    ['option_text' => 'Prints "positive" if x is greater than 0', 'is_correct' => true],
                    ['option_text' => 'Always prints "positive"', 'is_correct' => false],
                    ['option_text' => 'Prints "positive" if x is 0', 'is_correct' => false],
                    ['option_text' => 'Creates a syntax error', 'is_correct' => false],
                ]],
                ['question_text' => 'What is a function in programming?', 'points' => 8, 'options' => [
                    ['option_text' => 'A reusable block of code that performs a specific task', 'is_correct' => true],
                    ['option_text' => 'A variable that stores data', 'is_correct' => false],
                    ['option_text' => 'A way to comment code', 'is_correct' => false],
                    ['option_text' => 'A type of loop', 'is_correct' => false],
                ]],
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

    private function seedAssignmentsAndQuestions(): void
    {
        // Assignment activities are: 2, 4, 6, 8 (Linear Equations Practice, Statistics Project, Thermodynamics Assignment, OOP Programming Project)
        $assignmentActivities = [2, 4, 6, 8];
        
        foreach ($assignmentActivities as $activityId) {
            $activity = Activity::find($activityId);
            
            $assignment = Assignment::create([
                'activity_id' => $activityId,
                'created_by' => $activity->created_by,
                'title' => $activity->title,
                'description' => $activity->description,
                'assignment_type' => 'objective', // objective questions
                'total_points' => 100,
                'time_limit' => null,
                'allow_late_submission' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create questions for each assignment
            $this->createQuestionsForAssignment($assignment->id, $activityId);
        }
    }

    private function createQuestionsForAssignment($assignmentId, $activityId): void
    {
        $questionsData = [
            2 => [ // Linear Equations Practice
                [
                    'question_text' => 'Solve for x: 3x + 7 = 22',
                    'question_type' => 'multiple_choice',
                    'points' => 25,
                    'options' => [
                        ['option_text' => 'x = 3', 'is_correct' => false],
                        ['option_text' => 'x = 5', 'is_correct' => true],
                        ['option_text' => 'x = 7', 'is_correct' => false],
                        ['option_text' => 'x = 10', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'What is the slope-intercept form of a linear equation?',
                    'question_type' => 'multiple_choice',
                    'points' => 25,
                    'options' => [
                        ['option_text' => 'y = mx + b', 'is_correct' => true],
                        ['option_text' => 'ax + by = c', 'is_correct' => false],
                        ['option_text' => 'y = ax² + bx + c', 'is_correct' => false],
                        ['option_text' => 'x + y = z', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'If y = 2x - 4, what is y when x = 6?',
                    'question_type' => 'short_answer',
                    'points' => 25,
                    'correct_answer' => '8',
                    'acceptable_answers' => ['8', 'eight', '8.0'],
                    'case_sensitive' => false,
                ],
                [
                    'question_text' => 'True or False: A line with slope 0 is horizontal.',
                    'question_type' => 'true_false',
                    'points' => 25,
                    'correct_answer' => 'true',
                ],
            ],
            4 => [ // Statistics Project
                [
                    'question_text' => 'What is the mean of this data set: 4, 8, 6, 5, 7?',
                    'question_type' => 'multiple_choice',
                    'points' => 20,
                    'options' => [
                        ['option_text' => '5', 'is_correct' => false],
                        ['option_text' => '6', 'is_correct' => true],
                        ['option_text' => '7', 'is_correct' => false],
                        ['option_text' => '8', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'Which measure of central tendency is most affected by outliers?',
                    'question_type' => 'multiple_choice',
                    'points' => 20,
                    'options' => [
                        ['option_text' => 'Mean', 'is_correct' => true],
                        ['option_text' => 'Median', 'is_correct' => false],
                        ['option_text' => 'Mode', 'is_correct' => false],
                        ['option_text' => 'Range', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'What is the median of: 3, 7, 5, 9, 11?',
                    'question_type' => 'short_answer',
                    'points' => 30,
                    'correct_answer' => '7',
                    'acceptable_answers' => ['7', 'seven', '7.0'],
                    'case_sensitive' => false,
                ],
                [
                    'question_text' => 'True or False: Standard deviation measures the spread of data.',
                    'question_type' => 'true_false',
                    'points' => 30,
                    'correct_answer' => 'true',
                ],
            ],
            6 => [ // Thermodynamics Assignment
                [
                    'question_text' => 'What is the first law of thermodynamics?',
                    'question_type' => 'multiple_choice',
                    'points' => 25,
                    'options' => [
                        ['option_text' => 'Energy cannot be created or destroyed', 'is_correct' => true],
                        ['option_text' => 'Entropy always increases', 'is_correct' => false],
                        ['option_text' => 'Heat flows from cold to hot', 'is_correct' => false],
                        ['option_text' => 'Work equals force times distance', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'What is the SI unit of heat energy?',
                    'question_type' => 'multiple_choice',
                    'points' => 25,
                    'options' => [
                        ['option_text' => 'Calorie', 'is_correct' => false],
                        ['option_text' => 'Joule', 'is_correct' => true],
                        ['option_text' => 'Watt', 'is_correct' => false],
                        ['option_text' => 'Newton', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'Calculate the heat required to raise 2kg of water by 10°C (specific heat = 4.18 J/g°C). Answer in Joules.',
                    'question_type' => 'short_answer',
                    'points' => 25,
                    'correct_answer' => '83600',
                    'acceptable_answers' => ['83600', '83,600', '83600 J', '83.6 kJ'],
                    'case_sensitive' => false,
                ],
                [
                    'question_text' => 'True or False: Temperature is a measure of average kinetic energy.',
                    'question_type' => 'true_false',
                    'points' => 25,
                    'correct_answer' => 'true',
                ],
            ],
            8 => [ // OOP Programming Project
                [
                    'question_text' => 'What does OOP stand for?',
                    'question_type' => 'multiple_choice',
                    'points' => 20,
                    'options' => [
                        ['option_text' => 'Object-Oriented Programming', 'is_correct' => true],
                        ['option_text' => 'Open Operating Protocol', 'is_correct' => false],
                        ['option_text' => 'Optimized Output Process', 'is_correct' => false],
                        ['option_text' => 'Operational Object Program', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'Which is NOT a pillar of OOP?',
                    'question_type' => 'multiple_choice',
                    'points' => 20,
                    'options' => [
                        ['option_text' => 'Encapsulation', 'is_correct' => false],
                        ['option_text' => 'Inheritance', 'is_correct' => false],
                        ['option_text' => 'Polymorphism', 'is_correct' => false],
                        ['option_text' => 'Compilation', 'is_correct' => true],
                    ],
                ],
                [
                    'question_text' => 'What keyword is used to create a new instance of a class in most OOP languages?',
                    'question_type' => 'short_answer',
                    'points' => 30,
                    'correct_answer' => 'new',
                    'acceptable_answers' => ['new', 'New', 'NEW'],
                    'case_sensitive' => false,
                ],
                [
                    'question_text' => 'True or False: A class is a blueprint for creating objects.',
                    'question_type' => 'true_false',
                    'points' => 30,
                    'correct_answer' => 'true',
                ],
            ],
        ];

        if (isset($questionsData[$activityId])) {
            foreach ($questionsData[$activityId] as $index => $questionData) {
                $question = AssignmentQuestion::create([
                    'assignment_id' => $assignmentId,
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionData['question_type'],
                    'points' => $questionData['points'],
                    'correct_answer' => $questionData['correct_answer'] ?? null,
                    'acceptable_answers' => $questionData['acceptable_answers'] ?? null,
                    'case_sensitive' => $questionData['case_sensitive'] ?? false,
                    'order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create options for multiple choice questions
                if ($questionData['question_type'] === 'multiple_choice' && !empty($questionData['options'])) {
                    foreach ($questionData['options'] as $optionIndex => $optionData) {
                        AssignmentQuestionOption::create([
                            'assignment_question_id' => $question->id,
                            'option_text' => $optionData['option_text'],
                            'is_correct' => $optionData['is_correct'],
                            'order' => $optionIndex + 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }

    // ===== ENROLLMENT AND PROGRESS SEEDERS =====

    private function seedCourseEnrollments(): void
    {
        // Enroll all 15 students in all 3 courses
        for ($studentId = 1; $studentId <= 15; $studentId++) {
            $userId = $studentId + 8;
            
            for ($courseId = 1; $courseId <= 3; $courseId++) {
                if (!CourseEnrollment::where('student_id', $studentId)->where('course_id', $courseId)->exists()) {
                    CourseEnrollment::create([
                        'user_id' => $userId,
                        'student_id' => $studentId,
                        'course_id' => $courseId,
                        'instructor_id' => $courseId + 3,
                        'enrolled_at' => $this->faker()->dateTimeBetween('-3 months', '-1 month'),
                        'progress' => rand(10, 95),
                        'is_completed' => rand(0, 1),
                        'completed_at' => rand(0, 1) ? $this->faker()->dateTimeBetween('-1 month', 'now') : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                if (!DB::table('course_student')->where('course_id', $courseId)->where('student_id', $studentId)->exists()) {
                    DB::table('course_student')->insert([
                        'course_id' => $courseId,
                        'student_id' => $studentId,
                        'enrolled_at' => now(),
                        'status' => 'enrolled',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        
        $this->syncScheduleParticipants();
    }

    private function syncScheduleParticipants(): void
    {
        $courses = \App\Models\Course::whereHas('schedules')->with('schedules')->get();
        
        foreach ($courses as $course) {
            $enrolledStudents = DB::table('course_student')
                ->join('students', 'course_student.student_id', '=', 'students.id')
                ->where('course_student.course_id', $course->id)
                ->where('course_student.status', 'enrolled')
                ->select('students.user_id', 'students.id as student_id')
                ->get();
            
            foreach ($course->schedules as $schedule) {
                foreach ($enrolledStudents as $student) {
                    if (!\App\Models\ScheduleParticipant::where('schedule_id', $schedule->id)->where('user_id', $student->user_id)->exists()) {
                        \App\Models\ScheduleParticipant::create([
                            'schedule_id' => $schedule->id,
                            'user_id' => $student->user_id,
                            'role_in_schedule' => 'student',
                            'participation_status' => 'invited',
                        ]);
                    }
                }
            }
        }
    }

    private function seedStudentActivities(): void
    {
        for ($studentId = 1; $studentId <= 15; $studentId++) {
            // Math activities
            $this->createStudentActivity($studentId, 1, 1, 1);
            $this->createStudentActivity($studentId, 1, 1, 2);
            $this->createStudentActivity($studentId, 1, 2, 3);
            $this->createStudentActivity($studentId, 1, 3, 4);
            
            // Physics activities
            $this->createStudentActivity($studentId, 2, 4, 5);
            $this->createStudentActivity($studentId, 2, 5, 6);
            
            // Programming activities
            $this->createStudentActivity($studentId, 3, 6, 7);
            $this->createStudentActivity($studentId, 3, 7, 8);
        }
    }

    private function createStudentActivity($studentId, $courseId, $moduleId, $activityId): void
    {
        $statuses = ['not_started', 'in_progress', 'completed', 'submitted'];
        $status = $statuses[array_rand($statuses)];
        
        $maxScore = rand(80, 100);
        $score = null;
        $percentageScore = null;
        $startedAt = null;
        $completedAt = null;
        $submittedAt = null;
        $gradedAt = null;
        
        if ($status !== 'not_started') {
            $startedAt = $this->faker()->dateTimeBetween('-2 months', '-1 week');
            $score = rand(60, $maxScore);
            $percentageScore = ($score / $maxScore) * 100;
            
            if (in_array($status, ['completed', 'submitted'])) {
                $completedAt = $this->faker()->dateTimeBetween($startedAt, 'now');
                if ($status === 'submitted') {
                    $submittedAt = $completedAt;
                } else {
                    // Status is 'completed' - means it was graded
                    $submittedAt = $this->faker()->dateTimeBetween($startedAt, $completedAt);
                    $gradedAt = $this->faker()->dateTimeBetween($submittedAt, 'now');
                }
            }
        }

        StudentActivity::create([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'module_id' => $moduleId,
            'activity_id' => $activityId,
            'status' => $status,
            'score' => $score,
            'max_score' => $maxScore,
            'percentage_score' => $percentageScore,
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
            'submitted_at' => $submittedAt,
            'graded_at' => $gradedAt,
            'progress_data' => json_encode(['progress' => rand(0, 100)]),
            'feedback' => rand(0, 1) ? $this->faker()->sentence(10) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedQuizProgressAndAnswers(): void
    {
        $quizActivities = [1, 3, 5, 7];
        
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

        $isCompleted = in_array($studentActivity->status, ['completed', 'submitted']);
        $startedAt = $studentActivity->started_at ? Carbon::parse($studentActivity->started_at) : $this->faker()->dateTimeBetween('-1 month', '-1 week');
        
        // Create StudentActivityProgress record (consolidated model)
        $progress = StudentActivityProgress::updateOrCreate(
            [
                'student_activity_id' => $studentActivity->id,
                'activity_id' => $activityId,
            ],
            [
                'student_id' => $studentId,
                'activity_type' => 'quiz',
                'status' => $studentActivity->status,
                'started_at' => $startedAt,
                'submitted_at' => $studentActivity->submitted_at,
                'completed_at' => $studentActivity->completed_at,
                'graded_at' => $studentActivity->graded_at,
                'is_completed' => $isCompleted,
                'is_submitted' => $isCompleted,
                'completed_questions' => $isCompleted ? $questions->count() : rand(0, $questions->count()),
                'answered_questions' => $isCompleted ? $questions->count() : rand(0, $questions->count()),
                'total_questions' => $questions->count(),
                'quiz_data' => json_encode([
                    'quiz_id' => $quiz->id,
                    'last_accessed_at' => $isCompleted ? $this->faker()->dateTimeBetween($startedAt, 'now')->format('Y-m-d H:i:s') : $startedAt->format('Y-m-d H:i:s'),
                    'time_spent' => rand(10, 45) * 60,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $totalScore = 0;
        $totalPossible = $questions->sum('points');

        // Create answers
        foreach ($questions as $question) {
            $options = QuestionOption::where('question_id', $question->id)->get();
            $selectedOption = $options->random();
            
            $isCorrect = $selectedOption->is_correct;
            $pointsEarned = $isCorrect ? $question->points : 0;
            $totalScore += $pointsEarned;

            StudentQuizAnswer::updateOrCreate(
                [
                    'activity_progress_id' => $progress->id,
                    'question_id' => $question->id,
                ],
                [
                    'student_id' => $studentId,
                    'selected_option_id' => $selectedOption->id,
                    'answer_text' => $selectedOption->option_text,
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                    'answered_at' => $this->faker()->dateTimeBetween($startedAt, $startedAt->copy()->addHour()),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Update progress with scores
        $percentageScore = $totalPossible > 0 ? ($totalScore / $totalPossible) * 100 : 0;
        $progress->update([
            'score' => $totalScore,
            'max_score' => $totalPossible,
            'points_earned' => $totalScore,
            'points_possible' => $totalPossible,
            'percentage_score' => $percentageScore,
        ]);
        
        // Update student activity to match (maintain consistency)
        $studentActivity->update([
            'score' => $totalScore,
            'max_score' => $totalPossible,
            'percentage_score' => $percentageScore,
        ]);
    }

    private function seedAssignmentProgressAndAnswers(): void
    {
        $assignmentActivities = [2, 4, 6, 8]; // Assignment activity IDs
        
        for ($studentId = 1; $studentId <= 15; $studentId++) {
            foreach ($assignmentActivities as $activityId) {
                $studentActivity = StudentActivity::where('student_id', $studentId)
                    ->where('activity_id', $activityId)
                    ->first();
                
                if ($studentActivity && $studentActivity->status !== 'not_started') {
                    $this->createAssignmentProgress($studentId, $activityId, $studentActivity);
                }
            }
        }
    }

    private function createAssignmentProgress($studentId, $activityId, $studentActivity): void
    {
        $assignment = Assignment::where('activity_id', $activityId)->first();
        if (!$assignment) return;

        $questions = AssignmentQuestion::where('assignment_id', $assignment->id)->get();
        if ($questions->isEmpty()) return;

        $isCompleted = in_array($studentActivity->status, ['completed', 'submitted']);
        $startedAt = $studentActivity->started_at ? Carbon::parse($studentActivity->started_at) : $this->faker()->dateTimeBetween('-1 month', '-1 week');
        
        // Determine if assignment requires grading (has essay or file upload questions)
        $requiresGrading = $assignment->acceptsFileUploads();
        
        // Create StudentActivityProgress record
        $progress = StudentActivityProgress::updateOrCreate(
            [
                'student_activity_id' => $studentActivity->id,
                'activity_id' => $activityId,
            ],
            [
                'student_id' => $studentId,
                'activity_type' => 'assignment',
                'status' => $studentActivity->status,
                'started_at' => $startedAt,
                'submitted_at' => $studentActivity->submitted_at,
                'completed_at' => $studentActivity->completed_at,
                'graded_at' => $studentActivity->graded_at,
                'is_completed' => $isCompleted,
                'is_submitted' => in_array($studentActivity->status, ['submitted', 'completed']),
                'points_possible' => $assignment->total_points,
                'total_questions' => $questions->count(),
                'answered_questions' => $isCompleted ? $questions->count() : rand(0, $questions->count()),
                'requires_grading' => $requiresGrading,
                'due_date' => $assignment->activity->due_date,
                'assignment_data' => json_encode([
                    'assignment_id' => $assignment->id,
                    'submission_status' => $isCompleted ? 'submitted' : 'draft',
                    'last_accessed_at' => $isCompleted ? $this->faker()->dateTimeBetween($startedAt, 'now')->format('Y-m-d H:i:s') : $startedAt->format('Y-m-d H:i:s'),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $totalScore = 0;
        $totalPossible = $assignment->total_points;

        // Create answers for auto-gradable questions
        $answeredQuestionsCount = $progress->answered_questions;
        $questionsToAnswer = $questions->take($answeredQuestionsCount);
        
        foreach ($questionsToAnswer as $index => $question) {
            $answerData = $this->generateAnswerForQuestion($question);
            
            // Auto-grade if possible
            $isCorrect = false;
            $pointsEarned = 0;
            
            if (in_array($question->question_type, ['multiple_choice', 'true_false', 'short_answer', 'enumeration'])) {
                $isCorrect = $question->checkAnswer($answerData['answer']);
                $pointsEarned = $isCorrect ? $question->points : 0;
            }
            
            $totalScore += $pointsEarned;

            StudentAssignmentAnswer::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'assignment_id' => $assignment->id,
                    'assignment_question_id' => $question->id,
                ],
                [
                    'answer_text' => $answerData['answer_text'] ?? null,
                    'selected_options' => $answerData['selected_options'] ?? null,
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                    'answered_at' => $this->faker()->dateTimeBetween($startedAt, $startedAt->copy()->addHours(2)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Calculate percentage and update both progress and student activity
        $percentageScore = $totalPossible > 0 ? round(($totalScore / $totalPossible) * 100, 2) : 0;
        
        // Update progress with scores
        $progress->update([
            'score' => $totalScore,
            'max_score' => $totalPossible,
            'percentage_score' => $percentageScore,
            'points_earned' => $totalScore,
        ]);
        
        // Update student activity to match (maintain consistency)
        $studentActivity->update([
            'score' => $totalScore,
            'max_score' => $totalPossible,
            'percentage_score' => $percentageScore,
        ]);
    }

    private function generateAnswerForQuestion($question): array
    {
        $result = [
            'answer_text' => null,
            'selected_options' => null,
            'answer' => null,
        ];
        
        switch ($question->question_type) {
            case 'multiple_choice':
                $options = AssignmentQuestionOption::where('assignment_question_id', $question->id)->get();
                if ($options->isNotEmpty()) {
                    // 70% chance of correct answer
                    $correctOptions = $options->where('is_correct', true);
                    $selectedOption = (rand(1, 100) <= 70 && $correctOptions->isNotEmpty()) 
                        ? $correctOptions->random() 
                        : $options->random();
                    
                    $result['selected_options'] = [$selectedOption->id];
                    $result['answer'] = $result['selected_options'];
                }
                break;
                
            case 'true_false':
                // 70% chance of correct answer
                if (rand(1, 100) <= 70) {
                    $result['answer_text'] = $question->correct_answer;
                } else {
                    $result['answer_text'] = $question->correct_answer === 'true' ? 'false' : 'true';
                }
                $result['answer'] = $result['answer_text'];
                break;
                
            case 'short_answer':
                // 70% chance of correct answer
                if (rand(1, 100) <= 70 && $question->correct_answer) {
                    $result['answer_text'] = $question->correct_answer;
                } else {
                    $result['answer_text'] = 'Student answer ' . rand(1, 100);
                }
                $result['answer'] = $result['answer_text'];
                break;
                
            case 'enumeration':
                // 70% chance of correct answer
                $options = AssignmentQuestionOption::where('assignment_question_id', $question->id)
                    ->where('is_correct', true)
                    ->get();
                if ($options->isNotEmpty()) {
                    if (rand(1, 100) <= 70) {
                        $result['selected_options'] = $options->pluck('id')->toArray();
                    } else {
                        // Mix correct and incorrect
                        $allOptions = AssignmentQuestionOption::where('assignment_question_id', $question->id)->get();
                        $result['selected_options'] = $allOptions->random(min(2, $allOptions->count()))->pluck('id')->toArray();
                    }
                    $result['answer'] = $result['selected_options'];
                }
                break;
                
            case 'essay':
                $result['answer_text'] = $this->faker()->paragraph(3);
                $result['answer'] = $result['answer_text'];
                break;
        }
        
        return $result;
    }

    private function createCourseSchedule($course): void
    {
        if (!$course->end_date) return;

        $scheduleType = \App\Models\ScheduleType::where('name', 'course_due_date')->first();
        if (!$scheduleType) return;

        $endDate = new \DateTime($course->end_date);
        $fromDate = clone $endDate;
        $fromDate->modify('-1 hour');

        $schedule = \App\Models\Schedule::create([
            'schedule_type_id' => $scheduleType->id,
            'title' => $course->title . ' - Due Date',
            'description' => 'Course due date for ' . $course->title,
            'from_datetime' => $fromDate->format('Y-m-d H:i:s'),
            'to_datetime' => $endDate->format('Y-m-d H:i:s'),
            'is_all_day' => false,
            'is_recurring' => false,
            'status' => 'scheduled',
            'created_by' => $course->created_by,
            'schedulable_type' => 'App\\Models\\Course',
            'schedulable_id' => $course->id,
        ]);

        $instructor = $course->instructor;
        if ($instructor && $instructor->user_id) {
            \App\Models\ScheduleParticipant::create([
                'schedule_id' => $schedule->id,
                'user_id' => $instructor->user_id,
                'role_in_schedule' => 'instructor',
                'participation_status' => 'accepted',
            ]);
        }
    }

    /**
     * Recalculate course completion statuses for all enrollments
     * This ensures that courses with all modules completed are marked as complete
     */
    private function recalculateCourseCompletions(): void
    {
        $enrollments = CourseEnrollment::all();
        
        foreach ($enrollments as $enrollment) {
            // Check and auto-complete modules based on activity completion
            $enrollment->checkAndCompleteModules();
            
            // Update progress and check for course completion
            $enrollment->updateProgress();
        }
        
        $this->command->info('✅ Recalculated ' . $enrollments->count() . ' course enrollments');
    }
}
