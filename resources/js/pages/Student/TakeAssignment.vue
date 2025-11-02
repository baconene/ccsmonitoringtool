<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { FileText, Clock, CheckCircle2, AlertCircle, Upload, Send, ArrowLeft, XCircle, Check, X } from 'lucide-vue-next';
import { useNotification } from '@/composables/useNotification';
import Notification from '@/components/Notification.vue';

interface QuestionOption {
  id: number;
  option_text: string;
  order: number;
  is_correct?: boolean;
}

interface StudentAnswer {
  id?: number;
  answer_text?: string;
  selected_options?: number[];
  answered_at?: string;
  is_correct?: boolean | null;
  points_earned?: number;
  instructor_feedback?: string;
}

interface AssignmentQuestion {
  id: number;
  question_text: string;
  question_type: 'true_false' | 'multiple_choice' | 'enumeration' | 'short_answer' | 'essay';
  points: number;
  order: number;
  correct_answer?: string;
  options?: QuestionOption[];
  student_answer?: StudentAnswer | null;
}

interface Assignment {
  id: number;
  title: string;
  description: string;
  assignment_type: 'objective' | 'file_upload' | 'mixed';
  total_points: number;
  time_limit?: number;
  instructions?: string;
  activity: {
    id: number;
    title: string;
    due_date?: string;
  };
}

interface Progress {
  id: number;
  submission_status: 'draft' | 'submitted' | 'graded';
  answered_questions: number;
  total_questions: number;
  points_possible: number;
  auto_graded_score?: number;
  manual_graded_score?: number;
  final_score?: number;
  submitted_at?: string;
  graded_at?: string;
}

interface StudentActivityData {
  id: number;
  status: 'not_started' | 'in_progress' | 'completed';
  score?: number;
  max_score?: number;
  percentage_score?: number;
}

interface Props {
  assignment: Assignment;
  questions: AssignmentQuestion[];
  progress: Progress;
  studentActivity: StudentActivityData;
  fileUploadAnswer?: any;
  canSubmit: boolean;
  isOverdue: boolean;
}

const props = defineProps<Props>();

// Notification
const { notification, showNotification } = useNotification();

// State
const currentQuestionIndex = ref(0);
const answers = ref<Record<number, any>>({});
const selectedFile = ref<File | null>(null);
const isSubmitting = ref(false);
const isSavingAnswer = ref(false);

// Check if assignment is submitted or graded
const isSubmitted = computed(() => props.progress.submission_status === 'submitted' || props.progress.submission_status === 'graded');
const isGraded = computed(() => props.progress.submission_status === 'graded');
const isCompleted = computed(() => props.studentActivity.status === 'completed');
const isReadOnly = computed(() => isSubmitted.value || isCompleted.value);

// Initialize answers from student_answer
props.questions.forEach((question) => {
  if (question.student_answer) {
    answers.value[question.id] = {
      answer_text: question.student_answer.answer_text || '',
      selected_options: question.student_answer.selected_options || [],
    };
  } else {
    answers.value[question.id] = {
      answer_text: '',
      selected_options: [],
    };
  }
});

// Computed
const currentQuestion = computed(() => props.questions[currentQuestionIndex.value]);
const isLastQuestion = computed(() => currentQuestionIndex.value === props.questions.length - 1);
const isFirstQuestion = computed(() => currentQuestionIndex.value === 0);
const progressPercentage = computed(() => {
  if (props.progress.total_questions === 0) return 0;
  return Math.round((props.progress.answered_questions / props.progress.total_questions) * 100);
});

// Methods
const goToQuestion = (index: number) => {
  currentQuestionIndex.value = index;
};

const nextQuestion = () => {
  if (!isLastQuestion.value) {
    currentQuestionIndex.value++;
  }
};

const previousQuestion = () => {
  if (!isFirstQuestion.value) {
    currentQuestionIndex.value--;
  }
};

