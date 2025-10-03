<template>
  <div v-if="visible" class="fixed inset-0 flex items-center justify-center bg-black/50 dark:bg-black/70 z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-md border border-gray-200 dark:border-gray-700">
      
      <!-- Header with Icon -->
      <div class="flex items-center gap-3 mb-4">
        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
          <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>
        <div>
          <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
            {{ title || 'Remove Module' }}
          </h3>
        </div>
      </div>

      <!-- Message -->
      <p class="text-gray-600 dark:text-gray-300 mb-6">
        {{ message || 'Are you sure you want to remove this module? This action cannot be undone.' }}
      </p>

      <!-- Error Message -->
      <div v-if="error" class="mb-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800">
        <p class="text-red-700 dark:text-red-400 text-sm flex items-center gap-2">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ error }}
        </p>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end gap-3">
        <button
          @click="closeModal"
          class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors border border-gray-300 dark:border-gray-600"
          :disabled="loading"
        >
          Cancel
        </button>
        <button
          @click="confirmRemove"
          class="px-4 py-2 text-sm font-medium text-white bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-800 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
          :disabled="loading"
        >
          <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span v-if="loading">Removing...</span>
          <span v-else>Remove</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
  visible: boolean;
  moduleId?: number;
  title?: string;
  message?: string;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'removed'): void;
}>();

const loading = ref(false);
const error = ref<string | null>(null);

// Reset error when modal opens
watch(() => props.visible, (val) => {
  if (val) error.value = null;
});

function closeModal() {
  if (!loading.value) {
    emit('close');
  }
}

async function confirmRemove() {
  if (!props.moduleId) {
    error.value = 'Module ID is missing.';
    return;
  }

  loading.value = true;
  error.value = null;

  try {
    await router.delete(`/modules/${props.moduleId}`, {
  onSuccess: () => {
    emit('removed');  // notify parent to reload modules
    closeModal();
  },
  onError: (err: any) => {
    error.value = err?.message || 'Failed to remove module.';
  },
});
  } catch (err: any) {
    error.value = err?.message || 'An unexpected error occurred.';
  } finally {
    loading.value = false;
  }
}
</script>
