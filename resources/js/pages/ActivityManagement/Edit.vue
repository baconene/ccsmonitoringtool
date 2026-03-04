<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Activity, ActivityType } from '@/types';
import { ArrowLeft, Save, Trash2, Plus } from 'lucide-vue-next';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import CosmicBackground from '@/components/CosmicBackground.vue';

interface Question {
  id?: number;
  question_text: string;
  question_type: 'true_false' | 'multiple_choice' | 'enumeration' | 'short_answer';
  points: number;
  correct_answer?: string;
  acceptable_answers?: string[];
  explanation?: string;
  case_sensitive?: boolean;
  options?: Array<{ id?: number; option_text: string; is_correct: boolean }>;
}

interface Props {
  activity: Activity & {
    activity_type?: any;
    creator?: any;
    assignment?: {
      id: number;
      title: string;
      description?: string;
      instructions?: string;
      total_points: number;
      time_limit?: number;
      allow_late_submission: boolean;
      questions?: Question[];
    };
  };
  activityTypes: ActivityType[];
}

const props = defineProps<Props>();

const isAssignment = computed(() => {
  return props.activity.assignment !== undefined && props.activity.assignment !== null;
});

// Initialize form with basic fields first
const form = useForm({
  title: props.activity.title,
  description: props.activity.description || '',
  activity_type_id: props.activity.activity_type_id,
  due_date: props.activity.due_date ? new Date(props.activity.due_date).toISOString().split('T')[0] : '',
  instructions: '',
  total_points: 0,
  time_limit: null as number | null,
  allow_late_submission: false,
  questions: [] as Question[],
  deleted_question_ids: [] as number[],
});

const showAddQuestion = ref(false);
const errors = ref<{ [key: string]: string }>({});

// Watch for assignment data and populate form when available
watch(
  () => props.activity.assignment,
  (assignment) => {
    if (assignment) {
      console.log('Loading assignment data:', assignment);
      form.instructions = assignment.instructions || '';
      form.total_points = assignment.total_points || 0;
      form.time_limit = assignment.time_limit || null;
      form.allow_late_submission = assignment.allow_late_submission || false;
      // Deep copy questions to avoid mutation issues
      form.questions = JSON.parse(JSON.stringify(assignment.questions || []));
      console.log('Questions loaded:', form.questions.length);
    }
  },
  { immediate: true }
);

const handleBack = () => {
  router.visit('/activity-management');
};

const addQuestion = () => {
  if (isAssignment.value && form.questions) {
    form.questions.push({
      question_text: '',
      question_type: 'multiple_choice',
      points: 1,
      options: [
        { option_text: '', is_correct: true },
        { option_text: '', is_correct: false }
      ]
    });
  }
};

const removeQuestion = (index: number) => {
  if (isAssignment.value && form.questions) {
    const question = form.questions[index];
    if (question.id) {
      (form as any).deleted_question_ids = [...((form as any).deleted_question_ids || []), question.id];
    }
    form.questions.splice(index, 1);
  }
};

const addOption = (questionIndex: number) => {
  if (isAssignment.value && form.questions && form.questions[questionIndex]) {
    if (!form.questions[questionIndex].options) {
      form.questions[questionIndex].options = [];
    }
    form.questions[questionIndex].options!.push({
      option_text: '',
      is_correct: false
    });
  }
};

const removeOption = (questionIndex: number, optionIndex: number) => {
  if (isAssignment.value && form.questions && form.questions[questionIndex]?.options) {
    form.questions[questionIndex].options!.splice(optionIndex, 1);
  }
};

