<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Assignment;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentQuestionOption;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\GradeLevel;
use App\Models\Instructor;
use App\Models\Module;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;
use App\Models\StudentAssignmentAnswer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FreshTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Starting fresh test data seeding...');

        // 1. Clear all data
        $this->command->info('Clearing existing data...');
        $this->clearExistingData();

        // 2. Foundation Data
        $this->command->info('Seeding foundation data...');
        $this->seedRoles();
        $this->seedGradeLevels();
        $this->seedActivityTypes();

        // 3. Users
        $this->command->info('Seeding users...');
        $this->seedUsers();

        // 4. Course content
        $this->command->info('Seeding course structure...');
        $this->seedCourses();
        $this->seedModules();
        $this->seedActivities();
        
        // 4.1 Create skills and link to activities
        $this->command->info('Seeding skills and linking to activities...');
        $this->call(SkillSeeder::class);

        // 5. Assignments
        $this->command->info('Seeding assignments and questions...');
        $this->seedAssignmentsAndQuestions();

        // 6. Enrollments
        $this->command->info('Seeding enrollments...');
        $this->seedCourseEnrollments();

        // 7. Student Activity Progress
        $this->command->info('Seeding student activities and submissions...');
        $this->seedStudentActivities();

        $this->command->info('✅ Fresh test data seeded successfully!');
    }

    private function clearExistingData(): void
    {
        DB::statement('PRAGMA foreign_keys=OFF');

        // Clear in reverse dependency order
        DB::table('student_assignment_answers')->delete();
        DB::table('student_activity_progress')->delete();
        DB::table('student_activities')->delete();
        DB::table('course_enrollments')->delete();
        DB::table('assignment_question_options')->delete();
        DB::table('assignment_questions')->delete();
        DB::table('assignments')->delete();
        DB::table('module_activities')->delete();
        DB::table('skills')->delete();
        DB::table('skill_activities')->delete();
        DB::table('activities')->delete();
        DB::table('modules')->delete();
        DB::table('courses')->delete();
        DB::table('instructors')->delete();
        DB::table('students')->delete();
        DB::table('users')->delete();

        // Foundation data
        DB::table('activity_types')->delete();
        DB::table('grade_levels')->delete();
        DB::table('roles')->delete();

        // Reset sequences
        DB::table('sqlite_sequence')->delete();

        DB::statement('PRAGMA foreign_keys=ON');
    }

    private function seedRoles(): void
    {
        $roles = [
            ['name' => 'admin', 'display_name' => 'Admin', 'description' => 'System administrator', 'is_active' => true],
            ['name' => 'instructor', 'display_name' => 'Instructor', 'description' => 'Course instructor', 'is_active' => true],
            ['name' => 'student', 'display_name' => 'Student', 'description' => 'Course student', 'is_active' => true],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }

    private function seedGradeLevels(): void
    {
        GradeLevel::create([
            'name' => 'Grade 10',
            'display_name' => 'Tenth Grade',
            'level' => 10,
            'is_active' => true,
        ]);
    }

    private function seedActivityTypes(): void
    {
        $types = [
            ['name' => 'Assignment', 'description' => 'Student assignment', 'model' => 'App\\Models\\Assignment'],
            ['name' => 'Quiz', 'description' => 'Quiz activity', 'model' => 'App\\Models\\Quiz'],
        ];

        foreach ($types as $type) {
            ActivityType::create($type);
        }
    }

    private function seedUsers(): void
    {
        $instructorRole = Role::where('name', 'instructor')->first();
        $studentRole = Role::where('name', 'student')->first();
        $gradeLevel = GradeLevel::first();

        // Create instructors
        $instructorEmails = [
            ['name' => 'Test Instructor', 'email' => 'instructor@example.com'],
            ['name' => 'Test Admin', 'email' => 'admin@example.com'],
        ];

        foreach ($instructorEmails as $instructorData) {
            $instructorUser = User::create([
                'name' => $instructorData['name'],
                'email' => $instructorData['email'],
                'password' => Hash::make('password'),
                'role_id' => $instructorRole->id,
            ]);

            Instructor::create([
                'user_id' => $instructorUser->id,
                'instructor_id' => 'INS' . str_pad($instructorUser->id, 4, '0', STR_PAD_LEFT),
                'department' => 'Mathematics',
                'title' => 'Prof.',
                'employment_type' => 'full-time',
                'status' => 'active',
            ]);
        }

        // Create students
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Test Student {$i}",
                'email' => "student{$i}@example.com",
                'password' => Hash::make('password'),
                'role_id' => $studentRole->id,
            ]);

            Student::create([
                'user_id' => $user->id,
                'student_id_text' => 'STU2026' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'grade_level_id' => $gradeLevel->id,
                'status' => 'active',
            ]);
        }
    }

    private function seedCourses(): void
    {
        $instructor = Instructor::first();
        $instructorUser = User::find($instructor->user_id);

        Course::create([
            'course_code' => 'TEST101',
            'title' => 'Test Course for Submissions',
            'name' => 'TEST101 - Test Course',
            'description' => 'This is a test course to verify student submissions',
            'created_by' => $instructorUser->id,
            'instructor_id' => $instructor->id,
            'is_active' => true,
        ]);
    }

    private function seedModules(): void
    {
        $course = Course::first();

        Module::create([
            'course_id' => $course->id,
            'name' => 'Test Module',
            'title' => 'Test Module',
            'description' => 'Module for testing submissions',
            'sequence' => 1,
        ]);
    }

    private function seedActivities(): void
    {
        $assignmentType = ActivityType::where('name', 'Assignment')->first();
        $instructorUser = User::whereHas('role', function ($q) {
            $q->where('name', 'instructor');
        })->first();

        $activity = Activity::create([
            'title' => 'Test Assignment - Algebra',
            'description' => 'Complete the algebra problems',
            'activity_type_id' => $assignmentType->id,
            'created_by' => $instructorUser->id,
            'due_date' => now()->addDays(7),
        ]);

        // Link to module
        $module = Module::first();
        DB::table('module_activities')->insert([
            'module_id' => $module->id,
            'activity_id' => $activity->id,
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedAssignmentsAndQuestions(): void
    {
        $activity = Activity::first();
        $instructorUser = User::whereHas('role', function ($q) {
            $q->where('name', 'instructor');
        })->first();

        $assignment = Assignment::create([
            'activity_id' => $activity->id,
            'created_by' => $instructorUser->id,
            'title' => $activity->title,
            'description' => $activity->description,
            'assignment_type' => 'objective',
            'total_points' => 100,
        ]);

        $questions = [
            [
                'text' => 'What is 2 + 2?',
                'type' => 'multiple_choice',
                'points' => 25,
                'options' => [
                    ['text' => '3', 'correct' => false],
                    ['text' => '4', 'correct' => true],
                    ['text' => '5', 'correct' => false],
                    ['text' => '6', 'correct' => false],
                ],
            ],
            [
                'text' => 'What is 5 × 3?',
                'type' => 'multiple_choice',
                'points' => 25,
                'options' => [
                    ['text' => '12', 'correct' => false],
                    ['text' => '15', 'correct' => true],
                    ['text' => '18', 'correct' => false],
                    ['text' => '20', 'correct' => false],
                ],
            ],
            [
                'text' => 'What is 10 - 3?',
                'type' => 'multiple_choice',
                'points' => 25,
                'options' => [
                    ['text' => '6', 'correct' => false],
                    ['text' => '7', 'correct' => true],
                    ['text' => '8', 'correct' => false],
                    ['text' => '9', 'correct' => false],
                ],
            ],
            [
                'text' => 'What is 20 ÷ 4?',
                'type' => 'multiple_choice',
                'points' => 25,
                'options' => [
                    ['text' => '4', 'correct' => false],
                    ['text' => '5', 'correct' => true],
                    ['text' => '6', 'correct' => false],
                    ['text' => '7', 'correct' => false],
                ],
            ],
        ];

        foreach ($questions as $qIndex => $qData) {
            $question = AssignmentQuestion::create([
                'assignment_id' => $assignment->id,
                'question_text' => $qData['text'],
                'question_type' => $qData['type'],
                'points' => $qData['points'],
                'order' => $qIndex + 1,
            ]);

            foreach ($qData['options'] as $oData) {
                AssignmentQuestionOption::create([
                    'assignment_question_id' => $question->id,
                    'option_text' => $oData['text'],
                    'is_correct' => $oData['correct'],
                ]);
            }
        }
    }

    private function seedCourseEnrollments(): void
    {
        $course = Course::first();
        $instructor = Instructor::first();
        $students = Student::all();

        foreach ($students as $index => $student) {
            $user = User::find($student->user_id);
            CourseEnrollment::create([
                'user_id' => $user->id,
                'student_id' => $student->id,
                'course_id' => $course->id,
                'instructor_id' => $instructor->id,
            ]);
        }
    }

    private function seedStudentActivities(): void
    {
        $activity = Activity::first();
        $assignment = Assignment::first();
        $module = Module::first();
        $course = Course::first();
        $students = Student::all();

        foreach ($students as $index => $student) {
            $this->createStudentActivityWithSubmission($student, $activity, $assignment, $module, $course, $index);
        }
    }

    private function createStudentActivityWithSubmission($student, $activity, $assignment, $module, $course, $studentIndex): void
    {
        // Create StudentActivity
        $studentActivity = StudentActivity::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'module_id' => $module->id,
            'activity_id' => $activity->id,
            'status' => 'submitted',
            'started_at' => now()->subDays(2),
            'submitted_at' => now()->subDay(),
            'score' => null,
            'max_score' => 100,
            'percentage_score' => null,
        ]);

        // Create StudentActivityProgress
        $progress = StudentActivityProgress::create([
            'student_activity_id' => $studentActivity->id,
            'student_id' => $student->id,
            'activity_id' => $activity->id,
            'activity_type' => 'assignment',
            'status' => 'submitted',
            'started_at' => now()->subDays(2),
            'submitted_at' => now()->subDay(),
            'progress_percentage' => 100,
        ]);

        // Create StudentAssignmentAnswers and calculate score
        $questions = $assignment->questions()->get();
        $totalPoints = 0;

        foreach ($questions as $qIndex => $question) {
            $options = $question->options()->get();
            $correctOption = $options->firstWhere('is_correct', true);

            // Scoring logic for 5 students:
            // Student 1 (index 0): All correct (100%)
            // Student 2 (index 1): 75% correct (3 out of 4)
            // Student 3 (index 2): 50% correct (2 out of 4)
            // Student 4 (index 3): 25% correct (1 out of 4)
            // Student 5 (index 4): All incorrect (0%)
            
            $correctThreshold = match($studentIndex) {
                0 => 4, // All 4 questions correct
                1 => 3, // 3 out of 4 correct
                2 => 2, // 2 out of 4 correct
                3 => 1, // 1 out of 4 correct
                default => 0, // None correct
            };

            $shouldBeCorrect = $qIndex < $correctThreshold;

            if ($shouldBeCorrect && $correctOption) {
                $selectedOption = $correctOption->id;
                $isCorrect = true;
                $pointsEarned = $question->points;
            } else {
                $wrongOptions = $options->where('is_correct', false);
                $selectedOption = $wrongOptions->isNotEmpty() ? $wrongOptions->first()->id : $correctOption->id;
                $isCorrect = false;
                $pointsEarned = 0;
            }

            StudentAssignmentAnswer::create([
                'student_id' => $student->id,
                'assignment_id' => $assignment->id,
                'assignment_question_id' => $question->id,
                'selected_options' => json_encode([$selectedOption]),
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
                'answered_at' => now()->subDay(),
            ]);

            $totalPoints += $pointsEarned;
        }

        // Update scores
        $percentage = ($totalPoints / 100) * 100;
        $studentActivity->update([
            'score' => $totalPoints,
            'percentage_score' => $percentage,
        ]);

        $progress->update([
            'score' => $totalPoints,
            'percentage_score' => $percentage,
            'progress_percentage' => 100,
        ]);

        $this->command->line("  ✓ Student " . ($studentIndex + 1) . ": Created submission with score {$totalPoints}/100");
    }

    /**
     * Link activities to skills based on module relationships
     */
    private function linkSkillsToActivities(): void
    {
        $activities = Activity::with('modules.skills')->get();
        $totalLinks = 0;

        foreach ($activities as $activity) {
            $modules = $activity->modules;

            if ($modules->isEmpty()) {
                continue;
            }

            // Get all skills from all modules this activity belongs to
            $skills = $modules->flatMap(function ($module) {
                return $module->skills;
            })->unique('id');

            if ($skills->isEmpty()) {
                continue;
            }

            // Link activity to skills with default weight
            $skillData = [];
            foreach ($skills as $skill) {
                $skillData[$skill->id] = ['weight' => 1.0];
            }

            $activity->skills()->sync($skillData);
            $totalLinks += count($skillData);
        }

        $this->command->info("  ✓ Linked {$totalLinks} skill-activity relationships");
    }
}

