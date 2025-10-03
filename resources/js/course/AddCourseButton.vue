<!-- src/course/AddCourseButton.vue -->
<template>
  <button
    class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors whitespace-nowrap"
    @click="$emit('open')"
  >
    + Add Course
  </button>
</template>

<script setup lang="ts">
// Emits a simple event when clicked
defineEmits<{
  (e: 'open'): void
}>()
</script>