const handleSubmit = () => {
  // Validate questions if assignment
  if (isAssignment.value && (form as any).questions) {
    const newErrors: { [key: string]: string } = {};
    
    (form as any).questions.forEach((q: Question, i: number) => {
      if (!q.question_text.trim()) {
        newErrors[`question_${i}_text`] = 'Question text is required';
      }
      if (q.points < 1) {
        newErrors[`question_${i}_points`] = 'Points must be at least 1';
      }
      if (q.question_type === 'multiple_choice' && q.options) {
        if (q.options.length < 2) {
          newErrors[`question_${i}_options`] = 'Multiple choice must have at least 2 options';
        }
        const hasCorrect = q.options.some(o => o.is_correct);
        if (!hasCorrect) {
          newErrors[`question_${i}_correct`] = 'Must mark at least one correct option';
        }
      }
    });

    if (Object.keys(newErrors).length > 0) {
      errors.value = newErrors;
      return;
    }
  }
  
  errors.value = {};

  // Prepare the complete data structure for submission
  // This ensures nested arrays/objects are properly serialized
  const submitData: any = {
    title: form.title,
    description: form.description,
    activity_type_id: form.activity_type_id,
    due_date: form.due_date,
  };

  // Add assignment-specific data if applicable
  if (isAssignment.value && (form as any).questions) {
    submitData.instructions = (form as any).instructions || '';
    submitData.total_points = (form as any).total_points || 0;
    submitData.time_limit = (form as any).time_limit || null;
    submitData.allow_late_submission = (form as any).allow_late_submission || false;
    submitData.questions = (form as any).questions.map((q: Question) => {
      const questionObj: any = {
        question_text: q.question_text,
        question_type: q.question_type,
        points: q.points,
        explanation: q.explanation || '',
      };

      // Only include id if it exists (for updates)
      if (q.id) {
        questionObj.id = q.id;
      }

      // Include type-specific fields
      if (q.question_type === 'multiple_choice' && q.options) {
        questionObj.options = q.options.map(opt => ({
          ...(opt.id && { id: opt.id }),
          option_text: opt.option_text,
          is_correct: opt.is_correct === true || opt.is_correct === 'true',
        }));
      } else {
        questionObj.correct_answer = q.correct_answer || '';
        if (q.acceptable_answers) {
          questionObj.acceptable_answers = q.acceptable_answers;
        }
        if (q.case_sensitive !== undefined) {
          questionObj.case_sensitive = q.case_sensitive;
        }
      }

      return questionObj;
    });
    submitData.deleted_question_ids = (form as any).deleted_question_ids || [];
  }

  // Use Laravel's router.put() with explicit data
  console.log('Submitting data:', submitData);
  
  router.put(`/activities/${props.activity.id}`, submitData, {
    onSuccess: () => {
      router.visit(`/activities/${props.activity.id}`);
    },
    onError: (err) => {
      console.error('Save error:', err);
    }
  });
};
</script>