const saveAnswer = async (questionId: number) => {
  if (isSavingAnswer.value || isReadOnly.value) return;
  
  isSavingAnswer.value = true;
  const answer = answers.value[questionId];

  try {
    const response = await fetch(`/student/assignments/${props.assignment.id}/answers`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify({
        question_id: questionId,
        answer_text: answer.answer_text,
        selected_options: answer.selected_options,
      }),
    });

    const data = await response.json();

    if (data.success) {
      showNotification('success', 'Answer saved successfully');
      // Update progress
      props.progress.answered_questions = data.answered_questions;
      props.progress.auto_graded_score = data.auto_graded_score;
      
      // Update the question's student_answer to mark as answered
      const question = props.questions.find(q => q.id === questionId);
      if (question) {
        question.student_answer = {
          answer_text: answer.answer_text,
          selected_options: answer.selected_options,
          answered_at: new Date().toISOString(),
          is_correct: data.is_correct,
          points_earned: data.points_earned,
        };
      }
    } else {
      showNotification('error', data.message || 'Failed to save answer');
    }
  } catch (error) {
    console.error('Failed to save answer:', error);
    showNotification('error', 'Failed to save answer');
  } finally {
    isSavingAnswer.value = false;
  }
};

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    selectedFile.value = target.files[0];
  }
};

const uploadFile = async () => {
  if (!selectedFile.value) {
    showNotification('error', 'Please select a file to upload');
    return;
  }

  const formData = new FormData();
  formData.append('file', selectedFile.value);

  try {
    const response = await fetch(`/student/assignments/${props.assignment.id}/upload`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      showNotification('success', 'File uploaded successfully');
      selectedFile.value = null;
    } else {
      showNotification('error', data.message || 'Failed to upload file');
    }
  } catch (error) {
    console.error('Failed to upload file:', error);
    showNotification('error', 'Failed to upload file');
  }
};

const submitAssignment = async () => {
  if (isSubmitting.value || isReadOnly.value) return;

  if (props.progress.answered_questions < props.progress.total_questions && props.assignment.assignment_type === 'objective') {
    const confirm = window.confirm(`You have only answered ${props.progress.answered_questions} out of ${props.progress.total_questions} questions. Are you sure you want to submit?`);
    if (!confirm) return;
  }

  isSubmitting.value = true;

  router.post(`/student/assignments/${props.assignment.id}/submit`, {}, {
    preserveState: false,
    onError: (errors) => {
      console.error('Submit errors:', errors);
      showNotification('error', 'Failed to submit assignment');
      isSubmitting.value = false;
    },
  });
};

const toggleOption = (questionId: number, optionId: number) => {
  if (isReadOnly.value) return;
  
  const question = props.questions.find(q => q.id === questionId);
  if (!question) return;

  if (question.question_type === 'multiple_choice') {
    // For multiple choice, only one option can be selected
    answers.value[questionId].selected_options = [optionId];
  } else {
    // For other types with options
    const index = answers.value[questionId].selected_options.indexOf(optionId);
    if (index > -1) {
      answers.value[questionId].selected_options.splice(index, 1);
    } else {
      answers.value[questionId].selected_options.push(optionId);
    }
  }
};

const isOptionSelected = (questionId: number, optionId: number) => {
  return answers.value[questionId]?.selected_options?.includes(optionId) || false;
};

const isOptionCorrect = (option: QuestionOption) => {
  return option.is_correct === true;
};

const getQuestionStatus = (question: AssignmentQuestion) => {
  if (!question.student_answer) return null;
  
  if (question.student_answer.is_correct === true) {
    return { icon: CheckCircle2, color: 'text-green-600', bg: 'bg-green-50 dark:bg-green-900/20', border: 'border-green-200 dark:border-green-700' };
  } else if (question.student_answer.is_correct === false) {
    return { icon: XCircle, color: 'text-red-600', bg: 'bg-red-50 dark:bg-red-900/20', border: 'border-red-200 dark:border-red-700' };
  } else if (question.student_answer.points_earned !== undefined && question.student_answer.points_earned !== null) {
    return { icon: CheckCircle2, color: 'text-blue-600', bg: 'bg-blue-50 dark:bg-blue-900/20', border: 'border-blue-200 dark:border-blue-700' };
  }
  
  return null;
};

const goBack = () => {
  window.history.back();
};

// Auto-save answer when it changes (like QuizTaking)
watch(() => answers.value[currentQuestion.value.id], async (newValue, oldValue) => {
  // Only save if there's a new value, it's different from old, not read-only, and not currently submitting
  if (newValue !== undefined && newValue !== oldValue && !isReadOnly.value && !isSubmitting.value) {
    // For text answers
    if (typeof newValue === 'object' && newValue.answer_text !== undefined && newValue.answer_text !== oldValue?.answer_text) {
      await saveAnswer(currentQuestion.value.id);
    }
    // For selected options
    else if (typeof newValue === 'object' && newValue.selected_options !== undefined) {
      const oldOptions = oldValue?.selected_options || [];
      const newOptions = newValue.selected_options || [];
      if (JSON.stringify(oldOptions.sort()) !== JSON.stringify(newOptions.sort())) {
        await saveAnswer(currentQuestion.value.id);
      }
    }
  }
}, { deep: true });
</script>

