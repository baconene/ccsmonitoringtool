<script setup lang="ts">
// No props needed, this button is only for emitting
const emit = defineEmits<{
  (e: 'open'): void;
}>();
</script>

<template>
  <button
    type="button"
    @click="emit('open')"
    class="w-full px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors"
  >
    + Add Module
  </button>
</template>
