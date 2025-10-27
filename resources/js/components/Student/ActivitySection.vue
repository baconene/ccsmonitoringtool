<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="p-6">
      <div class="flex items-center mb-6">
        <ClipboardList class="h-6 w-6 text-purple-600 dark:text-purple-400 mr-3" />
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
          Activities ({{ completedActivities }}/{{ activities.length }})
        </h2>
      </div>

      <div v-if="activities.length > 0" class="space-y-6">
        <!-- Quizzes Section -->
        <div v-if="quizzes.length > 0" class="space-y-3">
          <div class="flex items-center mb-4">
            <HelpCircle class="h-5 w-5 text-blue-600 dark:text-blue-400 mr-2" />
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
              Quizzes ({{ completedQuizzes }}/{{ quizzes.length }})
            </h3>
          </div>
          <div 
            v-for="activity in quizzes" 
            :key="activity.id"
            class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800"
          >
            <div class="flex items-start justify-between">
              <div class="flex items-start flex-1">
                <div class="flex-shrink-0 mr-4 mt-1">
                  <div 
                    :class="activity.is_completed 
                      ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' 
                      : 'bg-gray-100 text-gray-400 dark:bg-gray-600 dark:text-gray-500'"
                    class="w-8 h-8 rounded-full flex items-center justify-center"
                  >
                    <CheckCircle2 v-if="activity.is_completed" class="h-4 w-4" />
                    <HelpCircle v-else class="h-4 w-4" />
                  </div>
                </div>
                <div class="flex-1">
                  <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">
                    {{ activity.title }}
                  </h4>
                  <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    {{ activity.description }}
                  </p>
                  
                  <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 mb-3">
                    <span class="flex items-center">
                      <Tag class="h-3 w-3 mr-1" />
                      {{ activity.activity_type }}
                    </span>
                    <span v-if="activity.question_count > 0" class="flex items-center">
                      <HelpCircle class="h-3 w-3 mr-1" />
                      {{ activity.question_count }} questions
                    </span>
                    <span v-if="activity.total_points > 0" class="flex items-center">
                      <Star class="h-3 w-3 mr-1" />
                      {{ activity.total_points }} points
                    </span>
                  </div>

                  <!-- Progress Information -->
                  <div v-if="activity.quiz_progress" class="mb-3">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                      Progress: {{ activity.quiz_progress.completed_questions }}/{{ activity.quiz_progress.total_questions }} questions
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mb-2">
                      <div 
                        class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                        :style="{ width: `${(activity.quiz_progress.completed_questions / activity.quiz_progress.total_questions) * 100}%` }"
                      ></div>
                    </div>
                    <div v-if="activity.is_completed" class="text-sm">
                      <span class="text-green-600 dark:text-green-400 font-medium">
                        Score: {{ activity.quiz_progress.percentage_score }}%
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex-shrink-0 ml-4">
                <div class="flex flex-col gap-2">
                  <!-- Quiz with questions - Start/Continue/View Results -->
                  <Link
                    v-if="activity.activity_type === 'Quiz' && activity.question_count > 0"
                    :href="getActivityLink(activity)"
                    :class="getActivityButtonClass(activity)"
                    class="px-4 py-2 text-sm font-medium rounded-md transition-colors text-center"
                  >
                    {{ getActivityButtonText(activity) }}
                  </Link>
                  
                  <!-- Mark as Complete button for non-quiz activities OR quiz/assignment with 0 questions -->
                  <button
                    v-else-if="!activity.is_completed && (
                      !['Quiz', 'Assignment'].includes(activity.activity_type) || 
                      (['Quiz', 'Assignment'].includes(activity.activity_type) && activity.question_count === 0)
                    )"
                    @click="markActivityComplete(activity)"
                    :disabled="isMarkingComplete"
                    class="px-4 py-2 text-sm font-medium bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-md transition-colors"
                  >
                    {{ isMarkingComplete ? 'Marking...' : 'Mark as Complete' }}
                  </button>
                  
                  <!-- View Activity button for completed activities or activities that can't be marked complete -->
                  <Link
                    v-else
                    :href="`/student/activities/${activity.id}`"
                    class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors text-center"
                  >
                    {{ activity.is_completed ? 'View Activity' : 'View Activity' }}
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Assignments Section -->
        <div v-if="assignments.length > 0" class="space-y-3">
          <div class="flex items-center mb-4">
            <FileText class="h-5 w-5 text-orange-600 dark:text-orange-400 mr-2" />
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
              Assignments ({{ completedAssignments }}/{{ assignments.length }})
            </h3>
          </div>
          <div 
            v-for="activity in assignments" 
            :key="activity.id"
            class="p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800"
          >
            <div class="flex items-start justify-between">
              <div class="flex items-start flex-1">
                <div class="flex-shrink-0 mr-4 mt-1">
                  <div 
                    :class="activity.is_completed 
                      ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' 
                      : 'bg-gray-100 text-gray-400 dark:bg-gray-600 dark:text-gray-500'"
                    class="w-8 h-8 rounded-full flex items-center justify-center"
                  >
                    <CheckCircle2 v-if="activity.is_completed" class="h-4 w-4" />
                    <FileText v-else class="h-4 w-4" />
                  </div>
                </div>
                <div class="flex-1">
                  <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">
                    {{ activity.title }}
                  </h4>
                  <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    {{ activity.description }}
                  </p>
                  
                  <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 mb-3">
                    <span class="flex items-center">
                      <Tag class="h-3 w-3 mr-1" />
                      {{ activity.activity_type }}
                    </span>
                    <span v-if="activity.question_count > 0" class="flex items-center">
                      <HelpCircle class="h-3 w-3 mr-1" />
                      {{ activity.question_count }} questions
                    </span>
                    <span v-if="activity.total_points > 0" class="flex items-center">
                      <Star class="h-3 w-3 mr-1" />
                      {{ activity.total_points }} points
                    </span>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex-shrink-0 ml-4">
                <div class="flex flex-col gap-2">
                  <!-- Mark as Complete button for assignments with 0 questions -->
                  <button
                    v-if="!activity.is_completed && activity.question_count === 0"
                    @click="markActivityComplete(activity)"
                    :disabled="isMarkingComplete"
                    class="px-4 py-2 text-sm font-medium bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-md transition-colors"
                  >
                    {{ isMarkingComplete ? 'Marking...' : 'Mark as Complete' }}
                  </button>
                  
                  <!-- View Activity button -->
                  <Link
                    v-else
                    :href="`/student/activities/${activity.id}`"
                    class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors text-center"
                  >
                    {{ activity.is_completed ? 'View Activity' : 'View Activity' }}
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Other Activities Section -->
        <div v-if="otherActivities.length > 0" class="space-y-3">
          <div class="flex items-center mb-4">
            <Book class="h-5 w-5 text-green-600 dark:text-green-400 mr-2" />
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
              Other Activities ({{ completedOtherActivities }}/{{ otherActivities.length }})
            </h3>
          </div>
          <div 
            v-for="activity in otherActivities" 
            :key="activity.id"
            class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800"
          >
            <div class="flex items-start justify-between">
              <div class="flex items-start flex-1">
                <div class="flex-shrink-0 mr-4 mt-1">
                  <div 
                    :class="activity.is_completed 
                      ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' 
                      : 'bg-gray-100 text-gray-400 dark:bg-gray-600 dark:text-gray-500'"
                    class="w-8 h-8 rounded-full flex items-center justify-center"
                  >
                    <CheckCircle2 v-if="activity.is_completed" class="h-4 w-4" />
                    <Book v-else class="h-4 w-4" />
                  </div>
                </div>
                <div class="flex-1">
                  <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">
                    {{ activity.title }}
                  </h4>
                  <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    {{ activity.description }}
                  </p>
                  
                  <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 mb-3">
                    <span class="flex items-center">
                      <Tag class="h-3 w-3 mr-1" />
                      {{ activity.activity_type }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex-shrink-0 ml-4">
                <div class="flex flex-col gap-2">
                  <!-- Mark as Complete button for other activities -->
                  <button
                    v-if="!activity.is_completed"
                    @click="markActivityComplete(activity)"
                    :disabled="isMarkingComplete"
                    class="px-4 py-2 text-sm font-medium bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-md transition-colors"
                  >
                    {{ isMarkingComplete ? 'Marking...' : 'Mark as Complete' }}
                  </button>
                  
                  <!-- View Activity button -->
                  <Link
                    v-else
                    :href="`/student/activities/${activity.id}`"
                    class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors text-center"
                  >
                    View Activity
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
        <ClipboardList class="h-12 w-12 mx-auto mb-4 opacity-50" />
        <p>No activities available for this module</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { 
  ClipboardList, 
  CheckCircle2, 
  Tag, 
  HelpCircle, 
  Star,
  FileText,
  Book
} from 'lucide-vue-next';

interface Activity {
  id: number;
  title: string;
  description: string;
  activity_type: string;
  question_count: number;
  total_points: number;
  is_completed: boolean;
  quiz_progress?: {
    id: number;
    is_completed: boolean;
    is_submitted: boolean;
    score: number;
    percentage_score: number;
    completed_questions: number;
    total_questions: number;
  } | null;
}

const props = defineProps<{
  activities: Activity[];
}>();

const completedActivities = computed(() => {
  return props.activities.filter(activity => activity.is_completed).length;
});

// Segregate activities by type
const quizzes = computed(() => {
  return props.activities.filter(activity => activity.activity_type === 'Quiz');
});

const assignments = computed(() => {
  return props.activities.filter(activity => activity.activity_type === 'Assignment');
});

const otherActivities = computed(() => {
  return props.activities.filter(activity => !['Quiz', 'Assignment'].includes(activity.activity_type));
});

// Count completed activities by type
const completedQuizzes = computed(() => {
  return quizzes.value.filter(activity => activity.is_completed).length;
});

const completedAssignments = computed(() => {
  return assignments.value.filter(activity => activity.is_completed).length;
});

const completedOtherActivities = computed(() => {
  return otherActivities.value.filter(activity => activity.is_completed).length;
});

const getActivityLink = (activity: Activity) => {
  if (activity.activity_type === 'Quiz') {
    if (activity.is_completed && activity.quiz_progress) {
      // Use student_activity_id from quiz_progress, not progress id
      return `/student/quiz/${activity.quiz_progress.student_activity_id}/results`;
    } else {
      return `/student/quiz/start/${activity.id}`;
    }
  }
  if (activity.activity_type === 'Assignment') {
    if (activity.is_completed && activity.student_activity) {
      // Use activity type and student_activity id
      return `/student/assignment/${activity.student_activity.id}/results`;
    } else {
      return `/student/assignment/start/${activity.id}`;
    }
  }
  return `/student/activities/${activity.id}`;
};

const getActivityButtonClass = (activity: Activity) => {
  if (activity.is_completed) {
    return 'bg-green-600 hover:bg-green-700 text-white';
  } else if (activity.quiz_progress && !activity.quiz_progress.is_completed) {
    return 'bg-yellow-600 hover:bg-yellow-700 text-white';
  } else {
    return 'bg-blue-600 hover:bg-blue-700 text-white';
  }
};

const getActivityButtonText = (activity: Activity) => {
  if (activity.is_completed) {
    return 'View Results';
  } else if (activity.quiz_progress && !activity.quiz_progress.is_completed) {
    return 'Continue Quiz';
  } else {
    return 'Start Quiz';
  }
};

// Mark activity as complete functionality
const isMarkingComplete = ref(false);

const markActivityComplete = async (activity: Activity) => {
  if (isMarkingComplete.value) return;
  
  isMarkingComplete.value = true;
  
  try {
    await router.post(`/activities/${activity.id}/mark-complete`, {}, {
      onSuccess: () => {
        // Update the activity locally to reflect the completion
        activity.is_completed = true;
        // Add a success notification if needed
        console.log('Activity marked as complete successfully');
      },
      onError: (errors) => {
        console.error('Failed to mark activity as complete:', errors);
        // Handle error (show notification, etc.)
      },
      onFinish: () => {
        isMarkingComplete.value = false;
      }
    });
  } catch (error) {
    console.error('Unexpected error:', error);
    isMarkingComplete.value = false;
  }
};
</script>