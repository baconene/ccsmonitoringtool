<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\Module;
use App\Models\Activity;
use App\Models\StudentActivity;
use App\Models\ModuleCompletion;
use App\Models\StudentQuizProgress;
use App\Models\StudentQuizAnswer;
use App\Models\Question;
use App\Models\QuestionOption;
use Carbon\Carbon;

class StudentActivitySeeder extends Seeder
{
    /**
     * Seed student activities and progress data.
     */
    public function run(): void
    {
        echo "\n🎯 Creating Student Activity Data...\n";
        echo "════════════════════════════════════════\n";

        // Get student users (those with role_id = 3)
        $studentUsers = User::where('role_id', 3)->take(5)->get();
        
        if ($studentUsers->isEmpty()) {
            echo "❌ No student users found. Please run the DatabaseSeeder first.\n";
            return;
        }

        // Get available courses
        $courses = Course::with(['modules.activities'])->take(2)->get();
        
        if ($courses->isEmpty()) {
            echo "❌ No courses found. Please run the CourseSeeder first.\n";
            return;
        }

        foreach ($studentUsers as $user) {
            // Find or create student record
            $student = Student::where('user_id', $user->id)->first();
            
            if (!$student) {
                echo "⚠️  No student record found for user {$user->name} (ID: {$user->id}). Skipping...\n";
                continue;
            }

            echo "\n👤 Creating activities for {$user->name} (User ID: {$user->id}, Student ID: {$student->id})...\n";

            // Enroll student in courses if not already enrolled
            foreach ($courses as $course) {
                // Check if already enrolled
                $isEnrolled = $course->students()->where('course_student.student_id', $student->id)->exists();
                
                if (!$isEnrolled) {
                    $course->students()->attach($student->id, [
                        'enrolled_at' => Carbon::now(),
                        'status' => 'enrolled',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    echo "  ✓ Enrolled in course: {$course->title}\n";
                }

                // Create activities for each module
                foreach ($course->modules as $module) {
                    $moduleCompletion = rand(60, 100); // Random completion percentage
                    $isModuleCompleted = $moduleCompletion >= 80;

                    echo "    📖 Module: {$module->description}\n";

                    // Create module completion record
                    if ($isModuleCompleted) {
                        ModuleCompletion::updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'module_id' => $module->id,
                            ],
                            [
                                'course_id' => $course->id,
                                'completed_at' => Carbon::now()->subDays(rand(1, 30)),
                                'completion_data' => json_encode([
                                    'completion_percentage' => $moduleCompletion,
                                    'seeded_data' => true,
                                ]),
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]
                        );
                    }

                    // Create student activities for each activity in the module
                    foreach ($module->activities as $activity) {
                        $score = rand(70, 100); // Random score between 70-100
                        $maxScore = 100;
                        $isCompleted = rand(0, 100) > 20; // 80% chance of completion
                        $submittedAt = $isCompleted ? Carbon::now()->subDays(rand(1, 45)) : null;
                        $percentageScore = $isCompleted ? round(($score / $maxScore) * 100, 2) : 0;
                        
                        // Map activity type names to database enum values
                        $activityTypeMap = [
                            'Quiz' => 'quiz',
                            'Assignment' => 'assignment', 
                            'Assessment' => 'assessment',
                            'Exercise' => 'assignment', // Default exercise to assignment
                        ];
                        $activityTypeName = $activity->activityType->name ?? 'Assignment';
                        $activityType = $activityTypeMap[$activityTypeName] ?? 'assignment';
                        
                        $studentActivity = StudentActivity::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'activity_id' => $activity->id,
                                'module_id' => $module->id,
                            ],
                            [
                                'course_id' => $course->id,
                                'activity_type' => $activityType,
                                'score' => $isCompleted ? $score : 0,
                                'max_score' => $maxScore,
                                'percentage_score' => $percentageScore,
                                'status' => $isCompleted ? 'completed' : 'in_progress',
                                'started_at' => Carbon::now()->subDays(rand(1, 50)),
                                'completed_at' => $isCompleted ? $submittedAt : null,
                                'submitted_at' => $submittedAt,
                                'graded_at' => $isCompleted ? ($submittedAt ? $submittedAt->copy()->addHours(rand(1, 48)) : null) : null,
                                'progress_data' => json_encode([
                                    'attempts' => rand(1, 3),
                                    'time_spent' => rand(300, 3600), // 5 minutes to 1 hour
                                    'seeded_data' => true,
                                ]),
                                'feedback' => $isCompleted ? 'Good work! Keep it up.' : null,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]
                        );

                        // If it's a quiz activity, create quiz progress and answers
                        if ($activity->activityType && $activity->activityType->name === 'Quiz') {
                            // Find the quiz record for this activity
                            $quiz = $activity->quiz ?? null;
                            if ($quiz) {
                                // Get questions for this quiz with their options
                                $questions = Question::where('quiz_id', $quiz->id)
                                    ->with('options')
                                    ->get();
                                $totalQuestions = $questions->count();
                                $completedQuestions = $isCompleted ? $totalQuestions : rand(1, max(1, $totalQuestions - 1));
                                
                                $quizProgress = StudentQuizProgress::updateOrCreate(
                                    [
                                        'student_id' => $student->id,
                                        'quiz_id' => $quiz->id,
                                        'activity_id' => $activity->id,
                                    ],
                                    [
                                        'started_at' => Carbon::now()->subDays(rand(1, 45)),
                                        'last_accessed_at' => Carbon::now()->subDays(rand(1, 7)),
                                        'is_completed' => $isCompleted,
                                        'is_submitted' => $isCompleted,
                                        'completed_questions' => $completedQuestions,
                                        'total_questions' => $totalQuestions,
                                        'score' => $isCompleted ? $score : 0,
                                        'percentage_score' => $percentageScore,
                                        'time_spent' => rand(300, 3600), // 5 minutes to 1 hour in seconds
                                        'created_at' => Carbon::now(),
                                        'updated_at' => Carbon::now(),
                                    ]
                                );

                                // Create individual quiz answers if completed
                                if ($isCompleted && $totalQuestions > 0) {
                                    $this->createQuizAnswers($student, $quizProgress, $questions, $score);
                                }
                            }
                        }

                        $statusIcon = $isCompleted ? '✅' : '⏳';
                        echo "      {$statusIcon} {$activity->title}: {$score}% " . ($isCompleted ? '(Completed)' : '(In Progress)') . "\n";
                    }
                }
            }
        }

        echo "\n🎉 Student activity data seeding completed!\n";
        echo "════════════════════════════════════════\n";

        // Show summary
        $totalActivities = StudentActivity::count();
        $completedActivities = StudentActivity::where('status', 'completed')->count();
        $totalQuizzes = StudentQuizProgress::count();
        $completedQuizzes = StudentQuizProgress::where('is_completed', true)->count();

        echo "\n📊 Summary:\n";
        echo "• Total Student Activities: {$totalActivities}\n";
        echo "• Completed Activities: {$completedActivities}\n";
        echo "• Total Quiz Progress Records: {$totalQuizzes}\n";
        echo "• Completed Quizzes: {$completedQuizzes}\n";
        echo "• Students with Activity Data: " . $studentUsers->count() . "\n";
        echo "• Courses with Student Data: " . $courses->count() . "\n";
    }

    /**
     * Create individual quiz answers for a completed quiz
     */
    private function createQuizAnswers($student, $quizProgress, $questions, $overallScore)
    {
        if (empty($questions)) {
            return;
        }

        $correctAnswersCount = 0;
        $totalQuestions = count($questions);

        foreach ($questions as $question) {
            // Get question options
            $options = $question->options ?? [];
            if (empty($options)) {
                continue;
            }

            // Determine if this answer should be correct based on overall score
            $targetCorrectCount = round(($overallScore / 100) * $totalQuestions);
            $shouldBeCorrect = $correctAnswersCount < $targetCorrectCount;

            // Select an option (correct or incorrect based on shouldBeCorrect)
            $correctOption = null;
            $incorrectOptions = [];

            foreach ($options as $option) {
                if ($option->is_correct) {
                    $correctOption = $option;
                } else {
                    $incorrectOptions[] = $option;
                }
            }

            // Choose the option to select
            $selectedOption = null;
            $isCorrect = false;

            if ($shouldBeCorrect && $correctOption) {
                $selectedOption = $correctOption;
                $isCorrect = true;
                $correctAnswersCount++;
            } elseif (!empty($incorrectOptions)) {
                $selectedOption = $incorrectOptions[array_rand($incorrectOptions)];
                $isCorrect = false;
            } elseif ($correctOption) {
                // Fallback if no incorrect options
                $selectedOption = $correctOption;
                $isCorrect = true;
                $correctAnswersCount++;
            }

            if ($selectedOption) {
                StudentQuizAnswer::create([
                    'student_id' => $student->id,
                    'quiz_progress_id' => $quizProgress->id,
                    'question_id' => $question->id,
                    'selected_option_id' => $selectedOption->id,
                    'answer_text' => $selectedOption->option_text,
                    'is_correct' => $isCorrect,
                    'points_earned' => $isCorrect ? 1 : 0,
                    'answered_at' => Carbon::now()->subDays(rand(1, 30)),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}