<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;

class QuizDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fix Activity 19 (Probability Quiz) - ensure it has proper quiz with questions and options
        $activity19 = Activity::find(19);
        if ($activity19) {
            // Delete existing quiz if any
            if ($activity19->quiz) {
                $activity19->quiz->questions()->each(function ($question) {
                    $question->options()->delete();
                    $question->delete();
                });
                $activity19->quiz->delete();
            }

            // Create new quiz
            $quiz = Quiz::create([
                'activity_id' => 19,
                'created_by' => 1,
                'title' => 'Probability Quiz',
                'description' => 'Basic probability concepts quiz for Chemistry Fundamentals'
            ]);

            // Create Question 1
            $question1 = Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => 'What is the probability of getting heads in a fair coin toss?',
                'question_type' => 'multiple_choice',
                'points' => 1
            ]);

            // Create options for Question 1
            QuestionOption::create([
                'question_id' => $question1->id,
                'option_text' => '0.25',
                'is_correct' => false
            ]);

            QuestionOption::create([
                'question_id' => $question1->id,
                'option_text' => '0.5',
                'is_correct' => true
            ]);

            QuestionOption::create([
                'question_id' => $question1->id,
                'option_text' => '0.75',
                'is_correct' => false
            ]);

            QuestionOption::create([
                'question_id' => $question1->id,
                'option_text' => '1.0',
                'is_correct' => false
            ]);

            // Create Question 2 for more content
            $question2 = Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => 'If you roll a standard 6-sided die, what is the probability of getting a number greater than 4?',
                'question_type' => 'multiple_choice',
                'points' => 2
            ]);

            // Create options for Question 2
            QuestionOption::create([
                'question_id' => $question2->id,
                'option_text' => '1/6',
                'is_correct' => false
            ]);

            QuestionOption::create([
                'question_id' => $question2->id,
                'option_text' => '2/6 or 1/3',
                'is_correct' => true
            ]);

            QuestionOption::create([
                'question_id' => $question2->id,
                'option_text' => '3/6 or 1/2',
                'is_correct' => false
            ]);

            QuestionOption::create([
                'question_id' => $question2->id,
                'option_text' => '4/6 or 2/3',
                'is_correct' => false
            ]);

            $this->command->info('Created quiz with 2 questions for Activity 19 (Probability Quiz)');
        }

        // Also fix Activity 4 (Chemistry Fundamentals - Module 1 Quiz) to ensure it has proper questions
        $activity4 = Activity::find(4);
        if ($activity4 && $activity4->quiz) {
            // Check if quiz has proper questions with options
            $quiz = $activity4->quiz;
            
            // Delete existing questions without proper text or options
            $quiz->questions()->each(function ($question) {
                if (empty($question->question_text) || $question->options()->count() == 0) {
                    $question->options()->delete();
                    $question->delete();
                }
            });

            // If no questions left, create some
            if ($quiz->questions()->count() == 0) {
                // Create Question 1
                $question1 = Question::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => 'What is the chemical symbol for Gold?',
                    'question_type' => 'multiple_choice',
                    'points' => 5
                ]);

                // Create options for Question 1
                QuestionOption::create([
                    'question_id' => $question1->id,
                    'option_text' => 'Go',
                    'is_correct' => false
                ]);

                QuestionOption::create([
                    'question_id' => $question1->id,
                    'option_text' => 'Au',
                    'is_correct' => true
                ]);

                QuestionOption::create([
                    'question_id' => $question1->id,
                    'option_text' => 'Ag',
                    'is_correct' => false
                ]);

                QuestionOption::create([
                    'question_id' => $question1->id,
                    'option_text' => 'Gd',
                    'is_correct' => false
                ]);

                // Create Question 2
                $question2 = Question::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => 'What is the atomic number of Carbon?',
                    'question_type' => 'multiple_choice',
                    'points' => 10
                ]);

                // Create options for Question 2
                QuestionOption::create([
                    'question_id' => $question2->id,
                    'option_text' => '4',
                    'is_correct' => false
                ]);

                QuestionOption::create([
                    'question_id' => $question2->id,
                    'option_text' => '6',
                    'is_correct' => true
                ]);

                QuestionOption::create([
                    'question_id' => $question2->id,
                    'option_text' => '8',
                    'is_correct' => false
                ]);

                QuestionOption::create([
                    'question_id' => $question2->id,
                    'option_text' => '12',
                    'is_correct' => false
                ]);

                $this->command->info('Created proper questions for Activity 4 (Chemistry Fundamentals - Module 1 Quiz)');
            }
        }

        $this->command->info('QuizDataSeeder completed successfully!');
    }
}