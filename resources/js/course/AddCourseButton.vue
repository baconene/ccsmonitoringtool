<!-- src/course/AddCourseButton.vue -->
<template>
  <button
    class="px-4 sm:px-6 py-2 sm:py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white text-sm sm:text-base rounded-lg font-semibold transition-all shadow-lg hover:shadow-xl hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 flex items-center justify-center gap-2 whitespace-nowrap"
    @click="$emit('open')"
  >
    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
    </svg>
    <span>Add Course</span>
  </button>
</template>

<script setup lang="ts">
// Emits a simple event when clicked
defineEmits<{
  (e: 'open'): void
}>()
</script>
