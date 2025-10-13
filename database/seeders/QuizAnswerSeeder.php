<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentQuizProgress;
use App\Models\StudentQuizAnswer;
use App\Models\Question;
use App\Models\QuestionOption;
use Carbon\Carbon;

class QuizAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🎯 Creating Quiz Answers for Existing Quiz Progress...\n";
        echo "═══════════════════════════════════════════════════════\n";

        // Get all completed quiz progress records that don't have answers yet
        $quizProgressRecords = StudentQuizProgress::where('is_completed', true)
            ->whereDoesntHave('answers')
            ->with(['student', 'quiz'])
            ->get();

        if ($quizProgressRecords->isEmpty()) {
            echo "✅ No quiz progress records without answers found.\n";
            return;
        }

        echo "Found {$quizProgressRecords->count()} completed quiz progress records without answers.\n\n";

        $totalAnswersCreated = 0;

        foreach ($quizProgressRecords as $progress) {
            $student = $progress->student;
            $quiz = $progress->quiz;

            if (!$student || !$quiz) {
                continue;
            }

            $quizName = $quiz->title ?: 'Quiz ID ' . $quiz->id;
            echo "📝 Creating answers for Student: {$student->name} (Quiz: {$quizName})\n";

            $questions = Question::where('quiz_id', $quiz->id)->with('options')->get();
            $answersCreated = $this->createQuizAnswers($progress, $questions, $progress->score);
            
            $totalAnswersCreated += $answersCreated;
            echo "   ✓ Created {$answersCreated} answers\n";
        }

        echo "\n🎉 Quiz answer seeding completed!\n";
        echo "═══════════════════════════════════════════════════════\n";
        echo "📊 Summary:\n";
        echo "• Quiz Progress Records Processed: {$quizProgressRecords->count()}\n";
        echo "• Total Quiz Answers Created: {$totalAnswersCreated}\n";
        echo "• Current Total Quiz Answers: " . StudentQuizAnswer::count() . "\n";
    }

    /**
     * Create individual quiz answers for a quiz progress record
     */
    private function createQuizAnswers($quizProgress, $questions, $overallScore)
    {
        if ($questions->isEmpty()) {
            return 0;
        }

        $correctAnswersCount = 0;
        $totalQuestions = $questions->count();
        $answersCreated = 0;

        // Calculate target correct answers based on overall score
        $targetCorrectCount = round(($overallScore / 100) * $totalQuestions);

        foreach ($questions as $question) {
            $options = $question->options;
            if ($options->isEmpty()) {
                continue;
            }

            // Determine if this answer should be correct
            $shouldBeCorrect = $correctAnswersCount < $targetCorrectCount;

            // Find correct and incorrect options
            $correctOption = $options->where('is_correct', true)->first();
            $incorrectOptions = $options->where('is_correct', false);

            // Choose the option to select
            $selectedOption = null;
            $isCorrect = false;

            if ($shouldBeCorrect && $correctOption) {
                $selectedOption = $correctOption;
                $isCorrect = true;
                $correctAnswersCount++;
            } elseif ($incorrectOptions->isNotEmpty()) {
                $selectedOption = $incorrectOptions->random();
                $isCorrect = false;
            } elseif ($correctOption) {
                // Fallback if no incorrect options
                $selectedOption = $correctOption;
                $isCorrect = true;
                $correctAnswersCount++;
            }

            // Handle different question types
            $answerData = [
                'student_id' => $quizProgress->student_id,
                'quiz_progress_id' => $quizProgress->id,
                'question_id' => $question->id,
                'answered_at' => $quizProgress->started_at 
                    ? $quizProgress->started_at->addMinutes(rand(5, 30))
                    : Carbon::now()->subDays(rand(1, 30)),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            // Handle question types differently
            if ($question->question_type === 'enumeration') {
                // For enumeration, use answer_text and set is_correct to null (pending review)
                $sampleEnumAnswers = [
                    'PHP, JavaScript, Python',
                    'HTML, CSS, JavaScript', 
                    'Java, C++, Python',
                    'React, Vue, Angular'
                ];
                $answerData['selected_option_id'] = null;
                $answerData['answer_text'] = $sampleEnumAnswers[array_rand($sampleEnumAnswers)];
                $answerData['is_correct'] = null; // Pending instructor review
                $answerData['points_earned'] = 0; // Pending review
            } elseif ($question->question_type === 'short-answer') {
                // For short answer, use answer_text and set is_correct to null (pending review)
                $sampleShortAnswers = [
                    'A programming language is a formal language comprising a set of instructions.',
                    'Variables are containers for storing data values.',
                    'Functions are reusable blocks of code.',
                    'Classes are blueprints for creating objects.'
                ];
                $answerData['selected_option_id'] = null;
                $answerData['answer_text'] = $sampleShortAnswers[array_rand($sampleShortAnswers)];
                $answerData['is_correct'] = null; // Pending instructor review
                $answerData['points_earned'] = 0; // Pending review
            } else {
                // For multiple-choice and true-false, use selected option
                if ($selectedOption) {
                    $answerData['selected_option_id'] = $selectedOption->id;
                    $answerData['answer_text'] = null; // Not used for option-based questions
                    $answerData['is_correct'] = $isCorrect;
                    $answerData['points_earned'] = $isCorrect ? 1 : 0;
                } else {
                    continue; // Skip if no option selected
                }
            }

            StudentQuizAnswer::create($answerData);
            $answersCreated++;
        }

        return $answersCreated;
    }
}
