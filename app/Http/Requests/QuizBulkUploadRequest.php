<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuizBulkUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check(); // User must be authenticated
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $activity = \App\Models\Activity::find($this->input('activity_id'));
        $hasExistingQuiz = $activity && $activity->quiz()->exists();

        return [
            'activity_id' => 'required|exists:activities,id',
            'quiz_title' => $hasExistingQuiz ? 'nullable|string|max:255|min:3' : 'required|string|max:255|min:3',
            'quiz_description' => 'nullable|string|max:1000',
            'csv_file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:512000', // 500MB max
                function ($attribute, $value, $fail) {
                    // Additional CSV validation
                    if ($value && $value->isValid()) {
                        $handle = fopen($value->getRealPath(), 'r');
                        if ($handle) {
                            $firstLine = fgetcsv($handle);
                            fclose($handle);
                            
                            $requiredHeaders = [
                                'Question Number', 'quiz_text', 'quiz_type', 'points', 
                                'correct_answer1', 'answer2', 'answer3', 'answer4'
                            ];
                            
                            if (!$firstLine || count($firstLine) < count($requiredHeaders)) {
                                $fail('The CSV file must contain the required headers: ' . implode(', ', $requiredHeaders));
                            }
                        }
                    }
                }
            ]
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'activity_id.required' => 'Please select an activity for this quiz.',
            'activity_id.exists' => 'The selected activity does not exist.',
            'quiz_title.required' => 'Quiz title is required when creating a new quiz.',
            'quiz_title.min' => 'Quiz title must be at least 3 characters long.',
            'quiz_title.max' => 'Quiz title cannot exceed 255 characters.',
            'quiz_description.max' => 'Quiz description cannot exceed 1000 characters.',
            'csv_file.required' => 'Please upload a CSV file.',
            'csv_file.mimes' => 'The file must be a CSV file (.csv or .txt).',
            'csv_file.max' => 'The CSV file cannot be larger than 500MB.',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'activity_id' => 'activity',
            'quiz_title' => 'quiz title',
            'quiz_description' => 'quiz description',
            'csv_file' => 'CSV file',
        ];
    }
}
