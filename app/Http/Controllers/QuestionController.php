<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuestionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'question_text' => 'required|string',
            'question_type' => 'required|string|in:multiple_choice,true_false,enumeration,short_answer',
            'points' => 'required|integer|min:1',
            'correct_answer' => 'nullable|string',
            'options' => 'nullable|array',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'required|boolean',
        ]);

        $question = Question::create([
            'quiz_id' => $validated['quiz_id'],
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'points' => $validated['points'],
            'correct_answer' => $validated['correct_answer'] ?? null,
        ]);

        // Create options if provided, or auto-create for true/false questions
        if (isset($validated['options']) && !empty($validated['options'])) {
            foreach ($validated['options'] as $option) {
                $question->options()->create($option);
            }
        } elseif ($validated['question_type'] === 'true_false') {
            // Auto-create True/False options if not provided
            $correctAnswer = $validated['correct_answer'] ?? 'true';
            // Handle empty string as well
            if (empty($correctAnswer)) {
                $correctAnswer = 'true';
            }
            $isTrue = in_array(strtolower(trim($correctAnswer)), ['true', 't', '1', 'yes', 'y']);
            
            $question->options()->create([
                'option_text' => 'True',
                'is_correct' => $isTrue
            ]);
            
            $question->options()->create([
                'option_text' => 'False',
                'is_correct' => !$isTrue
            ]);
        }

        return redirect()->back()->with('success', 'Question added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|string|in:multiple_choice,true_false,enumeration,short_answer',
            'points' => 'required|integer|min:1',
            'correct_answer' => 'nullable|string',
            'options' => 'nullable|array',
            'options.*.id' => 'nullable|exists:question_options,id',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'required|boolean',
        ]);

        $question->update([
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'points' => $validated['points'],
            'correct_answer' => $validated['correct_answer'] ?? null,
        ]);

        // Update or create options
        if (isset($validated['options'])) {
            // Delete removed options
            $optionIds = collect($validated['options'])->pluck('id')->filter();
            $question->options()->whereNotIn('id', $optionIds)->delete();

            // Update or create options
            foreach ($validated['options'] as $option) {
                if (isset($option['id'])) {
                    $question->options()->where('id', $option['id'])->update([
                        'option_text' => $option['option_text'],
                        'is_correct' => $option['is_correct'],
                    ]);
                } else {
                    $question->options()->create([
                        'option_text' => $option['option_text'],
                        'is_correct' => $option['is_correct'],
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        $question->delete();

        return redirect()->back()->with('success', 'Question deleted successfully.');
    }
}
