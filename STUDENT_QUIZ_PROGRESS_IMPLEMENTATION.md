# Student Quiz Progress System Implementation

## Overview
Implemented a comprehensive student quiz progress tracking system that allows students to take quizzes, track their progress, and view results. The system supports multiple question types and provides real-time progress tracking.

## Database Structure

### Tables Created

#### 1. `student_quiz_progress`
Tracks overall student progress for each quiz.

**Columns:**
- `id` - Primary key
- `student_id` - Foreign key to users table
- `quiz_id` - Foreign key to quizzes table
- `activity_id` - Foreign key to activities table
- `started_at` - Timestamp when quiz was started
- `last_accessed_at` - Last time student accessed the quiz
- `is_completed` - Boolean flag for completion status
- `is_submitted` - Boolean flag for submission status
- `completed_questions` - Count of answered questions
- `total_questions` - Total questions in quiz
- `score` - Calculated score (decimal 5,2)
- `percentage_score` - Score as percentage (decimal 5,2)
- `time_spent` - Time spent in minutes (integer)
- `created_at`, `updated_at` - Timestamps

**Indexes:**
- `student_id, quiz_id` - For quick lookup of student quiz progress
- `student_id, activity_id` - For activity-based queries

#### 2. `student_quiz_answers`
Stores individual answers to quiz questions.

**Columns:**
- `id` - Primary key
- `student_id` - Foreign key to users table
- `quiz_progress_id` - Foreign key to student_quiz_progress
- `question_id` - Foreign key to questions table
- `selected_option_id` - Foreign key to question_options (nullable, for multiple choice)
- `answer_text` - Text answer (nullable, for text-based questions)
- `is_correct` - Boolean flag for answer correctness
- `points_earned` - Points earned for this answer (decimal 5,2)
- `answered_at` - Timestamp when answered
- `created_at`, `updated_at` - Timestamps

**Indexes:**
- `student_id, quiz_progress_id` - For quick lookup of student answers
- `quiz_progress_id, question_id` - For question-specific queries

## Models

### StudentQuizProgress Model
**Location:** `app/Models/StudentQuizProgress.php`

**Relationships:**
- `student()` - BelongsTo User
- `quiz()` - BelongsTo Quiz (eager loaded)
- `activity()` - BelongsTo Activity
- `answers()` - HasMany StudentQuizAnswer (eager loaded)

**Key Methods:**
- `calculateScore()` - Calculates total score and percentage from answers

**Casts:**
- `started_at`, `last_accessed_at` - datetime
- `is_completed`, `is_submitted` - boolean
- `score`, `percentage_score` - decimal:2

### StudentQuizAnswer Model
**Location:** `app/Models/StudentQuizAnswer.php`

**Relationships:**
- `student()` - BelongsTo User
- `quizProgress()` - BelongsTo StudentQuizProgress
- `question()` - BelongsTo Question (eager loaded)
- `selectedOption()` - BelongsTo QuestionOption

**Key Methods:**
- `checkAnswer()` - Validates answer and calculates points earned
  - Multiple choice: Checks if selected option is correct
  - True/False: Compares answer text with correct answer
  - Enumeration/Short answer: Sets to false (requires manual grading)

**Casts:**
- `is_correct` - boolean
- `points_earned` - decimal:2
- `answered_at` - datetime

## Controllers

### StudentQuizController
**Location:** `app/Http/Controllers/Student/StudentQuizController.php`

**Methods:**

1. **`start($activityId)`** - GET
   - Creates or retrieves quiz progress for student
   - Returns Inertia page with quiz data and progress
   - Route: `/student/quiz/start/{activityId}`

2. **`submitAnswer($progressId)`** - POST
   - Saves student's answer to a question
   - Auto-validates answer for multiple choice and true/false
   - Updates progress completion count
   - Returns JSON response with answer and progress
   - Route: `/student/quiz/{progressId}/answer`

3. **`submit($progressId)`** - POST
   - Finalizes quiz submission
   - Marks quiz as completed and submitted
   - Calculates final score
   - Redirects to results page
   - Route: `/student/quiz/{progressId}/submit`

4. **`results($progressId)`** - GET
   - Shows quiz results with answers and score
   - Returns Inertia page with detailed results
   - Route: `/student/quiz/{progressId}/results`

5. **`getProgress($activityId)`** - GET
   - API endpoint to fetch current progress
   - Returns JSON with progress and answers
   - Route: `/api/student/quiz/{activityId}/progress`

### StudentCourseController (Updated)
**Location:** `app/Http/Controllers/Student/StudentCourseController.php`

**Updated `show()` method:**
- Now loads modules with activities
- Includes quiz progress for each activity
- Provides quiz metadata (question count, total points)
- Returns progress data (score, percentage, completion status)

**Data Structure Returned:**
```php
'modules' => [
    [
        'id' => 1,
        'title' => 'Module Title',
        'description' => 'Description',
        'module_type' => 'Quizzes',
        'lessons' => [...],
        'activities' => [
            [
                'id' => 1,
                'title' => 'Quiz Title',
                'activity_type' => [...],
                'question_count' => 10,
                'total_points' => 100,
                'quiz_progress' => [
                    'id' => 1,
                    'is_completed' => true,
                    'score' => 85,
                    'percentage_score' => 85.00,
                    'completed_questions' => 10,
                    'total_questions' => 10,
                ] or null
            ]
        ]
    ]
]
```

## Seeder

### StudentQuizProgressSeeder
**Location:** `database/seeders/StudentQuizProgressSeeder.php`

**Purpose:** Creates sample quiz progress data for testing

