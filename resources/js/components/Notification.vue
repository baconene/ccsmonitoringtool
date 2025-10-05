<script setup lang="ts">
import type { Notification } from '@/composables/useNotification';

defineProps<{
  notification: Notification | null;
}>();
</script>

<template>
  <Transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0 translate-y-2"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 translate-y-2"
  >
    <div
      v-if="notification"
      :class="[
        'fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-md',
        notification.type === 'success' && 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200',
        notification.type === 'error' && 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200',
        notification.type === 'info' && 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200',
        notification.type === 'warning' && 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200'
      ]"
    >
      <div class="flex items-start">
        <!-- Success Icon -->
        <svg
          v-if="notification.type === 'success'"
          class="w-5 h-5 mr-3 flex-shrink-0"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        
        <!-- Error Icon -->
        <svg
          v-else-if="notification.type === 'error'"
          class="w-5 h-5 mr-3 flex-shrink-0"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        
        <!-- Info Icon -->
        <svg
          v-else-if="notification.type === 'info'"
          class="w-5 h-5 mr-3 flex-shrink-0"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        
        <!-- Warning Icon -->
        <svg
          v-else-if="notification.type === 'warning'"
          class="w-5 h-5 mr-3 flex-shrink-0"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        
        <p class="flex-1">{{ notification.message }}</p>
      </div>
    </div>
  </Transition>
</template>
