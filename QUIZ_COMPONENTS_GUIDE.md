# Quiz Taking Components - Implementation Guide

## Overview
This guide outlines the implementation of the remaining components needed for the student quiz system.

## Components to Create

### 1. Student/QuizTaking.vue

**Purpose**: Interface for students to take quizzes

**Props Interface**:
```typescript
interface Props {
  activity: Activity;
  quiz: Quiz & {
    questions: (Question & {
      options?: QuestionOption[];
    })[];
  };
  progress: StudentQuizProgress;
}
```

**Key Features**:
- Question display with options
- Answer selection (multiple choice, true/false, text input)
- Progress indicator (e.g., "Question 3 of 10")
- Previous/Next navigation
- Submit answer button
- Quiz submission modal with confirmation
- Timer display (optional)

**API Calls**:
```typescript
// Submit individual answer
router.post(route('student.quiz.answer', progress.id), {
  question_id: questionId,
  selected_option_id: optionId, // for multiple choice
  answer_text: answerText, // for enumeration/short answer
});

// Submit entire quiz
router.post(route('student.quiz.submit', progress.id), {}, {
  onSuccess: () => {
    router.visit(route('student.quiz.results', progress.id));
  }
});
```

**State Management**:
```typescript
const currentQuestionIndex = ref(0);
const answers = ref<Record<number, any>>({});
const currentQuestion = computed(() => quiz.questions[currentQuestionIndex.value]);
const isLastQuestion = computed(() => currentQuestionIndex.value === quiz.questions.length - 1);
const answeredCount = computed(() => Object.keys(answers.value).length);
```

**UI Components Needed**:
- Question card with type indicator
- Multiple choice radio buttons
- True/False buttons
- Text area for enumeration/short answer
- Progress bar
- Navigation buttons
- Submit confirmation dialog

---

### 2. Student/QuizResults.vue

**Purpose**: Display quiz results and review answers

**Props Interface**:
```typescript
interface Props {
  progress: StudentQuizProgress & {
    quiz: Quiz;
    activity: Activity;
    answers: (StudentQuizAnswer & {
      question: Question & {
        options?: QuestionOption[];
      };
      selectedOption?: QuestionOption;
    })[];
  };
}
```

**Key Features**:
- Overall score display (score/total points, percentage)
- Pass/fail status (if applicable)
- Time spent on quiz
- Question-by-question review:
  - Question text
  - Student's answer
  - Correct answer (for auto-graded questions)
  - Points earned
  - Correctness indicator (✓ or ✗)
- Back to course button

**Computed Properties**:
```typescript
const scorePercentage = computed(() => progress.percentage_score);
const totalQuestions = computed(() => progress.total_questions);
const correctAnswers = computed(() => 
  progress.answers.filter(a => a.is_correct).length
);
const passed = computed(() => scorePercentage.value >= 70); // Adjust threshold
```

**UI Components Needed**:
- Score summary card
- Results stats (time, questions, score)
- Answer review list with color coding
- Back to course navigation

---

### 3. Update Student/Courses.vue (Already Done ✅)

**Status**: Complete - Now using Inertia props from backend

**Navigation to Quiz**:
When clicking on a course, navigate to course detail view:
```typescript
const goToCourse = (courseId: number) => {
  router.visit(route('student.courses.show', courseId));
};
```

---

### 4. Update or Create Student/CourseDetail.vue

**Purpose**: Display course modules, lessons, and activities with quiz access

**Props Interface**:
```typescript
interface Props {
  course: Course & {
    modules: (Module & {
      lessons: Lesson[];
      activities: (Activity & {
        quiz?: Quiz & {
          question_count: number;
          total_points: number;
        };
        quiz_progress?: StudentQuizProgress | null;
      })[];
    })[];
  };
  enrollment: CourseEnrollment;
}
```

**Key Features**:
- Course header with title, instructor, progress
- Module accordion/tabs
- Lesson list with completion status
- Activity cards showing:
  - Activity type (quiz, assignment, etc.)
  - Quiz info (question count, points)
  - Progress status (not started, in progress, completed)
  - Score (if completed)
  - Action button:
    - "Start Quiz" (not started)
    - "Continue Quiz" (in progress)
    - "Review Results" (completed)

