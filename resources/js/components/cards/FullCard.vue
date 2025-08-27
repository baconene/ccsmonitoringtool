<template>
  <div class="w-full h-full bg-white shadow-lg rounded-xl flex flex-col justify-between p-6">
    <!-- Centered Number -->
    <div class="flex-grow flex items-center justify-center">
      <div class="text-4xl font-extrabold text-blue-700">{{ number }}</div>
    </div>

    <!-- Bottom Text -->
    <div class="text-center text-sm text-gray-600">
      {{ text }}
    </div>
  </div>
</template>

<script setup lang="ts">
interface FullCardCounterProps {
  number: any
  text: string
}

defineProps<FullCardCounterProps>()
</script>
