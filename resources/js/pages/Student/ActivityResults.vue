<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { 
  CheckCircle2, 
  XCircle, 
  Clock, 
  Trophy, 
  FileText, 
  ArrowLeft,
  Award,
  BookOpen,
  AlertCircle
} from 'lucide-vue-next';

// Props can vary based on activity type
interface Props {
  activityType: 'Quiz' | 'Assignment' | 'Assessment' | 'Exercise';
  
  // For Quiz
  progress?: any;
  
  // For Assignment
  assignment?: any;
  questionResults?: any[];
  fileUpload?: any;
  studentActivity?: any;
  summary?: any;
  
  // Common
  courseId?: number;
  activity?: any;
  message?: string;
}

const props = defineProps<Props>();

// Computed properties
const isQuiz = computed(() => props.activityType === 'Quiz');
const isAssignment = computed(() => props.activityType === 'Assignment');
const isAssessment = computed(() => props.activityType === 'Assessment');
const isExercise = computed(() => props.activityType === 'Exercise');

const isGraded = computed(() => {
  if (isQuiz.value) {
    return props.progress?.status === 'completed';
  }
  if (isAssignment.value) {
    return props.progress?.submission_status === 'graded';
  }
  return false;
});

const isPending = computed(() => {
  if (isAssignment.value) {
    return props.progress?.requires_grading && !isGraded.value;
  }
  return false;
});

// Quiz specific computed
const quizScore = computed(() => {
  if (!isQuiz.value || !props.progress) return null;
  const score = props.progress.score;
  return typeof score === 'number' ? score : parseFloat(String(score || 0));
});

const quizPercentage = computed(() => {
  if (!isQuiz.value || !props.progress) return null;
  const score = props.progress.percentage_score;
  return typeof score === 'number' ? score : parseFloat(String(score || 0));
});

const quizTotalQuestions = computed(() => props.progress?.total_questions || 0);

const quizCorrectAnswers = computed(() => 
  props.progress?.answers?.filter((a: any) => a.is_correct).length || 0
);

const quizIncorrectAnswers = computed(() => 
  props.progress?.answers?.filter((a: any) => !a.is_correct && a.is_correct !== null).length || 0
);

const quizPassed = computed(() => (quizPercentage.value ?? 0) >= 70);

// Assignment specific computed
const assignmentCorrectCount = computed(() => {
  if (!isAssignment.value || !props.questionResults) return 0;
  return props.questionResults.filter(r => r.is_correct === true).length;
});

const assignmentIncorrectCount = computed(() => {
  if (!isAssignment.value || !props.questionResults) return 0;
  return props.questionResults.filter(r => r.is_correct === false).length;
});

const assignmentPercentage = computed(() => {
  if (!isAssignment.value || !props.summary || !isGraded.value) return null;
  const raw = props.summary.percentage;
  const num = typeof raw === 'number' ? raw : parseFloat(String(raw ?? 0));
  return Number.isNaN(num) ? null : num;
});

// Common computed
const pageTitle = computed(() => {
  if (isQuiz.value) return `Quiz Results: ${props.progress?.activity?.title}`;
  if (isAssignment.value) return `Assignment Results: ${props.assignment?.title}`;
  if (isAssessment.value) return `Assessment Results: ${props.activity?.title}`;
  if (isExercise.value) return `Exercise Results: ${props.activity?.title}`;
  return 'Activity Results';
});

const getScoreColor = (percentage?: number) => {
  if (!percentage) return 'text-gray-600';
  if (percentage >= 90) return 'text-green-600 dark:text-green-400';
  if (percentage >= 75) return 'text-blue-600 dark:text-blue-400';
  if (percentage >= 60) return 'text-amber-600 dark:text-amber-400';
  return 'text-red-600 dark:text-red-400';
};

const getGradeColor = (letter?: string) => {
  if (!letter) return 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400';
  if (letter.startsWith('A')) return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300';
  if (letter.startsWith('B')) return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300';
  if (letter.startsWith('C')) return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300';
  if (letter.startsWith('D')) return 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300';
  return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
};

