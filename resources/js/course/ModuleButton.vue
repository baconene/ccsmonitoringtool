<template>
  <button
    class="flex items-center justify-between w-full px-4 py-2 bg-gray-100 rounded-xl shadow-sm hover:bg-gray-200 transition"
  >
    <!-- Sequence -->
    <span v-if="seq" class="font-semibold text-gray-700">
      {{ seq }}
    </span>

    <!-- Text -->
    <span v-if="text" class="flex-1 mx-2 text-gray-800 text-left">
      {{ text }}
    </span>

    <!-- Eye + Percentage -->
    <div v-if="showEye" class="flex items-center gap-1">
           <span v-if="percentage !== undefined" class="text-green-600 text-sm font-medium">
        {{ percentage }}%
      </span>
     
    </div>
  </button>
</template>

<script setup lang="ts">
defineProps<{
  seq?: string | number
  text?: string
  showEye?: boolean
  percentage?: number
}>()
</script>