**Features:**
- Finds first student user (role_id = 3)
- Creates progress for up to 3 quizzes
- Generates 3 different states:
  - Not started (no progress record)
  - In progress (partial completion)
  - Completed (all questions answered)
- Creates realistic answers:
  - 70% correct answers
  - Appropriate answer types for each question type
- Calculates scores for completed quizzes

**Usage:**
```bash
php artisan db:seed --class=StudentQuizProgressSeeder
```

## TypeScript Types

### StudentQuizProgress Type
**Location:** `resources/js/types/index.ts`

```typescript
export type StudentQuizProgress = { 
  id: number;
  student_id: number;
  quiz_id: number;
  activity_id: number;
  quiz: Quiz;
  answers: Array<StudentQuizAnswer>;
  started_at: string;
  last_accessed_at: string;
  is_completed: boolean;
  is_submitted: boolean;
  completed_questions: number;
  total_questions: number;
  score?: number;
  percentage_score?: number;
  time_spent?: number;
  created_at: string;
  updated_at: string;
}
```

### StudentQuizAnswer Type
```typescript
export type StudentQuizAnswer = {
  id: number;
  student_id: number;
  quiz_progress_id: number;
  question_id: number;
  question: Question;
  selected_option_id?: number;
  answer_text?: string;
  is_correct: boolean;
  points_earned: number;
  answered_at: string;
  created_at: string;
  updated_at: string;
}
```

## Required Routes

Add these routes to `routes/web.php`:

```php
// Student Quiz Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    // Quiz taking
    Route::get('/quiz/start/{activity}', [StudentQuizController::class, 'start'])->name('quiz.start');
    Route::post('/quiz/{progress}/answer', [StudentQuizController::class, 'submitAnswer'])->name('quiz.answer');
    Route::post('/quiz/{progress}/submit', [StudentQuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz/{progress}/results', [StudentQuizController::class, 'results'])->name('quiz.results');
    Route::get('/quiz/{activity}/progress', [StudentQuizController::class, 'getProgress'])->name('quiz.progress');
});
```

## Frontend Implementation Needed

### 1. Student/QuizTaking.vue
**Purpose:** Main quiz taking interface

**Required Props:**
- `activity` - Activity object with quiz
- `quiz` - Quiz object with questions
- `progress` - StudentQuizProgress object

**Features Needed:**
- Display questions one at a time or all at once
- Handle different question types (multiple choice, true/false, etc.)
- Save answers as student progresses
- Show progress indicator (X of Y questions answered)
- Submit quiz button
- Timer (optional)
- Ability to review and change answers before submission

### 2. Student/QuizResults.vue
**Purpose:** Display quiz results after submission

**Required Props:**
- `progress` - StudentQuizProgress with answers
- `activity` - Activity object

**Features Needed:**
- Display final score and percentage
- Show all questions with student's answers
- Highlight correct/incorrect answers
- Show correct answers for review
- Option to retake quiz (if allowed)
- Back to course button

### 3. Updated Student/CourseDetail.vue
**Purpose:** Show course modules with quizzes

**Updates Needed:**
- Display modules with their activities
- Show quiz cards with:
  - Question count
  - Total points
  - Progress status (not started, in progress, completed)
  - Score (if completed)
  - "Start Quiz" or "Continue Quiz" button
- Handle click to start/continue quiz

## Migration Commands

```bash
# Run migrations
php artisan migrate

# Run seeder (after creating quizzes and students)
php artisan db:seed --class=StudentQuizProgressSeeder
```

## Testing Checklist

### Backend
- [ ] Student can start a new quiz
- [ ] Student can continue an existing quiz
- [ ] Student can submit answers
- [ ] Answers are validated correctly for multiple choice
- [ ] Answers are validated correctly for true/false
- [ ] Score is calculated correctly
- [ ] Student can only access their own quiz progress
- [ ] Quiz results display correctly

### Frontend  
- [ ] Quiz taking interface loads properly
- [ ] Student can select/enter answers
- [ ] Progress is saved in real-time
- [ ] Submit button works
- [ ] Results page shows correct information
- [ ] Course detail page shows quiz cards
- [ ] Quiz progress displays correctly

## Security Considerations

1. **Authorization:** Students can only access their own quiz progress
2. **Validation:** All answers are validated server-side
3. **Submission:** Once submitted, quiz cannot be resubmitted (unless explicitly allowed)
4. **Progress Tracking:** Last accessed time prevents stale data

## Future Enhancements

1. **Time Limits:** Add time limits per quiz
2. **Attempt Limits:** Limit number of attempts per quiz
3. **Question Randomization:** Randomize question and option order
4. **Partial Credit:** Award partial points for partially correct answers
5. **Manual Grading:** Interface for teachers to grade short answer/enumeration questions
6. **Quiz Analytics:** Detailed analytics on student performance
7. **Retake Policy:** Configure retake policies per quiz
8. **Question Bank:** Pull random questions from a question bank

## Summary

✅ Database migrations created
✅ Models with relationships implemented
✅ Controllers with full CRUD operations
✅ Seeder for sample data
✅ TypeScript types defined
✅ StudentCourseController updated with quiz data
✅ Documentation complete

**Next Steps:**
1. Run migrations: `php artisan migrate`
2. Create quiz data and student users
3. Run seeder: `php artisan db:seed --class=StudentQuizProgressSeeder`
4. Add routes to `web.php`
5. Create frontend Vue components (QuizTaking.vue, QuizResults.vue)
6. Update CourseDetail.vue to display quiz information
7. Test the complete flow
