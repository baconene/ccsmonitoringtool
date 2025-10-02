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
    class="w-full px-3 py-1 bg-gray-500 text-white rounded hover:bg-black transition"
  >
    + Add Module
  </button>
</template>
