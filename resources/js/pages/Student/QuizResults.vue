<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Activity, Quiz, Question, QuestionOption, StudentQuizProgress, StudentQuizAnswer } from '@/types';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { CheckCircle2, XCircle, Clock, BookOpen, Trophy } from 'lucide-vue-next';

interface Props {
  progress: StudentQuizProgress & {
    quiz: Quiz & {
      total_points: number;
    };
    activity: Activity;
    answers: (StudentQuizAnswer & {
      question: Question & {
        options?: QuestionOption[];
      };
      selectedOption?: QuestionOption;
    })[];
  };
  courseId?: number;
}

const props = defineProps<Props>();
const page = usePage();

// Debug logging
console.log('Quiz Results - Progress data:', props.progress);
console.log('Quiz Results - Score:', props.progress.score, '(type:', typeof props.progress.score, ')');
console.log('Quiz Results - Percentage:', props.progress.percentage_score, '(type:', typeof props.progress.percentage_score, ')');
console.log('Quiz Results - Total Points:', props.progress.quiz.total_points, '(type:', typeof props.progress.quiz.total_points, ')');
console.log('Quiz Results - Answers count:', props.progress.answers?.length);

// Computed properties
const scorePercentage = computed(() => {
  const score = props.progress.percentage_score;
  // Handle both number and string (just in case)
  return typeof score === 'number' ? score : parseFloat(String(score || 0));
});

const totalScore = computed(() => {
  const score = props.progress.score;
  // Handle both number and string (just in case)
  return typeof score === 'number' ? score : parseFloat(String(score || 0));
});

const totalQuestions = computed(() => props.progress.total_questions || 0);
const correctAnswers = computed(() => 
  props.progress.answers?.filter(a => a.is_correct).length || 0
);
const incorrectAnswers = computed(() => 
  props.progress.answers?.filter(a => !a.is_correct && a.is_correct !== null).length || 0
);
const pendingReview = computed(() => 
  props.progress.answers?.filter(a => a.is_correct === null).length || 0
);
const passed = computed(() => scorePercentage.value >= 70);

const timeSpent = computed(() => {
  if (!props.progress.time_spent) return 'N/A';
  
  // Convert to positive number and round to avoid floating point issues
  const totalSeconds = Math.abs(Math.round(props.progress.time_spent));
  
  const minutes = Math.floor(totalSeconds / 60);
  const seconds = totalSeconds % 60;
  
  if (minutes > 0) {
    return `${minutes}m ${seconds}s`;
  }
  return `${seconds}s`;
});

// Get score color class
const getScoreColorClass = computed(() => {
  if (scorePercentage.value >= 90) return 'text-green-600 dark:text-green-400';
  if (scorePercentage.value >= 75) return 'text-blue-600 dark:text-blue-400';
  if (scorePercentage.value >= 60) return 'text-amber-600 dark:text-amber-400';
  return 'text-red-600 dark:text-red-400';
});

// Get correct answer text for display
const getCorrectAnswerText = (answer: StudentQuizAnswer & { question: Question & { options?: QuestionOption[] } }) => {
  const question = answer.question;
  
  if (question.question_type === 'multiple-choice' || question.question_type === 'true-false') {
    const correctOption = question.options?.find(opt => opt.is_correct);
    return correctOption?.option_text || 'N/A';
  } else if (question.question_type === 'enumeration' || question.question_type === 'short-answer') {
    return question.correct_answer || 'Pending instructor review';
  } else {
    return 'N/A';
  }
};

// Get student answer text for display
const getStudentAnswerText = (answer: StudentQuizAnswer & { question: Question; selectedOption?: QuestionOption }) => {
  if (answer.selectedOption) {
    return answer.selectedOption.option_text;
  }
  return answer.answer_text || 'Not answered';
};
</script>