const goBack = () => {
  // Go back to the course detail page
  if (props.courseId) {
    window.location.href = `/student/courses/${props.courseId}`;
  } else {
    window.history.back();
  }
};

// Get correct answer text for quiz
const getCorrectAnswerText = (answer: any) => {
  const question = answer.question;
  
  if (question.question_type === 'multiple_choice' || question.question_type === 'true_false') {
    const correctOption = question.options?.find((opt: any) => opt.is_correct);
    return correctOption?.option_text || 'N/A';
  } else if (question.question_type === 'enumeration' || question.question_type === 'short_answer') {
    return question.correct_answer || 'Pending instructor review';
  }
  return 'N/A';
};

// Get student answer text for quiz
const getStudentAnswerText = (answer: any) => {
  const question = answer.question;
  
  if (question.question_type === 'multiple_choice' || question.question_type === 'true_false') {
    // For multiple choice/true-false, find the selected option
    const selectedOption = answer.selectedOption || answer.selected_option;
    if (selectedOption) {
      return selectedOption.option_text;
    }
    // Fallback: find the option by selected_option_id
    if (answer.selected_option_id && question.options) {
      const option = question.options.find((opt: any) => opt.id === answer.selected_option_id);
      if (option) return option.option_text;
    }
    return answer.answer_text || 'No answer';
  } else if (question.question_type === 'enumeration' || question.question_type === 'short_answer') {
    return answer.answer_text || 'No answer';
  }
  return 'N/A';
};

const formatTime = (seconds: number) => {
  if (!seconds) return 'N/A';
  const totalSeconds = Math.abs(Math.round(seconds));
  const minutes = Math.floor(totalSeconds / 60);
  const secs = totalSeconds % 60;
  return minutes > 0 ? `${minutes}m ${secs}s` : `${secs}s`;
};
</script>

