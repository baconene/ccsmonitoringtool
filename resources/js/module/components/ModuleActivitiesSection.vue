<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
        </svg>
        Activities
        <button
          @click="$emit('add')"
          class="p-2 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors group"
          title="Add Activity"
        >
          <Plus class="h-5 w-5 text-gray-600 dark:text-gray-400 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors" />
        </button>
      </h3>
      
      <div class="text-sm text-gray-500 dark:text-gray-400">
        {{ activities.length }} activit{{ activities.length === 1 ? 'y' : 'ies' }} total
      </div>
    </div>

    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
      <div v-if="activities.length > 0" class="space-y-3">
        <div
          v-for="activity in activities"
          :key="activity.id"
          class="flex items-start gap-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow"
        >
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-2">
              <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ activity.title }}</h4>
              <span
                class="px-2 py-0.5 text-xs rounded-full flex-shrink-0"
                :class="getActivityTypeBadgeClass((activity.activityType || activity.activity_type)?.name)"
              >
                {{ getActivityTypeName(activity) }}
              </span>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ activity.description }}</p>
            
            <!-- Activity Stats -->
            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
              <span v-if="activity.question_count" class="flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ activity.question_count }} question{{ activity.question_count !== 1 ? 's' : '' }}
              </span>
              <span v-if="activity.total_points" class="flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                {{ activity.total_points }} points
              </span>
              <span v-if="activity.has_due_date" class="flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Due date set
              </span>
              <span v-if="activity.creator" class="flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                {{ activity.creator.name }}
              </span>
            </div>
          </div>
          
          <button
            @click="$emit('remove', activity.id)"
            class="p-2 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors group flex-shrink-0"
            title="Remove Activity"
          >
            <Trash class="h-4 w-4 text-gray-600 dark:text-gray-400 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors" />
          </button>
        </div>
      </div>
      
      <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <p class="text-sm">No activities added yet</p>
        <p class="text-xs mt-1">Click the + button above to add activities</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Plus, Trash } from "lucide-vue-next";
import { useActivityType } from '@/composables/useActivityType';
import type { Activity } from '@/types';

const props = defineProps<{
  activities: Activity[];
}>();

defineEmits<{
  (e: 'add'): void;
  (e: 'remove', activityId: number): void;
}>();

const { getActivityTypeName, getActivityTypeBadgeClass } = useActivityType();
</script>
