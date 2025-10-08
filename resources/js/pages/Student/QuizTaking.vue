<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import { Activity, Quiz, Question, QuestionOption, StudentQuizProgress } from '@/types';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
// import { Textarea } from '@/components/ui/textarea';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';

interface Props {
  activity: Activity;
  quiz: Quiz & {
    total_points: number;
    questions: (Question & {
      options?: QuestionOption[];
    })[];
  };
  progress: StudentQuizProgress;
}

const props = defineProps<Props>();

// State
const currentQuestionIndex = ref(0);
const answers = ref<Record<number, any>>({});
const showSubmitDialog = ref(false);
const isSubmitting = ref(false);

// Client-side protection: Check if quiz is completed or past due
onMounted(() => {
  // Check if quiz is already completed
  if (props.progress.is_completed) {
    router.visit(`/student/quiz/${props.progress.id}/results`, {
      replace: true,
      onError: () => {
        // Fallback if results route fails
        router.visit('/student/courses', { replace: true });
      }
    });
    return;
  }

  // Check if quiz is past due (use activity due_date or fallback to created_at + 7 days)
  const dueDate = props.activity.due_date 
    ? new Date(props.activity.due_date)
    : new Date(new Date(props.activity.created_at).getTime() + (7 * 24 * 60 * 60 * 1000));
  const now = new Date();
  
  if (now > dueDate) {
    alert(`Quiz deadline has passed. This quiz was due on ${dueDate.toLocaleDateString()} at ${dueDate.toLocaleTimeString()}.`);
    router.visit('/student/courses', { replace: true });
    return;
  }

  // Prevent browser back button from accessing completed/past due quizzes
  const handlePopState = () => {
    if (props.progress.is_completed) {
      router.visit(`/student/quiz/${props.progress.id}/results`, { replace: true });
    }
  };
  
  window.addEventListener('popstate', handlePopState);
  
  // Cleanup listener when component unmounts
  return () => {
    window.removeEventListener('popstate', handlePopState);
  };
});

// Initialize answers from existing progress
if (props.progress.answers && props.progress.answers.length > 0) {
  props.progress.answers.forEach((answer: any) => {
    if (answer.selected_option_id) {
      answers.value[answer.question_id] = answer.selected_option_id;
    } else if (answer.answer_text) {
      answers.value[answer.question_id] = answer.answer_text;
    }
  });
}

// Computed properties
const currentQuestion = computed(() => props.quiz.questions[currentQuestionIndex.value]);
const isLastQuestion = computed(() => currentQuestionIndex.value === props.quiz.questions.length - 1);
const isFirstQuestion = computed(() => currentQuestionIndex.value === 0);
const answeredCount = computed(() => Object.keys(answers.value).length);
const progressPercentage = computed(() => 
  (answeredCount.value / props.quiz.questions.length) * 100
);
const currentAnswer = computed({
  get: () => answers.value[currentQuestion.value.id],
  set: (value) => {
    answers.value[currentQuestion.value.id] = value;
  }
});

// Navigation
const goToQuestion = (index: number) => {
  currentQuestionIndex.value = index;
};

const previousQuestion = () => {
  if (!isFirstQuestion.value) {
    currentQuestionIndex.value--;
  }
};

const nextQuestion = () => {
  // The answer is already auto-saved by the watch, just move to next question
  if (!isLastQuestion.value) {
    // Add small delay to ensure any pending save completes
    setTimeout(() => {
      currentQuestionIndex.value++;
    }, 200);
  }
};

// Submit individual answer
const submitAnswer = async () => {
  const answer = answers.value[currentQuestion.value.id];
  // Check if answer exists (but allow 0 as a valid answer)
  if (answer === undefined || answer === null || answer === '') {
    console.log('No answer to submit');
    return;
  }

  const data: any = {
    question_id: currentQuestion.value.id,
  };

  // Set appropriate field based on question type
  if (currentQuestion.value.question_type === 'multiple-choice' || currentQuestion.value.question_type === 'true-false') {
    data.selected_option_id = answer;
  } else {
    data.answer_text = answer;
  }

  console.log('Submitting answer:', {
    questionId: currentQuestion.value.id,
    questionType: currentQuestion.value.question_type,
    answer: answer,
    data: data
  });

  try {
    await axios.post(`/student/quiz/${props.progress.id}/answer`, data, {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      }
    });
    console.log('Answer submitted successfully');
  } catch (error: any) {
    console.error('Failed to submit answer:', error);
    console.error('Answer data:', data);
    console.error('Question type:', currentQuestion.value.question_type);
    console.error('Response data:', error.response?.data);
    console.error('Validation errors:', error.response?.data?.errors);
  }
};