**Quiz Button Logic**:
```typescript
const getQuizButtonText = (activity: Activity) => {
  const progress = activity.quiz_progress;
  if (!progress) return 'Start Quiz';
  if (progress.is_completed) return 'Review Results';
  return 'Continue Quiz';
};

const getQuizRoute = (activity: Activity) => {
  const progress = activity.quiz_progress;
  if (progress?.is_completed) {
    return route('student.quiz.results', progress.id);
  }
  return route('student.quiz.start', activity.id);
};

const startQuiz = (activity: Activity) => {
  router.visit(getQuizRoute(activity));
};
```

---

## Implementation Steps

### Step 1: Create QuizTaking.vue
```bash
# Create component file
resources/js/Pages/Student/QuizTaking.vue
```

1. Set up props interface with Activity, Quiz, Progress
2. Create state for current question index and answers
3. Implement question display logic
4. Add answer selection UI for each question type
5. Implement navigation (previous/next)
6. Add submit answer API call
7. Implement quiz submission with confirmation
8. Add progress indicator and timer

### Step 2: Create QuizResults.vue
```bash
# Create component file
resources/js/Pages/Student/QuizResults.vue
```

1. Set up props interface with Progress and nested data
2. Display score summary card
3. Implement question review list
4. Add color coding for correct/incorrect answers
5. Show correct answers for comparison
6. Add back to course navigation

### Step 3: Update CourseDetail.vue (if exists)
```bash
# Update or create
resources/js/Pages/Student/CourseDetail.vue
```

1. Check if component exists, create if not
2. Set up props with course and modules
3. Display module/lesson structure
4. Add activity cards with quiz information
5. Implement quiz button logic (start/continue/review)
6. Add progress indicators for quizzes

### Step 4: Test Complete Flow
```bash
# Run migrations
php artisan migrate

# Seed sample data
php artisan db:seed --class=StudentQuizProgressSeeder

# Build frontend
npm run build

# Start server
php artisan serve
```

**Testing Checklist**:
1. Log in as student
2. Navigate to courses page
3. Click on a course
4. View modules and activities
5. Click "Start Quiz" on quiz activity
6. Answer questions
7. Submit quiz
8. View results
9. Navigate back to course

---

## Component Communication Flow

```
Student/Courses.vue (List)
    ↓ (click course)
Student/CourseDetail.vue (Modules/Activities)
    ↓ (click "Start Quiz")
Student/QuizTaking.vue (Take Quiz)
    ↓ (submit quiz)
Student/QuizResults.vue (View Results)
    ↓ (back to course)
Student/CourseDetail.vue
```

---

## API Endpoints Reference

### Already Implemented in StudentQuizController:
- `GET /student/quiz/start/{activity}` → Creates progress, renders QuizTaking
- `POST /student/quiz/{progress}/answer` → Saves answer, auto-grades
- `POST /student/quiz/{progress}/submit` → Finalizes quiz, calculates score
- `GET /student/quiz/{progress}/results` → Renders QuizResults
- `GET /student/quiz/{activity}/progress` → JSON API for progress data

### Already Implemented in StudentCourseController:
- `GET /student/courses` → Renders Courses list
- `GET /student/courses/{course}` → Renders CourseDetail with modules/activities

---

## TypeScript Types Reference

All types are defined in `resources/js/types/index.ts`:
- `StudentQuizProgress`
- `StudentQuizAnswer`
- `Activity`
- `Quiz`
- `Question`
- `QuestionOption`
- `Course`
- `Module`
- `Lesson`

---

## UI/UX Considerations

### Quiz Taking:
- Auto-save answers as student progresses (optional)
- Warn before leaving page with unsaved answers
- Clear visual feedback for selected answers
- Disable "Next" until question is answered (optional)
- Show confirmation modal before final submission
- Display progress percentage

