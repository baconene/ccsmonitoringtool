<!-- src/course/AddCourseButton.vue -->
<template>
  <button
    class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-black transition"
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
