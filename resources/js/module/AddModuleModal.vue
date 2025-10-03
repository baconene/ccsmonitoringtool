<script setup lang="ts">
import ModuleForm from '@/module/ModuleForm.vue';
import { defineProps, defineEmits } from 'vue';

// Props
const props = defineProps<{
  courseId: number;
  show: boolean;
}>();

// Events
const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'saved'): void;
}>();

function handleSaved() {
  emit('saved');
  emit('close');
}
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 flex items-center justify-center bg-black/50 dark:bg-black/70 z-50 p-4"
  >
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Add Module</h3>
        <button
          @click="$emit('close')"
          class="p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          title="Close"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Module form -->
      <ModuleForm :course-id="courseId" @saved="handleSaved" />
    </div>
  </div>
</template>