<template>
  <AppLayout>
    <Head :title="pageTitle" />

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <button
          @click="goBack"
          class="flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-4 transition-colors"
        >
          <ArrowLeft class="w-4 h-4 mr-1" />
          Back to Course
        </button>

        <div class="flex items-center gap-3 mb-2">
          <Trophy class="w-8 h-8 text-yellow-500" />
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
            {{ activityType }} Results
          </h1>
        </div>
        <p v-if="isQuiz" class="text-lg text-gray-600 dark:text-gray-400">{{ progress?.activity?.title }}</p>
        <p v-if="isAssignment" class="text-lg text-gray-600 dark:text-gray-400">{{ assignment?.title }}</p>
        <p v-if="isAssessment || isExercise" class="text-lg text-gray-600 dark:text-gray-400">{{ activity?.title }}</p>
      </div>

      <!-- Pending Review Banner (Assignment only) -->
      <div v-if="isPending" class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded-r-lg">
        <div class="flex items-center gap-3">
          <Clock class="w-6 h-6 text-yellow-600 dark:text-yellow-400 flex-shrink-0" />
          <div>
            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Pending Review</h3>
            <p class="text-sm text-yellow-700 dark:text-yellow-400 mt-0.5">
              Your {{ activityType.toLowerCase() }} has been submitted and is awaiting instructor grading.
            </p>
          </div>
        </div>
      </div>

      <!-- Coming Soon Message (Assessment/Exercise) -->
      <div v-if="message" class="mb-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 rounded-r-lg">
        <div class="flex items-center gap-3">
          <AlertCircle class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0" />
          <div>
            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Information</h3>
            <p class="text-sm text-blue-700 dark:text-blue-400 mt-0.5">{{ message }}</p>
          </div>
        </div>
      </div>

      <!-- QUIZ RESULTS -->
      <template v-if="isQuiz && progress">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
          <!-- Score Card -->
          <Card class="lg:col-span-2">
            <CardHeader>
              <CardTitle class="flex items-center gap-2">
                <Award class="w-5 h-5" />
                Your Score
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl text-white shadow-lg">
                  <div class="text-sm font-medium opacity-90 mb-1">Total Score</div>
                  <div class="text-4xl font-bold">
                    {{ quizScore }}<span class="text-2xl">/{{ progress.max_score || progress.points_possible || 0 }}</span>
                  </div>
                </div>

                <div class="text-center p-4 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl text-white shadow-lg">
                  <div class="text-sm font-medium opacity-90 mb-1">Percentage</div>
                  <div class="text-4xl font-bold">
                    {{ quizPercentage?.toFixed(1) }}<span class="text-2xl">%</span>
                  </div>
                </div>

                <div class="text-center p-4 bg-gradient-to-br from-green-500 to-green-600 rounded-xl text-white shadow-lg">
                  <div class="text-sm font-medium opacity-90 mb-1">Status</div>
                  <div class="text-2xl font-bold">
                    {{ quizPassed ? 'PASSED' : 'FAILED' }}
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Statistics Card -->
          <Card>
            <CardHeader>
              <CardTitle class="text-base">Statistics</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Questions</span>
                <span class="font-semibold">{{ quizTotalQuestions }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-green-600 dark:text-green-400 flex items-center gap-1">
                  <CheckCircle2 class="w-4 h-4" />
                  Correct
                </span>
                <span class="font-semibold text-green-600">{{ quizCorrectAnswers }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                  <XCircle class="w-4 h-4" />
                  Incorrect
                </span>
                <span class="font-semibold text-red-600">{{ quizIncorrectAnswers }}</span>
              </div>
              <div class="flex items-center justify-between pt-2 border-t dark:border-gray-700">
                <span class="text-sm text-gray-600 dark:text-gray-400">Time Spent</span>
                <span class="font-semibold">{{ formatTime(progress.time_spent) }}</span>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Quiz Questions Review -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <FileText class="w-5 h-5" />
              Question-by-Question Review
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-6">
            <div
              v-for="(answer, index) in progress.answers"
              :key="answer.id"
              class="p-4 border dark:border-gray-700 rounded-lg"
            >
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-2">
                  <span class="font-semibold text-gray-900 dark:text-white">Question {{ Number(index) + 1 }}</span>
                  <Badge :variant="answer.question.question_type === 'multiple_choice' ? 'default' : 'secondary'">
                    {{ answer.question.question_type }}
                  </Badge>
                </div>
                <div class="flex items-center gap-2">
                  <CheckCircle2 v-if="answer.is_correct" class="w-5 h-5 text-green-600" />
                  <XCircle v-else-if="answer.is_correct === false" class="w-5 h-5 text-red-600" />
                  <Clock v-else class="w-5 h-5 text-yellow-600" />
                  <span class="text-sm font-medium">{{ answer.question.points }} points</span>
                </div>
              </div>

              <p class="text-gray-900 dark:text-white mb-4">{{ answer.question.question_text }}</p>

              <div class="space-y-2 bg-gray-50 dark:bg-gray-800/50 p-3 rounded">
                <div>
                  <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Your Answer:</span>
                  <span class="ml-2 font-semibold" :class="answer.is_correct ? 'text-green-600' : 'text-red-600'">
                    {{ getStudentAnswerText(answer) }}
                  </span>
                </div>
                <div v-if="!answer.is_correct && answer.is_correct !== null">
                  <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Correct Answer:</span>
                  <span class="ml-2 font-semibold text-green-600">{{ getCorrectAnswerText(answer) }}</span>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </template>

      <!-- ASSIGNMENT RESULTS -->
      <template v-if="isAssignment && assignment">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
          <!-- Score Card -->
          <Card class="lg:col-span-2">
            <CardHeader>
              <CardTitle class="flex items-center gap-2">
                <Award class="w-5 h-5" />
                Your Score
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl text-white shadow-lg">
                  <div class="text-sm font-medium opacity-90 mb-1">Points Earned</div>
                  <div class="text-4xl font-bold">
                    {{ isGraded ? summary?.points_earned : '—' }}<span class="text-2xl">/{{ summary?.total_points }}</span>
                  </div>
                </div>

                <div class="text-center p-4 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl text-white shadow-lg">
                  <div class="text-sm font-medium opacity-90 mb-1">Percentage</div>
                  <div class="text-4xl font-bold">
                    {{ isGraded && assignmentPercentage != null ? assignmentPercentage.toFixed(1) : '—' }}<span class="text-2xl">%</span>
                  </div>
                </div>

                <div class="text-center p-4 bg-gradient-to-br from-green-500 to-green-600 rounded-xl text-white shadow-lg">
                  <div class="text-sm font-medium opacity-90 mb-1">Grade</div>
                  <div class="text-4xl font-bold">
                    {{ isGraded ? summary?.grade_letter : '—' }}
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Statistics Card -->
          <Card>
            <CardHeader>
              <CardTitle class="text-base">Statistics</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Questions</span>
                <span class="font-semibold">{{ questionResults?.length || 0 }}</span>
              </div>
              <div v-if="isGraded" class="flex items-center justify-between">
                <span class="text-sm text-green-600 dark:text-green-400 flex items-center gap-1">
                  <CheckCircle2 class="w-4 h-4" />
                  Correct
                </span>
                <span class="font-semibold text-green-600">{{ assignmentCorrectCount }}</span>
              </div>
              <div v-if="isGraded" class="flex items-center justify-between">
                <span class="text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                  <XCircle class="w-4 h-4" />
                  Incorrect
                </span>
                <span class="font-semibold text-red-600">{{ assignmentIncorrectCount }}</span>
              </div>
              <div class="flex items-center justify-between pt-2 border-t dark:border-gray-700">
                <span class="text-sm text-gray-600 dark:text-gray-400">Submitted</span>
                <span class="text-sm font-medium">{{ new Date(summary?.submitted_at).toLocaleDateString() }}</span>
              </div>
              <div v-if="summary?.graded_at" class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Graded</span>
                <span class="text-sm font-medium">{{ new Date(summary.graded_at).toLocaleDateString() }}</span>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Instructor Feedback -->
        <Card v-if="summary?.instructor_feedback" class="mb-6">
          <CardHeader>
            <CardTitle class="text-base">Instructor Feedback</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-gray-700 dark:text-gray-300">{{ summary.instructor_feedback }}</p>
          </CardContent>
        </Card>

        <!-- Assignment Questions Review -->
        <Card v-if="questionResults && questionResults.length > 0">
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <FileText class="w-5 h-5" />
              Question-by-Question Review
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-6">
            <div
              v-for="(result, index) in questionResults"
              :key="result.question.id"
              class="p-4 border dark:border-gray-700 rounded-lg"
            >
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-2">
                  <span class="font-semibold text-gray-900 dark:text-white">Question {{ Number(index) + 1 }}</span>
                  <Badge :variant="result.question.question_type === 'multiple_choice' ? 'default' : 'secondary'">
                    {{ result.question.question_type }}
                  </Badge>
                  <Badge v-if="isPending" variant="outline">Pending Review</Badge>
                </div>
                <div class="flex items-center gap-2">
                  <CheckCircle2 v-if="result.is_correct === true" class="w-5 h-5 text-green-600" />
                  <XCircle v-else-if="result.is_correct === false" class="w-5 h-5 text-red-600" />
                  <Clock v-else class="w-5 h-5 text-yellow-600" />
                  <span class="text-sm font-medium">{{ result.points_possible }} points</span>
                </div>
              </div>

              <p class="text-gray-900 dark:text-white mb-4">{{ result.question.question_text }}</p>

              <div v-if="result.student_answer" class="space-y-2 bg-gray-50 dark:bg-gray-800/50 p-3 rounded">
                <div v-if="result.student_answer.answer_text">
                  <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Your Answer:</span>
                  <p class="mt-1 text-gray-900 dark:text-white">{{ result.student_answer.answer_text }}</p>
                </div>
                <div v-if="result.question.correct_answer && isGraded">
                  <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Correct Answer:</span>
                  <p class="mt-1 text-green-600">{{ result.question.correct_answer }}</p>
                </div>
                <div v-if="result.student_answer.instructor_feedback">
                  <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Feedback:</span>
                  <p class="mt-1 text-gray-900 dark:text-white">{{ result.student_answer.instructor_feedback }}</p>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </template>
    </div>
  </AppLayout>
</template>
