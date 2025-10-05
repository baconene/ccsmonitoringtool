<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
        </svg>
        Documents
        <button
          @click="$emit('upload')"
          class="p-2 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors group"
          title="Upload Document"
        >
          <Plus class="h-5 w-5 text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" />
        </button>
      </h3>
      
      <div class="text-sm text-gray-500 dark:text-gray-400">
        Available for all students
      </div>
    </div>

    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
      <div class="text-center py-8 text-gray-500 dark:text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
        </svg>
        <p class="text-sm">No documents uploaded yet</p>
        <p class="text-xs mt-1">Click the + button above to upload documents</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Plus } from "lucide-vue-next";

defineEmits<{
  (e: 'upload'): void;
}>();
</script>