<template>
  <AppLayout>
    <Head :title="`Quiz Results: ${progress.activity.title}`" />

    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
      <!-- Animated Success Banner at Top -->
      <div v-if="(page.props.flash as any)?.success" 
           class="fixed top-0 left-0 right-0 z-50 animate-slide-down">
        <div class="max-w-4xl mx-auto mt-4 px-4">
          <div class="p-4 rounded-lg shadow-lg backdrop-blur-sm" 
               :class="passed ? 'bg-green-50/95 dark:bg-green-900/95 border-2 border-green-500' : 'bg-blue-50/95 dark:bg-blue-900/95 border-2 border-blue-500'">
            <div class="flex items-center justify-center">
              <CheckCircle2 v-if="passed" class="w-8 h-8 text-green-600 dark:text-green-400 mr-3 animate-bounce" />
              <BookOpen v-else class="w-8 h-8 text-blue-600 dark:text-blue-400 mr-3 animate-pulse" />
              <p :class="passed ? 'text-green-800 dark:text-green-200' : 'text-blue-800 dark:text-blue-200'" class="font-bold text-lg">
                {{ (page.props.flash as any)?.success }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Centered Content Container -->
      <div class="max-w-4xl mx-auto" :class="(page.props.flash as any)?.success ? 'mt-24' : ''">

      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
          Quiz Results
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
          {{ progress.activity.title }}
        </p>
      </div>

      <!-- Score Summary -->
      <Card class="mb-8 border-2 animate-scale-in" :class="passed ? 'border-green-500' : 'border-red-500'">
        <CardContent class="pt-6">
          <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full mb-4 animate-bounce" 
                 :class="passed ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900'">
              <Trophy 
                :class="[
                  'w-12 h-12',
                  passed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
                ]" 
              />
            </div>
            
            <h2 class="text-6xl font-bold mb-2 animate-scale-in" :class="getScoreColorClass">
              {{ scorePercentage.toFixed(1) }}%
            </h2>
            
            <p class="text-xl text-gray-600 dark:text-gray-400 mb-4">
              {{ totalScore.toFixed(2) }} / {{ progress.quiz.total_points || 0 }} points
            </p>

            <Badge 
              :variant="passed ? 'default' : 'destructive'"
              class="text-lg px-4 py-2"
            >
              {{ passed ? '✓ Passed' : '✗ Not Passed' }}
            </Badge>
          </div>

          <!-- Stats Grid -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
              <div class="flex items-center justify-center mb-2">
                <BookOpen class="w-5 h-5 text-gray-600 dark:text-gray-400" />
              </div>
              <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                {{ totalQuestions }}
              </div>
              <div class="text-sm text-gray-600 dark:text-gray-400">
                Total Questions
              </div>
            </div>

            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
              <div class="flex items-center justify-center mb-2">
                <CheckCircle2 class="w-5 h-5 text-green-600 dark:text-green-400" />
              </div>
              <div class="text-2xl font-semibold text-green-600 dark:text-green-400">
                {{ correctAnswers }}
              </div>
              <div class="text-sm text-gray-600 dark:text-gray-400">
                Correct
              </div>
            </div>

            <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
              <div class="flex items-center justify-center mb-2">
                <XCircle class="w-5 h-5 text-red-600 dark:text-red-400" />
              </div>
              <div class="text-2xl font-semibold text-red-600 dark:text-red-400">
                {{ incorrectAnswers }}
              </div>
              <div class="text-sm text-gray-600 dark:text-gray-400">
                Incorrect
              </div>
            </div>

            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
              <div class="flex items-center justify-center mb-2">
                <Clock class="w-5 h-5 text-blue-600 dark:text-blue-400" />
              </div>
              <div class="text-2xl font-semibold text-blue-600 dark:text-blue-400">
                {{ timeSpent }}
              </div>
              <div class="text-sm text-gray-600 dark:text-gray-400">
                Time Spent
              </div>
            </div>
          </div>

          <div v-if="pendingReview > 0" class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg">
            <p class="text-amber-800 dark:text-amber-200 text-sm text-center">
              ⏳ {{ pendingReview }} answer(s) pending instructor review
            </p>
          </div>
        </CardContent>
      </Card>

      <!-- Answer Review -->
      <div class="space-y-4">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
          Answer Review
        </h2>

        <Card 
          v-for="(answer, index) in progress.answers" 
          :key="answer.id"
          class="border-l-4"
          :class="[
            answer.is_correct === true 
              ? 'border-l-green-500' 
              : answer.is_correct === false 
              ? 'border-l-red-500' 
              : 'border-l-amber-500'
          ]"
        >
          <CardHeader>
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <Badge variant="outline" class="text-xs">
                    Question {{ index + 1 }}
                  </Badge>
                  <Badge 
                    :variant="
                      answer.is_correct === true 
                        ? 'default' 
                        : answer.is_correct === false 
                        ? 'destructive' 
                        : 'secondary'
                    "
                    class="text-xs"
                  >
                    <CheckCircle2 v-if="answer.is_correct === true" class="w-3 h-3 mr-1" />
                    <XCircle v-else-if="answer.is_correct === false" class="w-3 h-3 mr-1" />
                    <Clock v-else class="w-3 h-3 mr-1" />
                    {{ 
                      answer.is_correct === true 
                        ? 'Correct' 
                        : answer.is_correct === false 
                        ? 'Incorrect' 
                        : 'Pending Review'
                    }}
                  </Badge>
                </div>
                <CardTitle class="text-base">
                  {{ answer.question.question_text }}
                </CardTitle>
              </div>
              <div class="text-right ml-4">
                <div class="text-sm font-semibold" :class="answer.is_correct ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400'">
                  {{ answer.points_earned }} / {{ answer.question.points }} pts
                </div>
              </div>
            </div>
          </CardHeader>

          <CardContent class="space-y-3">
            <!-- Student's Answer -->
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
                Your Answer:
              </p>
              <div 
                class="p-3 rounded-lg"
                :class="[
                  answer.is_correct === true 
                    ? 'bg-green-50 dark:bg-green-900/20 text-green-900 dark:text-green-100' 
                    : answer.is_correct === false 
                    ? 'bg-red-50 dark:bg-red-900/20 text-red-900 dark:text-red-100' 
                    : 'bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100'
                ]"
              >
                {{ getStudentAnswerText(answer) }}
              </div>
            </div>

            <!-- Correct Answer (for incorrect or pending answers) -->
            <div v-if="answer.is_correct !== true">
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
                Correct Answer:
              </p>
              <div class="p-3 bg-green-50 dark:bg-green-900/20 text-green-900 dark:text-green-100 rounded-lg">
                {{ getCorrectAnswerText(answer) }}
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Action Buttons -->
      <div class="mt-8 flex items-center justify-center gap-4">
        <Link :href="courseId ? `/student/courses/${courseId}` : '/student/courses'">
          <Button variant="outline" size="lg">
            {{ courseId ? '← Back to Course' : '← Back to My Courses' }}
          </Button>
        </Link>
      </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Smooth transitions */
.transition-all {
  transition: all 0.3s ease-in-out;
}

/* Slide down animation for success banner */
@keyframes slide-down {
  0% {
    transform: translateY(-100%);
    opacity: 0;
  }
  100% {
    transform: translateY(0);
    opacity: 1;
  }
}

.animate-slide-down {
  animation: slide-down 0.5s ease-out forwards;
}

/* Score reveal animation */
@keyframes scale-in {
  0% {
    transform: scale(0.8);
    opacity: 0;
  }
  50% {
    transform: scale(1.05);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.animate-scale-in {
  animation: scale-in 0.6s ease-out forwards;
}
</style>