<template>
  <AppLayout>
    <Head :title="`${isGraded ? 'Results' : 'Take Assignment'}: ${assignment.title}`" />

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <button
          @click="goBack"
          class="flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-4 transition-colors"
        >
          <ArrowLeft class="w-4 h-4 mr-1" />
          Back to Course
        </button>

        <div class="flex items-start justify-between flex-wrap gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 mb-2 flex-wrap">
              <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ assignment.title }}</h1>
              <Badge v-if="isGraded" variant="default" class="bg-green-600 hover:bg-green-700">
                <CheckCircle2 class="w-3.5 h-3.5 mr-1" />
                Graded
              </Badge>
              <Badge v-else-if="isSubmitted" variant="secondary">
                <Send class="w-3.5 h-3.5 mr-1" />
                Submitted
              </Badge>
            </div>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">{{ assignment.description }}</p>
          </div>
          
          <!-- Score Display (if graded) -->
          <div v-if="isGraded && studentActivity.score !== undefined" class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl p-4 sm:p-6 shadow-lg min-w-[180px]">
            <div class="text-center">
              <div class="text-xs sm:text-sm font-medium opacity-90 mb-1">Your Score</div>
              <div class="text-3xl sm:text-4xl font-bold">{{ studentActivity.score }}/{{ assignment.total_points }}</div>
              <div class="text-xs sm:text-sm opacity-90 mt-1">{{ studentActivity.percentage_score?.toFixed(1) }}%</div>
            </div>
          </div>
        </div>
        
        <div class="mt-4 flex flex-wrap items-center gap-2 sm:gap-3 text-sm">
          <Badge variant="outline" class="flex items-center gap-1.5">
            <FileText class="w-3.5 h-3.5" />
            {{ questions.length }} {{ questions.length === 1 ? 'Question' : 'Questions' }}
          </Badge>
          <Badge variant="outline" class="flex items-center gap-1.5">
            <CheckCircle2 class="w-3.5 h-3.5" />
            {{ assignment.total_points }} Points
          </Badge>
          <Badge v-if="assignment.time_limit" variant="outline" class="flex items-center gap-1.5">
            <Clock class="w-3.5 h-3.5" />
            {{ assignment.time_limit }} {{ assignment.time_limit === 1 ? 'Minute' : 'Minutes' }}
          </Badge>
          <Badge v-if="isOverdue && !isSubmitted" variant="destructive" class="flex items-center gap-1.5">
            <AlertCircle class="w-3.5 h-3.5" />
            Overdue
          </Badge>
          <Badge v-if="isSubmitted && progress.submitted_at" variant="outline" class="flex items-center gap-1.5">
            <Send class="w-3.5 h-3.5" />
            Submitted {{ new Date(progress.submitted_at).toLocaleDateString() }}
          </Badge>
        </div>
      </div>

      <!-- Progress Bar (only if not graded) -->
      <div v-if="!isGraded" class="mb-6 bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ isSubmitted ? 'Submitted' : 'Progress' }}
          </span>
          <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ progress.answered_questions }}/{{ progress.total_questions }} answered ({{ progressPercentage }}%)
          </span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
          <div
            class="h-2.5 rounded-full transition-all duration-300"
            :class="isSubmitted ? 'bg-green-600' : 'bg-blue-600'"
            :style="{ width: `${progressPercentage}%` }"
          ></div>
        </div>
      </div>

      <!-- Main Question Area -->
      <div class="max-w-4xl mx-auto">
        <Card v-if="currentQuestion" :class="[
            'transition-all',
            isGraded && getQuestionStatus(currentQuestion) ? getQuestionStatus(currentQuestion)!.bg : '',
            isGraded && getQuestionStatus(currentQuestion) ? 'border-2 ' + getQuestionStatus(currentQuestion)!.border : ''
          ]">
            <CardHeader class="pb-4">
              <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex-1 min-w-[200px]">
                  <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <CardTitle class="text-lg sm:text-xl">
                      Question {{ currentQuestionIndex + 1 }}
                    </CardTitle>
                    <Badge variant="outline" class="text-xs">{{ currentQuestion.question_type.replace(/_/g, ' ').toUpperCase() }}</Badge>
                  </div>
                  <CardDescription>{{ currentQuestion.points }} {{ currentQuestion.points === 1 ? 'point' : 'points' }}</CardDescription>
                </div>
                
                <!-- Question Result Badge -->
                <div v-if="isGraded && currentQuestion.student_answer" class="flex flex-col items-end gap-1">
                  <Badge 
                    v-if="currentQuestion.student_answer.is_correct === true"
                    class="bg-green-600 text-white hover:bg-green-700"
                  >
                    <CheckCircle2 class="w-3.5 h-3.5 mr-1" />
                    Correct
                  </Badge>
                  <Badge 
                    v-else-if="currentQuestion.student_answer.is_correct === false"
                    class="bg-red-600 text-white hover:bg-red-700"
                  >
                    <XCircle class="w-3.5 h-3.5 mr-1" />
                    Incorrect
                  </Badge>
                  <Badge 
                    v-else-if="currentQuestion.student_answer.points_earned !== undefined"
                    variant="secondary"
                  >
                    {{ currentQuestion.student_answer.points_earned }}/{{ currentQuestion.points }} points
                  </Badge>
                  
                  <span v-if="currentQuestion.student_answer.points_earned !== undefined" class="text-sm font-semibold" :class="[
                    currentQuestion.student_answer.is_correct === true ? 'text-green-600 dark:text-green-400' :
                    currentQuestion.student_answer.is_correct === false ? 'text-red-600 dark:text-red-400' :
                    'text-blue-600 dark:text-blue-400'
                  ]">
                    {{ currentQuestion.student_answer.points_earned }}/{{ currentQuestion.points }} pts
                  </span>
                </div>
              </div>
            </CardHeader>
            <CardContent class="space-y-6">
              <!-- Question Text -->
              <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                <p class="text-base sm:text-lg font-medium text-gray-900 dark:text-white leading-relaxed" v-html="currentQuestion.question_text"></p>
              </div>

              <!-- Answer Input Based on Type -->
              <div class="space-y-4">
                <!-- True/False -->
                <div v-if="currentQuestion.question_type === 'true_false'" class="space-y-3">
                  <button
                    v-for="option in ['True', 'False']"
                    :key="option"
                    @click="!isReadOnly && (answers[currentQuestion.id].answer_text = option)"
                    :disabled="isReadOnly"
                    :class="[
                      'w-full p-4 text-left rounded-lg border-2 transition-all',
                      answers[currentQuestion.id].answer_text === option
                        ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20'
                        : 'border-gray-200 dark:border-gray-700',
                      !isReadOnly && 'hover:border-blue-300 cursor-pointer',
                      isReadOnly && 'cursor-not-allowed opacity-75'
                    ]"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <div :class="[
                          'w-5 h-5 rounded-full border-2 mr-3 flex items-center justify-center',
                          answers[currentQuestion.id].answer_text === option
                            ? 'border-blue-600 bg-blue-600'
                            : 'border-gray-300 dark:border-gray-600'
                        ]">
                          <div v-if="answers[currentQuestion.id].answer_text === option" class="w-2 h-2 bg-white rounded-full"></div>
                        </div>
                        <span class="font-medium">{{ option }}</span>
                      </div>
                      <!-- Show correct answer indicator if graded -->
                      <div v-if="isGraded && currentQuestion.correct_answer === option">
                        <CheckCircle2 class="w-5 h-5 text-green-600" />
                      </div>
                    </div>
                  </button>
                </div>

                <!-- Multiple Choice -->
                <div v-if="currentQuestion.question_type === 'multiple_choice'" class="space-y-3">
                  <button
                    v-for="option in currentQuestion.options"
                    :key="option.id"
                    @click="!isReadOnly && toggleOption(currentQuestion.id, option.id)"
                    :disabled="isReadOnly"
                    :class="[
                      'w-full p-4 text-left rounded-lg border-2 transition-all',
                      isOptionSelected(currentQuestion.id, option.id)
                        ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20'
                        : isGraded && isOptionCorrect(option)
                        ? 'border-green-200 dark:border-green-700 bg-green-50 dark:bg-green-900/10'
                        : 'border-gray-200 dark:border-gray-700',
                      !isReadOnly && 'hover:border-blue-300 cursor-pointer',
                      isReadOnly && 'cursor-not-allowed opacity-75'
                    ]"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center flex-1">
                        <div :class="[
                          'w-5 h-5 rounded-full border-2 mr-3 flex items-center justify-center flex-shrink-0',
                          isOptionSelected(currentQuestion.id, option.id)
                            ? 'border-blue-600 bg-blue-600'
                            : 'border-gray-300 dark:border-gray-600'
                        ]">
                          <div v-if="isOptionSelected(currentQuestion.id, option.id)" class="w-2 h-2 bg-white rounded-full"></div>
                        </div>
                        <span class="font-medium break-words">{{ option.option_text }}</span>
                      </div>
                      <!-- Show correct answer indicator if graded -->
                      <div v-if="isGraded && isOptionCorrect(option)" class="flex-shrink-0 ml-2">
                        <CheckCircle2 class="w-5 h-5 text-green-600" />
                      </div>
                    </div>
                  </button>
                </div>

                <!-- Short Answer / Enumeration / Essay -->
                <div v-if="['short_answer', 'enumeration', 'essay'].includes(currentQuestion.question_type)">
                  <textarea
                    v-model="answers[currentQuestion.id].answer_text"
                    :rows="currentQuestion.question_type === 'essay' ? 10 : 4"
                    :disabled="isReadOnly"
                    :class="[
                      'w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-white transition-all',
                      isReadOnly && 'cursor-not-allowed opacity-75 bg-gray-50 dark:bg-gray-900/50'
                    ]"
                    :placeholder="isReadOnly ? 'Your answer' : (currentQuestion.question_type === 'enumeration' ? 'Enter your answers, one per line' : 'Type your answer here...')"
                  ></textarea>
                </div>
              </div>

              <!-- Instructor Feedback (if graded and has feedback) -->
              <div v-if="isGraded && currentQuestion.student_answer?.instructor_feedback" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                <div class="flex items-start gap-2">
                  <AlertCircle class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" />
                  <div>
                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300 mb-1">Instructor Feedback</p>
                    <p class="text-sm text-yellow-700 dark:text-yellow-400">{{ currentQuestion.student_answer.instructor_feedback }}</p>
                  </div>
                </div>
              </div>

              <!-- Navigation and Save Buttons -->
              <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700 flex-wrap gap-3">
                <div class="flex gap-2">
                  <Button
                    @click="previousQuestion"
                    :disabled="isFirstQuestion"
                    variant="outline"
                    size="sm"
                  >
                    ← Previous
                  </Button>
                  <Button
                    @click="nextQuestion"
                    :disabled="isLastQuestion"
                    variant="outline"
                    size="sm"
                  >
                    Next →
                  </Button>
                </div>
                <Button
                  v-if="!isReadOnly"
                  @click="saveAnswer(currentQuestion.id)"
                  :disabled="isSavingAnswer"
                  size="sm"
                >
                  {{ isSavingAnswer ? 'Saving...' : 'Save Answer' }}
                </Button>
                <Badge v-else variant="secondary" class="text-xs">
                  Read Only
                </Badge>
              </div>
            </CardContent>
          </Card>

          <!-- File Upload Section (for file_upload or mixed assignments) -->
          <Card v-if="assignment.assignment_type === 'file_upload' || assignment.assignment_type === 'mixed'" class="mt-6">
            <CardHeader>
              <CardTitle class="flex items-center gap-2 text-lg">
                <Upload class="w-5 h-5" />
                File Submission
              </CardTitle>
              <CardDescription>Upload your assignment file (PDF, DOCX, etc.)</CardDescription>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
                <div v-if="fileUploadAnswer" class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg">
                  <div class="flex items-center gap-2">
                    <CheckCircle2 class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" />
                    <div>
                      <p class="text-sm font-medium text-green-700 dark:text-green-300">
                        File uploaded successfully
                      </p>
                      <p class="text-xs text-green-600 dark:text-green-400 mt-0.5">
                        {{ fileUploadAnswer.file_name }}
                      </p>
                    </div>
                  </div>
                </div>
                
                <div v-if="!isReadOnly">
                  <input
                    type="file"
                    @change="handleFileSelect"
                    class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300 cursor-pointer"
                  />
                </div>
                
                <Button v-if="!isReadOnly" @click="uploadFile" :disabled="!selectedFile">
                  <Upload class="w-4 h-4 mr-2" />
                  Upload File
                </Button>
              </div>
            </CardContent>
          </Card>

          <!-- Submit Assignment -->
          <div v-if="!isReadOnly" class="mt-6 flex justify-end">
            <Button
              @click="submitAssignment"
              :disabled="!canSubmit || isSubmitting"
              size="lg"
              class="bg-green-600 hover:bg-green-700"
            >
              <Send class="w-4 h-4 mr-2" />
              {{ isSubmitting ? 'Submitting...' : 'Submit Assignment' }}
            </Button>
          </div>
        </div>
      </div>

    <Notification :notification="notification" />
  </AppLayout>
</template>