### Quiz Results:
- Use color coding: Green (correct), Red (incorrect), Yellow (pending)
- Show pass/fail status prominently
- Display time spent in friendly format (e.g., "5 minutes")
- Allow navigation back to quiz taking for retakes (if enabled)
- Highlight key stats (score, percentage, correct answers)

### Course Detail:
- Use icons for activity types (quiz, assignment, lesson)
- Show completion badges/checkmarks
- Use cards or list items for activities
- Display quiz metadata (questions, points, time limit)
- Clear call-to-action buttons with appropriate states

---

## Sample Code Snippets

### Question Display (QuizTaking.vue)
```vue
<template>
  <div v-if="currentQuestion.question_type === 'multiple-choice'">
    <div v-for="option in currentQuestion.options" :key="option.id">
      <label class="flex items-center space-x-2">
        <input
          type="radio"
          :name="`question-${currentQuestion.id}`"
          :value="option.id"
          v-model="answers[currentQuestion.id]"
        />
        <span>{{ option.option_text }}</span>
      </label>
    </div>
  </div>

  <div v-else-if="currentQuestion.question_type === 'true-false'">
    <button
      @click="answers[currentQuestion.id] = 'true'"
      :class="{ 'bg-blue-500': answers[currentQuestion.id] === 'true' }"
    >
      True
    </button>
    <button
      @click="answers[currentQuestion.id] = 'false'"
      :class="{ 'bg-blue-500': answers[currentQuestion.id] === 'false' }"
    >
      False
    </button>
  </div>

  <div v-else-if="currentQuestion.question_type === 'enumeration'">
    <textarea
      v-model="answers[currentQuestion.id]"
      placeholder="Enter your answer"
      rows="4"
    />
  </div>
</template>
```

### Submit Quiz (QuizTaking.vue)
```typescript
const submitQuiz = () => {
  if (answeredCount.value < quiz.questions.length) {
    if (!confirm('You have unanswered questions. Submit anyway?')) {
      return;
    }
  }

  router.post(route('student.quiz.submit', progress.id), {}, {
    onSuccess: () => {
      router.visit(route('student.quiz.results', progress.id));
    },
    onError: (errors) => {
      console.error('Quiz submission failed:', errors);
    }
  });
};
```

### Score Display (QuizResults.vue)
```vue
<template>
  <div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold text-center">Quiz Results</h2>
    
    <div class="mt-4 text-center">
      <div class="text-5xl font-bold text-blue-600">
        {{ progress.percentage_score }}%
      </div>
      <div class="text-lg text-gray-600 mt-2">
        {{ progress.score }} / {{ progress.quiz.total_points }} points
      </div>
    </div>

    <div class="mt-6 grid grid-cols-3 gap-4">
      <div class="text-center">
        <div class="text-2xl font-semibold">{{ correctAnswers }}</div>
        <div class="text-sm text-gray-600">Correct</div>
      </div>
      <div class="text-center">
        <div class="text-2xl font-semibold">{{ totalQuestions }}</div>
        <div class="text-sm text-gray-600">Total Questions</div>
      </div>
      <div class="text-center">
        <div class="text-2xl font-semibold">{{ timeSpent }}</div>
        <div class="text-sm text-gray-600">Time Spent</div>
      </div>
    </div>

    <div class="mt-6">
      <div 
        v-if="passed" 
        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"
      >
        ✓ You passed!
      </div>
      <div 
        v-else 
        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"
      >
        ✗ You did not pass. Keep practicing!
      </div>
    </div>
  </div>
</template>
```

---

## Next Actions

1. **Create QuizTaking.vue** - Highest priority
2. **Create QuizResults.vue** - High priority  
3. **Update/Create CourseDetail.vue** - High priority
4. **Run migrations and seeders** - Test data
5. **Test complete flow** - End-to-end testing
6. **Add polish and enhancements** - UI/UX improvements

---

**Document Status**: Ready for implementation
**Dependencies**: All backend components complete, routes added, types defined
**Estimated Time**: 4-6 hours for all three components