// Check if all questions are answered
const allQuestionsAnswered = computed(() => {
  return props.quiz.questions.every(question => answers.value[question.id] !== undefined && answers.value[question.id] !== '');
});

// Submit entire quiz
const openSubmitDialog = () => {
  if (!allQuestionsAnswered.value) {
    alert('Please answer all questions before submitting the quiz.');
    return;
  }
  showSubmitDialog.value = true;
};

const confirmSubmit = () => {
  isSubmitting.value = true;
  
  router.post(`/student/quiz/${props.progress.id}/submit`, {}, {
    onSuccess: () => {
      router.visit(`/student/quiz/${props.progress.id}/results`);
    },
    onError: (errors) => {
      console.error('Quiz submission failed:', errors);
      isSubmitting.value = false;
      showSubmitDialog.value = false;
    }
  });
};

const cancelSubmit = () => {
  showSubmitDialog.value = false;
};

// Get question type display name
const getQuestionTypeLabel = (type: string) => {
  const labels: Record<string, string> = {
    'multiple-choice': 'Multiple Choice',
    'true-false': 'True or False',
    'enumeration': 'Enumeration',
    'short-answer': 'Short Answer'
  };
  return labels[type] || type;
};

// Auto-save answer when it changes
watch(currentAnswer, async (newValue, oldValue) => {
  // Only save if there's a new value and it's different from old
  if (newValue !== undefined && newValue !== '' && newValue !== oldValue) {
    await submitAnswer();
  }
});

// Auto-submit when all questions are answered
let hasAutoSubmitted = ref(false);
watch(allQuestionsAnswered, (newValue) => {
  if (newValue && !hasAutoSubmitted.value && !isSubmitting.value) {
    hasAutoSubmitted.value = true;
    // Show dialog to confirm auto-submission after ensuring last answer is saved
    setTimeout(() => {
      showSubmitDialog.value = true;
    }, 1000); // Increased delay to ensure last answer is saved
  }
});
</script>

