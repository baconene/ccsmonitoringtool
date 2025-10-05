<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding Quiz Activities...');

        // Get the instructor
        $instructor = User::where('email', 'instructor1@test.com')->first();
        
        if (!$instructor) {
            $this->command->error('❌ Instructor not found: instructor1@test.com');
            return;
        }

        // Get quiz activity type
        $quizActivityType = ActivityType::where('name', 'Quiz')->first();
        
        if (!$quizActivityType) {
            $this->command->error('❌ Quiz activity type not found');
            return;
        }

        // Define 5 quiz activities with their questions
        $activities = [
            [
                'title' => 'Introduction to Programming Quiz',
                'description' => 'Test your knowledge of basic programming concepts',
                'passing_percentage' => 70,
                'questions' => [
                    [
                        'question_text' => 'What does HTML stand for?',
                        'question_type' => 'multiple-choice',
                        'points' => 10,
                        'options' => [
                            ['option_text' => 'Hyper Text Markup Language', 'is_correct' => true],
                            ['option_text' => 'High Tech Modern Language', 'is_correct' => false],
                            ['option_text' => 'Home Tool Markup Language', 'is_correct' => false],
                            ['option_text' => 'Hyperlinks and Text Markup Language', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'Which programming language is known as the "language of the web"?',
                        'question_type' => 'multiple-choice',
                        'points' => 10,
                        'options' => [
                            ['option_text' => 'Python', 'is_correct' => false],
                            ['option_text' => 'Java', 'is_correct' => false],
                            ['option_text' => 'JavaScript', 'is_correct' => true],
                            ['option_text' => 'C++', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'A variable is a container for storing data values.',
                        'question_type' => 'true-false',
                        'points' => 5,
                        'options' => [
                            ['option_text' => 'True', 'is_correct' => true],
                            ['option_text' => 'False', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'What symbol is used for comments in most programming languages?',
                        'question_type' => 'multiple-choice',
                        'points' => 10,
                        'options' => [
                            ['option_text' => '//', 'is_correct' => true],
                            ['option_text' => '##', 'is_correct' => false],
                            ['option_text' => '**', 'is_correct' => false],
                            ['option_text' => '%%', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'CSS is used for programming logic.',
                        'question_type' => 'true-false',
                        'points' => 5,
                        'options' => [
                            ['option_text' => 'True', 'is_correct' => false],
                            ['option_text' => 'False', 'is_correct' => true],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Mathematics Fundamentals Quiz',
                'description' => 'Test your understanding of basic mathematical concepts',
                'passing_percentage' => 75,
                'questions' => [
                    [
                        'question_text' => 'What is the value of π (pi) approximately?',
                        'question_type' => 'multiple-choice',
                        'points' => 10,
                        'options' => [
                            ['option_text' => '2.14', 'is_correct' => false],
                            ['option_text' => '3.14', 'is_correct' => true],
                            ['option_text' => '4.14', 'is_correct' => false],
                            ['option_text' => '5.14', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'A triangle has how many sides?',
                        'question_type' => 'multiple-choice',
                        'points' => 5,
                        'options' => [
                            ['option_text' => '2', 'is_correct' => false],
                            ['option_text' => '3', 'is_correct' => true],
                            ['option_text' => '4', 'is_correct' => false],
                            ['option_text' => '5', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'The square root of 144 is 12.',
                        'question_type' => 'true-false',
                        'points' => 10,
                        'options' => [
                            ['option_text' => 'True', 'is_correct' => true],
                            ['option_text' => 'False', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'What is 25% of 200?',
                        'question_type' => 'multiple-choice',
                        'points' => 15,
                        'options' => [
                            ['option_text' => '25', 'is_correct' => false],
                            ['option_text' => '50', 'is_correct' => true],
                            ['option_text' => '75', 'is_correct' => false],
                            ['option_text' => '100', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'A prime number is divisible only by 1 and itself.',
                        'question_type' => 'true-false',
                        'points' => 10,
                        'options' => [
                            ['option_text' => 'True', 'is_correct' => true],
                            ['option_text' => 'False', 'is_correct' => false],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Science Basics Quiz',
                'description' => 'Test your knowledge of basic science concepts',
                'passing_percentage' => 70,
                'questions' => [
                    [
                        'question_text' => 'What is the chemical symbol for water?',
                        'question_type' => 'multiple-choice',
                        'points' => 10,
                        'options' => [
                            ['option_text' => 'H2O', 'is_correct' => true],
                            ['option_text' => 'O2', 'is_correct' => false],
                            ['option_text' => 'CO2', 'is_correct' => false],
                            ['option_text' => 'H2', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'The Earth revolves around the Sun.',
                        'question_type' => 'true-false',
                        'points' => 5,
                        'options' => [
                            ['option_text' => 'True', 'is_correct' => true],
                            ['option_text' => 'False', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'How many bones are in the adult human body?',
                        'question_type' => 'multiple-choice',
                        'points' => 15,
                        'options' => [
                            ['option_text' => '186', 'is_correct' => false],
                            ['option_text' => '206', 'is_correct' => true],
                            ['option_text' => '226', 'is_correct' => false],
                            ['option_text' => '246', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'Plants produce oxygen through photosynthesis.',
                        'question_type' => 'true-false',
                        'points' => 10,
                        'options' => [
                            ['option_text' => 'True', 'is_correct' => true],
                            ['option_text' => 'False', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'What planet is known as the Red Planet?',
                        'question_type' => 'multiple-choice',
                        'points' => 10,
                        'options' => [
                            ['option_text' => 'Venus', 'is_correct' => false],
                            ['option_text' => 'Mars', 'is_correct' => true],
                            ['option_text' => 'Jupiter', 'is_correct' => false],
                            ['option_text' => 'Saturn', 'is_correct' => false],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'History and Geography Quiz',
                'description' => 'Test your knowledge of world history and geography',
                'passing_percentage' => 65,
                'questions' => [
                    [
                        'question_text' => 'What is the capital of France?',
                        'question_type' => 'multiple-choice',
                        'points' => 10,
                        'options' => [
                            ['option_text' => 'London', 'is_correct' => false],
                            ['option_text' => 'Berlin', 'is_correct' => false],
                            ['option_text' => 'Paris', 'is_correct' => true],
                            ['option_text' => 'Madrid', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'World War II ended in 1945.',
                        'question_type' => 'true-false',
                        'points' => 10,
                        'options' => [
                            ['option_text' => 'True', 'is_correct' => true],
                            ['option_text' => 'False', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'Which ocean is the largest?',
                        'question_type' => 'multiple-choice',
                        'points' => 10,
                        'options' => [
                            ['option_text' => 'Atlantic Ocean', 'is_correct' => false],
                            ['option_text' => 'Indian Ocean', 'is_correct' => false],
                            ['option_text' => 'Arctic Ocean', 'is_correct' => false],
                            ['option_text' => 'Pacific Ocean', 'is_correct' => true],
                        ]
                    ],
                    [
                        'question_text' => 'The Great Wall of China is visible from space.',
                        'question_type' => 'true-false',
                        'points' => 10,
                        'options' => [
                            ['option_text' => 'True', 'is_correct' => false],
                            ['option_text' => 'False', 'is_correct' => true],
                        ]
                    ],
                    [
                        'question_text' => 'How many continents are there?',
                        'question_type' => 'multiple-choice',
                        'points' => 10,
                        'options' => [
                            ['option_text' => '5', 'is_correct' => false],
                            ['option_text' => '6', 'is_correct' => false],
                            ['option_text' => '7', 'is_correct' => true],
                            ['option_text' => '8', 'is_correct' => false],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Computer Science Basics Quiz',
                'description' => 'Test your understanding of computer science fundamentals',
                'passing_percentage' => 80,
                'questions' => [
                    [
                        'question_text' => 'What does CPU stand for?',
                        'question_type' => 'multiple-choice',
                        'points' => 10,
                        'options' => [
                            ['option_text' => 'Central Processing Unit', 'is_correct' => true],
                            ['option_text' => 'Computer Personal Unit', 'is_correct' => false],
                            ['option_text' => 'Central Program Utility', 'is_correct' => false],
                            ['option_text' => 'Computer Processing Utility', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'RAM stands for Random Access Memory.',
                        'question_type' => 'true-false',
                        'points' => 5,
                        'options' => [
                            ['option_text' => 'True', 'is_correct' => true],
                            ['option_text' => 'False', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'How many bits are in a byte?',
                        'question_type' => 'multiple-choice',
                        'points' => 10,
                        'options' => [
                            ['option_text' => '4', 'is_correct' => false],
                            ['option_text' => '8', 'is_correct' => true],
                            ['option_text' => '16', 'is_correct' => false],
                            ['option_text' => '32', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'An algorithm is a step-by-step procedure for solving a problem.',
                        'question_type' => 'true-false',
                        'points' => 10,
                        'options' => [
                            ['option_text' => 'True', 'is_correct' => true],
                            ['option_text' => 'False', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'What does SQL stand for?',
                        'question_type' => 'multiple-choice',
                        'points' => 15,
                        'options' => [
                            ['option_text' => 'Structured Query Language', 'is_correct' => true],
                            ['option_text' => 'Simple Query Language', 'is_correct' => false],
                            ['option_text' => 'Standard Question Language', 'is_correct' => false],
                            ['option_text' => 'Sequential Query Language', 'is_correct' => false],
                        ]
                    ],
                ]
            ],
        ];

        // Create activities with quizzes and questions
        foreach ($activities as $index => $activityData) {
            // Create activity
            $activity = Activity::create([
                'title' => $activityData['title'],
                'description' => $activityData['description'],
                'activity_type_id' => $quizActivityType->id,
                'created_by' => $instructor->id,
                'passing_percentage' => $activityData['passing_percentage'],
            ]);

            $this->command->info("✓ Created Activity: {$activity->title}");

            // Create quiz for this activity
            $quiz = Quiz::create([
                'activity_id' => $activity->id,
                'created_by' => $instructor->id,
                'title' => $activity->title,
                'description' => $activity->description,
            ]);

            $this->command->info("  ↳ Created Quiz: {$quiz->title}");

            // Create questions for this quiz
            foreach ($activityData['questions'] as $questionData) {
                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionData['question_type'],
                    'points' => $questionData['points'],
                    'correct_answer' => null, // Will be set if needed
                ]);

                $this->command->info("    ↳ Created Question: " . substr($questionData['question_text'], 0, 50) . "...");

                // Create options for this question
                foreach ($questionData['options'] as $optionData) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $optionData['is_correct'],
                    ]);
                }
                
                $this->command->info("      ↳ Created {$question->options->count()} options");
            }
        }

        $this->command->info('✅ Activity seeding completed successfully!');
        $this->command->info('📊 Total Activities Created: ' . Activity::count());
        $this->command->info('📝 Total Quizzes Created: ' . Quiz::count());
        $this->command->info('❓ Total Questions Created: ' . Question::count());
        $this->command->info('⭕ Total Question Options Created: ' . QuestionOption::count());
    }
}
