<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Activity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class QuizCsvUploadService
{
    /**
     * Process CSV file and create quiz with questions and options
     *
     * @param UploadedFile $file
     * @param int $activityId
     * @param string $quizTitle
     * @param string|null $quizDescription
     * @return array
     * @throws Exception
     */
    public function processQuizCsv(UploadedFile $file, int $activityId, string $quizTitle, ?string $quizDescription = null): array
    {
        // Validate file
        $this->validateCsvFile($file);

        // Parse CSV data
        $csvData = $this->parseCsvFile($file);

        // Validate CSV structure
        $this->validateCsvStructure($csvData);

        DB::beginTransaction();
        
        try {
            // Check if activity already has a quiz before processing
            $activity = Activity::find($activityId);
            $hadExistingQuiz = $activity && $activity->quiz;
            
            // Get or create the quiz
            $quiz = $this->getOrCreateQuiz($activityId, $quizTitle, $quizDescription);

            // Process each question row
            $questionCount = 0;
            foreach ($csvData as $index => $row) {
                if ($index === 0) continue; // Skip header row
                
                $this->createQuestionFromRow($quiz->id, $row, $index);
                $questionCount++;
            }

            DB::commit();

            // Determine if quiz was created or updated
            $message = $hadExistingQuiz 
                ? "Added {$questionCount} questions to existing quiz successfully"
                : "Quiz created successfully with {$questionCount} questions";

            return [
                'success' => true,
                'message' => $message,
                'quiz_id' => $quiz->id,
                'questions_count' => $questionCount,
                'was_existing_quiz' => $hadExistingQuiz
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to process CSV: " . $e->getMessage());
        }
    }

    /**
     * Validate uploaded CSV file
     */
    private function validateCsvFile(UploadedFile $file): void
    {
        $validator = Validator::make([
            'file' => $file
        ], [
            'file' => 'required|file|mimes:csv,txt|max:512000'
        ]);

        if ($validator->fails()) {
            throw new Exception('Invalid file: ' . implode(', ', $validator->errors()->all()));
        }
    }

    /**
     * Parse CSV file into array
     */
    private function parseCsvFile(UploadedFile $file): array
    {
        $csvData = [];
        $handle = fopen($file->getRealPath(), 'r');
        
        if ($handle === false) {
            throw new Exception('Unable to read CSV file');
        }

        while (($row = fgetcsv($handle)) !== false) {
            $csvData[] = $row;
        }
        
        fclose($handle);

        if (empty($csvData)) {
            throw new Exception('CSV file is empty');
        }

        return $csvData;
    }

    /**
     * Validate CSV structure
     */
    private function validateCsvStructure(array $csvData): void
    {
        if (count($csvData) < 2) {
            throw new Exception('CSV must contain at least header and one data row');
        }

        $expectedHeaders = [
            'Question Number', 'quiz_text', 'quiz_type', 'points', 
            'correct_answer1', 'answer2', 'answer3', 'answer4'
        ];

        $headers = $csvData[0];
        
        if (count($headers) < count($expectedHeaders)) {
            throw new Exception('CSV must contain all required columns: ' . implode(', ', $expectedHeaders));
        }

        // Validate each data row has the minimum required columns
        for ($i = 1; $i < count($csvData); $i++) {
            if (count($csvData[$i]) < count($expectedHeaders)) {
                throw new Exception("Row " . ($i + 1) . " has insufficient columns");
            }
        }
    }

    /**
     * Get existing quiz or create new one
     */
    private function getOrCreateQuiz(int $activityId, string $title, ?string $description): Quiz
    {
        // Verify activity exists
        $activity = Activity::find($activityId);
        if (!$activity) {
            throw new Exception("Activity with ID {$activityId} not found");
        }

        // Check if activity already has a quiz
        $existingQuiz = $activity->quiz;
        
        if ($existingQuiz) {
            // Activity already has a quiz, return it (we'll add questions to it)
            return $existingQuiz;
        }

        // No existing quiz, create a new one
        return Quiz::create([
            'activity_id' => $activityId,
            'created_by' => auth()->id(),
            'title' => $title,
            'description' => $description
        ]);
    }

    /**
     * Create question from CSV row
     */
    private function createQuestionFromRow(int $quizId, array $row, int $rowIndex): void
    {
        // Map CSV columns (adjust indices based on your CSV structure)
        $questionNumber = $row[0] ?? null;
        $questionText = $row[1] ?? '';
        $questionType = $row[2] ?? 'multiple_choice';
        $points = (int)($row[3] ?? 1);
        
        // Answer options (columns 4-7: correct_answer1, answer2, answer3, answer4)
        $answers = [
            $row[4] ?? '', // correct_answer1 (marked as correct)
            $row[5] ?? '', // answer2
            $row[6] ?? '', // answer3
            $row[7] ?? ''  // answer4
        ];

        // Validate required fields
        if (empty($questionText)) {
            throw new Exception("Question text is required for row " . ($rowIndex + 1));
        }

        // Determine question type mapping
        $questionTypeMap = [
            'multiple_cl' => 'multiple_choice',
            'multiple_choice' => 'multiple_choice',
            'true_false' => 'true_false',
            'enumeration' => 'enumeration',
            'short_answer' => 'short_answer'
        ];

        $mappedQuestionType = $questionTypeMap[$questionType] ?? 'multiple_choice';

        // Create the question
        $question = Question::create([
            'quiz_id' => $quizId,
            'question_text' => $questionText,
            'question_type' => $mappedQuestionType,
            'points' => $points,
            'correct_answer' => $mappedQuestionType === 'true_false' ? ($answers[0] ?? 'true') : null
        ]);

        // Create question options for multiple choice and true/false questions
        if ($mappedQuestionType === 'multiple_choice') {
            $this->createQuestionOptions($question->id, $answers);
        } elseif ($mappedQuestionType === 'true_false') {
            $this->createTrueFalseOptions($question->id, $answers[0] ?? 'true');
        }
    }

    /**
     * Create question options
     */
    private function createQuestionOptions(int $questionId, array $answers): void
    {
        // Remove empty answers
        $validAnswers = array_filter($answers, fn($answer) => !empty(trim($answer)));
        
        if (empty($validAnswers)) {
            throw new Exception("At least one answer option is required for multiple choice questions");
        }

        // Shuffle answers to randomize order, but keep track of which one is correct
        $correctAnswer = $validAnswers[0]; // First answer is always correct based on CSV structure
        shuffle($validAnswers);

        foreach ($validAnswers as $answer) {
            QuestionOption::create([
                'question_id' => $questionId,
                'option_text' => trim($answer),
                'is_correct' => $answer === $correctAnswer // Mark the correct answer
            ]);
        }
    }

    /**
     * Create True/False options
     */
    private function createTrueFalseOptions(int $questionId, string $correctAnswer): void
    {
        // Normalize the correct answer
        $correctAnswer = strtolower(trim($correctAnswer));
        $isTrue = in_array($correctAnswer, ['true', 't', '1', 'yes', 'y']);

        // Create True option
        QuestionOption::create([
            'question_id' => $questionId,
            'option_text' => 'True',
            'is_correct' => $isTrue
        ]);

        // Create False option
        QuestionOption::create([
            'question_id' => $questionId,
            'option_text' => 'False',
            'is_correct' => !$isTrue
        ]);
    }

    /**
     * Get expected CSV format as example
     */
    public function getCsvFormatExample(): array
    {
        return [
            'headers' => [
                'Question Number', 'quiz_text', 'quiz_type', 'points',
                'correct_answer1', 'answer2', 'answer3', 'answer4'
            ],
            'example_row' => [
                '1', '1+1?', 'multiple_choice', '1', '2', '5', '4', '3'
            ],
            'description' => [
                'Question Number' => 'Sequential number for the question',
                'quiz_text' => 'The actual question text',
                'quiz_type' => 'Type of question (multiple_choice, true_false, etc.)',
                'points' => 'Points awarded for correct answer',
                'correct_answer1' => 'The correct answer (will be marked as correct)',
                'answer2-4' => 'Additional answer options (incorrect answers)'
            ]
        ];
    }
}