<template>
  <AppLayout>
    <Head :title="`Quiz: ${activity.title}`" />

    <div class="py-8 px-4 sm:px-6 lg:px-8">
      <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
            {{ activity.title }}
          </h1>
          <p class="text-gray-600 dark:text-gray-400">
            {{ quiz.description }}
          </p>
        </div>

        <!-- Progress Bar -->
        <Card class="mb-6">
          <CardContent class="pt-6">
            <div class="space-y-2">
              <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                <span>Progress: {{ answeredCount }} of {{ quiz.questions.length }} questions answered</span>
                <span>{{ Math.round(progressPercentage) }}%</span>
              </div>
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                <div 
                  class="bg-blue-600 h-3 rounded-full transition-all duration-300"
                  :style="{ width: `${progressPercentage}%` }"
                ></div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Question Card -->
        <Card class="mb-6">
        <CardHeader>
          <div class="flex items-center justify-between">
            <CardTitle class="text-lg">
              Question {{ currentQuestionIndex + 1 }} of {{ quiz.questions.length }}
            </CardTitle>
            <span class="text-sm px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">
              {{ getQuestionTypeLabel(currentQuestion.question_type) }}
            </span>
          </div>
          <CardDescription class="text-base mt-4 text-gray-900 dark:text-gray-100">
            {{ currentQuestion.question_text }}
          </CardDescription>
        </CardHeader>

        <CardContent>
          <!-- Multiple Choice -->
          <div v-if="currentQuestion.question_type === 'multiple-choice'" class="space-y-3">
            <div
              v-for="option in currentQuestion.options"
              :key="option.id"
              class="flex items-center space-x-3 p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer"
            >
              <input
                type="radio"
                :value="option.id"
                :id="`option-${option.id}`"
                v-model="currentAnswer"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
              />
              <label :for="`option-${option.id}`" class="flex-1 cursor-pointer text-gray-900 dark:text-gray-100">
                {{ option.option_text }}
              </label>
            </div>
          </div>

          <!-- True/False -->
          <div v-else-if="currentQuestion.question_type === 'true-false'" class="space-y-3">
            <div class="grid grid-cols-2 gap-4">
              <Button
                v-for="option in currentQuestion.options"
                :key="option.id"
                :variant="currentAnswer === option.id ? 'default' : 'outline'"
                @click="currentAnswer = option.id"
                class="h-16 text-lg"
              >
                {{ option.option_text }}
              </Button>
            </div>
          </div>

          <!-- Enumeration / Short Answer -->
          <div v-else-if="['enumeration', 'short-answer'].includes(currentQuestion.question_type)" class="space-y-3">
            <textarea
              v-model="currentAnswer"
              :placeholder="currentQuestion.question_type === 'enumeration' ? 'Enter your answers (one per line)' : 'Enter your answer'"
              :rows="currentQuestion.question_type === 'enumeration' ? 6 : 4"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <p class="text-sm text-gray-500 dark:text-gray-400">
              {{ currentQuestion.question_type === 'enumeration' ? 'Enter each item on a new line' : 'Provide a clear and concise answer' }}
            </p>
          </div>

        </CardContent>
      </Card>

      <!-- Navigation -->
      <div class="space-y-4 mb-6">
        <!-- Question Numbers Grid -->
        <Card>
          <CardContent class="pt-6">
            <div class="space-y-3">
              <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                <span class="font-medium">Question Navigation</span>
                <div class="flex items-center gap-3">
                  <div class="flex items-center gap-1">
                    <div class="w-4 h-4 rounded bg-green-100 dark:bg-green-900"></div>
                    <span class="text-xs">Answered</span>
                  </div>
                  <div class="flex items-center gap-1">
                    <div class="w-4 h-4 rounded bg-gray-100 dark:bg-gray-800"></div>
                    <span class="text-xs">Not Answered</span>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-2 flex-wrap">
                <button
                  v-for="(question, index) in quiz.questions"
                  :key="question.id"
                  @click="goToQuestion(index)"
                  :class="[
                    'w-12 h-12 rounded-lg text-sm font-medium transition-all',
                    currentQuestionIndex === index
                      ? 'bg-blue-600 text-white ring-2 ring-blue-600 ring-offset-2 dark:ring-offset-gray-900'
                      : answers[question.id]
                      ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 hover:bg-green-200 dark:hover:bg-green-800'
                      : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700'
                  ]"
                  :title="answers[question.id] ? 'Question answered' : 'Question not answered'"
                >
                  {{ index + 1 }}
                </button>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Navigation Buttons -->
        <div class="flex items-center justify-between gap-4">
          <Button
            @click="previousQuestion"
            :disabled="isFirstQuestion"
            variant="outline"
            class="flex-1 sm:flex-none"
          >
            ← Previous Question
          </Button>

          <Button
            v-if="!isLastQuestion"
            @click="nextQuestion"
            :disabled="!currentAnswer"
            class="flex-1 sm:flex-none"
          >
            <span v-if="!currentAnswer">Answer to Continue</span>
            <span v-else>Next Question →</span>
          </Button>

          <Button
            v-else
            @click="openSubmitDialog"
            :disabled="!allQuestionsAnswered"
            class="flex-1 sm:flex-none bg-green-600 hover:bg-green-700"
          >
            <span v-if="!allQuestionsAnswered">Complete All Questions</span>
            <span v-else>Submit Quiz →</span>
          </Button>
        </div>
      </div>

      <!-- Submit Dialog -->
      <Dialog v-model:open="showSubmitDialog">
        <DialogContent>
          <DialogHeader>
            <DialogTitle class="text-xl">🎉 Quiz Completed!</DialogTitle>
            <DialogDescription class="space-y-3 pt-4">
              <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <p class="font-medium text-green-900 dark:text-green-100">
                  ✓ All questions answered!
                </p>
                <ul class="mt-2 space-y-1 text-sm text-green-800 dark:text-green-200">
                  <li>{{ answeredCount }} of {{ quiz.questions.length }} questions completed</li>
                  <li>Total Points Available: {{ quiz.total_points }}</li>
                </ul>
              </div>

              <p class="text-sm text-gray-600 dark:text-gray-400 pt-2">
                ⓘ Click submit to see your results. Once submitted, you cannot change your answers.
              </p>
            </DialogDescription>
          </DialogHeader>
          <DialogFooter class="gap-2">
            <Button
              variant="outline"
              @click="cancelSubmit"
              :disabled="isSubmitting"
            >
              Review Answers
            </Button>
            <Button
              @click="confirmSubmit"
              :disabled="isSubmitting"
              class="bg-green-600 hover:bg-green-700"
            >
              {{ isSubmitting ? 'Submitting...' : '✓ Submit Quiz' }}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Ensure smooth transitions */
button {
  transition: all 0.2s ease-in-out;
}
</style>
