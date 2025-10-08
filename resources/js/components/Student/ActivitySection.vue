<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="p-6">
      <div class="flex items-center mb-6">
        <ClipboardList class="h-6 w-6 text-purple-600 dark:text-purple-400 mr-3" />
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
          Activities ({{ completedActivities }}/{{ activities.length }})
        </h2>
      </div>

      <div v-if="activities.length > 0" class="space-y-4">
        <div 
          v-for="activity in activities" 
          :key="activity.id"
          class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg"
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
                  <ClipboardList v-else class="h-4 w-4" />
                </div>
              </div>
              <div class="flex-1">
                <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-2">
                  {{ activity.title }}
                </h3>
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
                      class="bg-purple-600 h-2 rounded-full transition-all duration-300"
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
                <Link
                  v-if="activity.activity_type === 'Quiz'"
                  :href="getActivityLink(activity)"
                  :class="getActivityButtonClass(activity)"
                  class="px-4 py-2 text-sm font-medium rounded-md transition-colors text-center"
                >
                  {{ getActivityButtonText(activity) }}
                </Link>
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
      <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
        <ClipboardList class="h-12 w-12 mx-auto mb-4 opacity-50" />
        <p>No activities available for this module</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { 
  ClipboardList, 
  CheckCircle2, 
  Tag, 
  HelpCircle, 
  Star
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

const getActivityLink = (activity: Activity) => {
  if (activity.activity_type === 'Quiz') {
    if (activity.is_completed && activity.quiz_progress) {
      return `/student/quiz/${activity.quiz_progress.id}/results`;
    } else {
      return `/student/quiz/start/${activity.id}`;
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
</script>