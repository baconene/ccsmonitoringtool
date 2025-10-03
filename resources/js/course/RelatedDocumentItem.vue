<template>
  <div
    class="flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600"
  >
    <!-- Left: Icon + Name -->
    <div class="flex items-center space-x-2">
      <!-- Icon depends on doc.type -->
      <span v-html="icon" class="w-5 h-5 text-gray-600 dark:text-gray-400"></span>
      <span class="text-gray-700 dark:text-gray-300">{{ doc?.name || "Untitled" }}</span>
    </div>

    <!-- Remove Button -->
    <button
      @click="$emit('remove')"
      class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-bold transition-colors"
    >
      x
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{
  doc?: {
    name?: string;
    type?: string;
  };
}>();

defineEmits<{
  (e: "remove"): void;
}>();

// Dynamically return SVG icons based on doc.type
const icon = computed(() => {
  const type = props.doc?.type?.toLowerCase?.() || "";

  switch (type) {
    case "pdf":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M6 2h9l5 5v15a1 1 0 01-1 1H6a1 1 0 01-1-1V3a1 1 0 011-1zm7 1.5V9h5.5L13 3.5zM8 13h8v2H8v-2zm0 4h5v2H8v-2z"/></svg>`;
    case "word":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M6 2h9l5 5v15a1 1 0 01-1 1H6a1 1 0 01-1-1V3a1 1 0 011-1zm7 1.5V9h5.5L13 3.5zM7.5 12h2l1 4 1-4h2l1 4 1-4h2l-2.5 8h-2l-1-4-1 4h-2l-2.5-8z"/></svg>`;
    case "excel":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M6 2h9l5 5v15a1 1 0 01-1 1H6a1 1 0 01-1-1V3a1 1 0 011-1zm7 1.5V9h5.5L13 3.5zM8.5 12h2l1.5 2 1.5-2h2l-2.5 3 2.5 3h-2l-1.5-2-1.5 2h-2l2.5-3-2.5-3z"/></svg>`;
    case "image":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 24 24"><path d="M4 5a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V7a2 2 0 00-2-2H4zm0 2h16v5l-4-3-4 5-3-2-5 5V7z"/></svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M6 2h9l5 5v15a1 1 0 01-1 1H6a1 1 0 01-1-1V3a1 1 0 011-1zm7 1.5V9h5.5L13 3.5z"/></svg>`;
  }
});
</script>
