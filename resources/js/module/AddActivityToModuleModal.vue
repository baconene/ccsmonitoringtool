<template>
  <div
    v-if="visible"
    class="fixed inset-0 flex items-center justify-center bg-black/50 dark:bg-black/70 z-50 p-4"
  >
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Add Activities to Module</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Select activities to add to this {{ moduleType }} module
          </p>
        </div>
        <button
          @click="closeModal"
          class="p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          title="Close"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Search and Filter -->
      <div class="mb-6 space-y-4">
        <div class="relative">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search activities..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
          />
        </div>

        <!-- Activity Type Filter (for specific module types) -->
        <div v-if="moduleType !== 'Mixed' && moduleType !== 'Activities'" class="flex items-center gap-2">
          <Filter class="w-4 h-4 text-gray-500 dark:text-gray-400" />
          <span class="text-sm text-gray-600 dark:text-gray-400">
            Showing only {{ moduleType }} activities
          </span>
        </div>
      </div>

      <!-- Activities List -->
      <div class="space-y-3 mb-6 max-h-96 overflow-y-auto">
        <div
          v-for="activity in filteredActivities"
          :key="activity.id"
          @click="toggleActivity(activity.id)"
          class="flex items-start gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer transition-all"
          :class="{
            'bg-blue-50 dark:bg-blue-900/20 border-blue-500 dark:border-blue-400': selectedActivities.includes(activity.id),
            'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700': !selectedActivities.includes(activity.id)
          }"
        >
          <!-- Checkbox -->
          <div class="flex items-center pt-1">
            <div
              class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors"
              :class="{
                'bg-blue-500 border-blue-500': selectedActivities.includes(activity.id),
                'border-gray-300 dark:border-gray-600': !selectedActivities.includes(activity.id)
              }"
            >
              <svg
                v-if="selectedActivities.includes(activity.id)"
                class="w-3 h-3 text-white"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
              </svg>
            </div>
          </div>

          <!-- Activity Info -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ activity.title }}</h4>
              <span
                class="px-2 py-0.5 text-xs rounded-full"
                :class="getActivityTypeBadgeClass(getActivityType(activity)?.name)"
              >
                {{ getActivityType(activity)?.name || 'Unknown' }}
              </span>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
              {{ activity.description || 'No description' }}
            </p>
            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
              <span v-if="activity.question_count">
                {{ activity.question_count }} question{{ activity.question_count !== 1 ? 's' : '' }}
              </span>
              <span v-else-if="getActivityType(activity)?.name.toLowerCase().includes('quiz')" class="text-red-500 dark:text-red-400">
                ⚠️ No questions available
              </span>
              <span v-if="activity.total_points">
                {{ activity.total_points }} points
              </span>
              <span v-if="activity.creator">
                Created by {{ activity.creator.name }}
              </span>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredActivities.length === 0" class="text-center py-12">
          <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
            No Activities Found
          </h3>
          <p class="text-gray-500 dark:text-gray-400">
            {{ searchQuery ? 'Try adjusting your search' : 'No activities available to add' }}
          </p>
        </div>
      </div>

      <!-- Selected Count -->
      <div v-if="selectedActivities.length > 0" class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
        <p class="text-sm text-blue-700 dark:text-blue-400">
          {{ selectedActivities.length }} activit{{ selectedActivities.length === 1 ? 'y' : 'ies' }} selected
        </p>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button
          type="button"
          @click="closeModal"
          class="w-full sm:w-auto px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors border border-gray-300 dark:border-gray-600"
          :disabled="isSubmitting"
        >
          Cancel
        </button>
        <button
          type="button"
          @click="addActivities"
          class="w-full sm:w-auto px-6 py-3 text-sm font-medium text-white bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          :disabled="selectedActivities.length === 0 || isSubmitting"
        >
          <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span v-if="isSubmitting">Adding...</span>
          <span v-else>Add Selected Activities</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { X, Search, Filter } from 'lucide-vue-next';
import type { Activity } from '@/types';

const props = defineProps<{
  visible: boolean;
  moduleId: number;
  moduleType: string;
  availableActivities: Activity[];
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'added'): void;
}>();

const searchQuery = ref('');
const selectedActivities = ref<number[]>([]);
const isSubmitting = ref(false);

// Helper to get activity type (handles both snake_case and camelCase)
const getActivityType = (activity: Activity) => {
  const activityType = activity.activityType || (activity as any).activity_type;
  return typeof activityType === 'object' ? activityType : null;
};

// Filter activities based on module type
const filteredActivities = computed(() => {
  let activities = props.availableActivities;

  // Filter by module type
  if (props.moduleType === 'Quizzes') {
    activities = activities.filter(a => {
      const type = getActivityType(a);
      const isQuizType = type?.name.toLowerCase().includes('quiz');
      // For quiz activities, also check if they have quiz content (questions)
      const hasQuizContent = isQuizType ? (a.question_count && a.question_count > 0) : true;
      return isQuizType && hasQuizContent;
    });
  } else if (props.moduleType === 'Assignments') {
    activities = activities.filter(a => {
      const type = getActivityType(a);
      return type?.name.toLowerCase().includes('assignment');
    });
  } else if (props.moduleType === 'Assessment') {
    activities = activities.filter(a => {
      const type = getActivityType(a);
      return type?.name.toLowerCase().includes('assessment') ||
             type?.name.toLowerCase().includes('exam') ||
             type?.name.toLowerCase().includes('test');
    });
  }

  // Filter by search query FIRST (before type filtering)
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    activities = activities.filter(a => {
      const type = getActivityType(a);
      return a.title.toLowerCase().includes(query) ||
             a.description?.toLowerCase().includes(query) ||
             type?.name.toLowerCase().includes(query);
    });
  }

  // For mixed modules, only filter out incomplete quiz activities if NOT searching
  // This allows users to find and add activities even if they don't have content yet
  if ((props.moduleType === 'Mixed' || props.moduleType === 'Activities') && !searchQuery.value) {
    activities = activities.filter(a => {
      const type = getActivityType(a);
      const isQuizType = type?.name.toLowerCase().includes('quiz');
      // If it's a quiz, make sure it has content
      if (isQuizType) {
        return a.question_count && a.question_count > 0;
      }
      return true; // Non-quiz activities are fine
    });
  }

  return activities;
});

function toggleActivity(activityId: number) {
  const index = selectedActivities.value.indexOf(activityId);
  if (index > -1) {
    selectedActivities.value.splice(index, 1);
  } else {
    selectedActivities.value.push(activityId);
  }
}

function getActivityTypeBadgeClass(typeName?: string) {
  if (!typeName) return 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
  
  const name = typeName.toLowerCase();
  if (name.includes('quiz')) {
    return 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400';
  } else if (name.includes('assignment')) {
    return 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400';
  } else if (name.includes('exercise')) {
    return 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400';
  }
  return 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
}

function addActivities() {
  if (selectedActivities.value.length === 0) return;

  isSubmitting.value = true;

  router.post(`/modules/${props.moduleId}/activities`, {
    activity_ids: selectedActivities.value
  }, {
    onSuccess: () => {
      emit('added');
      closeModal();
    },
    onError: (errors) => {
      console.error('Error adding activities:', errors);
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
}

function closeModal() {
  selectedActivities.value = [];
  searchQuery.value = '';
  emit('close');
}
</script>