<template>
  <AppLayout>
    <div class="relative min-h-screen bg-gradient-to-br from-gray-50 via-purple-50/30 to-pink-50/30 dark:from-gray-900 dark:via-purple-950/20 dark:to-pink-950/20 transition-colors duration-300">
      <CosmicBackground />
      
      <div class="relative z-10 py-4 sm:py-6 lg:py-8 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
          <button
            @click="handleBack"
            class="mb-3 sm:mb-4 flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-colors group text-sm sm:text-base"
          >
            <ArrowLeft :size="20" class="group-hover:-translate-x-1 transition-transform" />
            <span>Back to Activities</span>
          </button>

          <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">
            {{ isAssignment ? 'Edit Assignment' : 'Edit Activity' }}
          </h1>
          <p class="mt-1 sm:mt-2 text-xs sm:text-sm lg:text-base text-gray-600 dark:text-gray-400">
            {{ isAssignment ? 'Update assignment details and questions' : 'Update the details of your activity' }}
          </p>
        </div>

        <!-- Edit Form -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-purple-200/50 dark:border-purple-700/50 p-4 sm:p-6 lg:p-8">
          <form @submit.prevent="handleSubmit" class="space-y-6 sm:space-y-8">
            <!-- Basic Information Section -->
            <div class="space-y-4 sm:space-y-6">
              <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                Basic Information
              </h2>

              <!-- Title -->
              <div>
                <Label for="title" class="text-xs sm:text-sm font-medium">
                  {{ isAssignment ? 'Assignment Title' : 'Activity Title' }} <span class="text-red-500">*</span>
                </Label>
                <Input
                  id="title"
                  v-model="form.title"
                  type="text"
                  placeholder="Enter title"
                  required
                  class="mt-1 sm:mt-2 text-sm sm:text-base"
                  :class="{ 'border-red-500': form.errors.title }"
                />
                <p v-if="form.errors.title" class="mt-1 text-xs text-red-500">
                  {{ form.errors.title }}
                </p>
              </div>

              <!-- Description -->
              <div>
                <Label for="description" class="text-xs sm:text-sm font-medium">
                  Description
                </Label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="3"
                  placeholder="Enter description"
                  class="mt-1 sm:mt-2 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                  :class="{ 'border-red-500': form.errors.description }"
                ></textarea>
                <p v-if="form.errors.description" class="mt-1 text-xs text-red-500">
                  {{ form.errors.description }}
                </p>
              </div>

              <!-- Activity Type -->
              <div>
                <Label for="activity_type" class="text-xs sm:text-sm font-medium">
                  Activity Type <span class="text-red-500">*</span>
                </Label>
                <select
                  id="activity_type"
                  v-model="form.activity_type_id"
                  required
                  class="mt-1 sm:mt-2 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                  :class="{ 'border-red-500': form.errors.activity_type_id }"
                >
                  <option value="" disabled>Select activity type</option>
                  <option
                    v-for="type in activityTypes"
                    :key="type.id"
                    :value="type.id"
                  >
                    {{ type.name }}
                  </option>
                </select>
                <p v-if="form.errors.activity_type_id" class="mt-1 text-xs text-red-500">
                  {{ form.errors.activity_type_id }}
                </p>
              </div>

              <!-- Due Date -->
              <div>
                <Label for="due_date" class="text-xs sm:text-sm font-medium">
                  Due Date
                </Label>
                <Input
                  id="due_date"
                  v-model="form.due_date"
                  type="date"
                  class="mt-1 sm:mt-2 text-sm sm:text-base"
                  :class="{ 'border-red-500': form.errors.due_date }"
                />
                <p v-if="form.errors.due_date" class="mt-1 text-xs text-red-500">
                  {{ form.errors.due_date }}
                </p>
              </div>
            </div>

            <!-- Assignment-Specific Section -->
            <template v-if="isAssignment">
              <div class="border-t border-gray-200 dark:border-gray-700 pt-6 sm:pt-8">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-4 sm:mb-6">
                  Assignment Settings
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                  <!-- Instructions -->
                  <div class="sm:col-span-2">
                    <Label for="instructions" class="text-xs sm:text-sm font-medium">
                      Instructions
                    </Label>
                    <textarea
                      id="instructions"
                      v-model="(form as any).instructions"
                      rows="3"
                      placeholder="Enter assignment instructions"
                      class="mt-1 sm:mt-2 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                    ></textarea>
                  </div>

                  <!-- Total Points -->
                  <div>
                    <Label for="total_points" class="text-xs sm:text-sm font-medium">
                      Total Points <span class="text-red-500">*</span>
                    </Label>
                    <Input
                      id="total_points"
                      v-model.number="(form as any).total_points"
                      type="number"
                      min="1"
                      required
                      class="mt-1 sm:mt-2 text-sm sm:text-base"
                    />
                  </div>

                  <!-- Time Limit -->
                  <div>
                    <Label for="time_limit" class="text-xs sm:text-sm font-medium">
                      Time Limit (minutes)
                    </Label>
                    <Input
                      id="time_limit"
                      v-model.number="(form as any).time_limit"
                      type="number"
                      min="1"
                      class="mt-1 sm:mt-2 text-sm sm:text-base"
                    />
                  </div>

                  <!-- Allow Late Submission -->
                  <div class="sm:col-span-2 flex items-center gap-3">
                    <input
                      id="allow_late"
                      v-model="(form as any).allow_late_submission"
                      type="checkbox"
                      class="w-4 h-4 border-gray-300 rounded"
                    />
                    <Label for="allow_late" class="text-xs sm:text-sm font-medium cursor-pointer">
                      Allow Late Submission
                    </Label>
                  </div>
                </div>
              </div>

              <!-- Questions Section -->
              <div class="border-t border-gray-200 dark:border-gray-700 pt-6 sm:pt-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0 mb-4 sm:mb-6">
                  <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                    Questions ({{ (form as any).questions?.length || 0 }})
                  </h2>
                  <Button
                    type="button"
                    @click="addQuestion"
                    class="w-full sm:w-auto flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm"
                  >
                    <Plus :size="16" />
                    Add Question
                  </Button>
                </div>

                <!-- Questions List -->
                <div class="space-y-4 sm:space-y-6">
                  <div
                    v-for="(question, qIndex) in (form as any).questions"
                    :key="`question-${qIndex}`"
                    class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 sm:p-6 border border-gray-200 dark:border-gray-600 space-y-4"
                  >
                    <!-- Question Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0">
                      <h3 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">
                        Question {{ qIndex + 1 }}
                      </h3>
                      <button
                        type="button"
                        @click="removeQuestion(qIndex)"
                        class="w-full sm:w-auto px-3 py-2 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 rounded-lg flex items-center justify-center sm:justify-start gap-2 text-xs sm:text-sm transition-colors"
                      >
                        <Trash2 :size="16" />
                        Delete
                      </button>
                    </div>

                    <!-- Question Text -->
                    <div>
                      <Label :for="`question_text_${qIndex}`" class="text-xs sm:text-sm font-medium">
                        Question Text <span class="text-red-500">*</span>
                      </Label>
                      <textarea
                        :id="`question_text_${qIndex}`"
                        v-model="question.question_text"
                        rows="2"
                        placeholder="Enter question"
                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 text-sm"
                        :class="{ 'border-red-500': errors[`question_${qIndex}_text`] }"
                      ></textarea>
                      <p v-if="errors[`question_${qIndex}_text`]" class="mt-1 text-xs text-red-500">
                        {{ errors[`question_${qIndex}_text`] }}
                      </p>
                    </div>

                    <!-- Question Type and Points -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <div>
                        <Label :for="`question_type_${qIndex}`" class="text-xs sm:text-sm font-medium">
                          Question Type <span class="text-red-500">*</span>
                        </Label>
                        <select
                          :id="`question_type_${qIndex}`"
                          v-model="question.question_type"
                          class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 text-sm"
                        >
                          <option value="true_false">True/False</option>
                          <option value="multiple_choice">Multiple Choice</option>
                          <option value="short_answer">Short Answer</option>
                          <option value="enumeration">Enumeration</option>
                        </select>
                      </div>

                      <div>
                        <Label :for="`question_points_${qIndex}`" class="text-xs sm:text-sm font-medium">
                          Points <span class="text-red-500">*</span>
                        </Label>
                        <Input
                          :id="`question_points_${qIndex}`"
                          v-model.number="question.points"
                          type="number"
                          min="1"
                          class="mt-1 text-sm"
                        />
                      </div>
                    </div>

                    <!-- Correct Answer / Options -->
                    <template v-if="question.question_type === 'multiple_choice'">
                      <div>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0 mb-3">
                          <Label class="text-xs sm:text-sm font-medium">
                            Options
                          </Label>
                          <button
                            type="button"
                            @click="addOption(qIndex)"
                            class="w-full sm:w-auto px-2 py-1 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-400 rounded text-xs transition-colors"
                          >
                            + Add Option
                          </button>
                        </div>
                        <div class="space-y-2">
                          <div
                            v-for="(option, oIndex) in question.options || []"
                            :key="`option-${qIndex}-${oIndex}`"
                            class="flex flex-col sm:flex-row sm:items-center gap-2"
                          >
                            <input
                              :id="`option_correct_${qIndex}_${oIndex}`"
                              v-model="option.is_correct"
                              type="checkbox"
                              class="w-4 h-4 rounded"
                            />
                            <input
                              v-model="option.option_text"
                              type="text"
                              placeholder="Option text"
                              class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 text-sm"
                            />
                            <button
                              type="button"
                              @click="removeOption(qIndex, oIndex)"
                              class="w-full sm:w-auto px-2 py-2 sm:py-1 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded text-xs"
                            >
                              Remove
                            </button>
                          </div>
                        </div>
                        <p v-if="errors[`question_${qIndex}_options`]" class="mt-2 text-xs text-red-500">
                          {{ errors[`question_${qIndex}_options`] }}
                        </p>
                        <p v-if="errors[`question_${qIndex}_correct`]" class="mt-2 text-xs text-red-500">
                          {{ errors[`question_${qIndex}_correct`] }}
                        </p>
                      </div>
                    </template>

                    <template v-else>
                      <div>
                        <Label :for="`correct_answer_${qIndex}`" class="text-xs sm:text-sm font-medium">
                          Correct Answer
                        </Label>
                        <Input
                          :id="`correct_answer_${qIndex}`"
                          v-model="question.correct_answer"
                          type="text"
                          placeholder="Enter correct answer"
                          class="mt-1 text-sm"
                        />
                      </div>
                    </template>

                    <!-- Explanation -->
                    <div>
                      <Label :for="`explanation_${qIndex}`" class="text-xs sm:text-sm font-medium">
                        Explanation (Optional)
                      </Label>
                      <textarea
                        :id="`explanation_${qIndex}`"
                        v-model="question.explanation"
                        rows="2"
                        placeholder="Enter explanation for the answer"
                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 text-sm"
                      ></textarea>
                    </div>
                  </div>
                </div>

                <p v-if="!((form as any).questions && (form as any).questions.length)" class="text-center py-8 text-gray-500 dark:text-gray-400 text-sm">
                  No questions added yet. Click "Add Question" to create one.
                </p>
              </div>
            </template>

            <!-- Form Actions -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 sm:pt-8 flex flex-col-reverse sm:flex-row gap-3">
              <Button
                type="button"
                variant="outline"
                @click="handleBack"
                :disabled="form.processing"
                class="border-purple-300 dark:border-purple-700 hover:bg-purple-50 dark:hover:bg-purple-900/20 text-sm sm:text-base"
              >
                Cancel
              </Button>
              <Button
                type="submit"
                :disabled="form.processing"
                class="flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white shadow-lg hover:shadow-xl transition-all duration-200 text-sm sm:text-base"
              >
                <Save :size="16" />
                {{ form.processing ? 'Saving...' : 'Save Changes' }}
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
