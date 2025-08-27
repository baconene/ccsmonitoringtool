<template>
  <div
    class="w-full bg-white shadow-xl shadow-md p-4 m-2 hover:shadow-lg transition-all duration-300 flex items-center gap-4"
  >
    <!-- Icon Slot -->
    <div  :class="['text-sm sm:text-base', textClass]" class="text-blue-600 text-3xl">
      <slot name="icon" />
    </div>

    <!-- Number and Text -->
       <!-- Number and Text -->
    <div :class="['flex flex-col', centerText ? 'justify-center' : '']">
      <div  :class="['text-sm sm:text-base', textClass]" class="text-xl sm:text-2sm font-bold text-blue-600">{{ number }}</div>
      <div :class="['text-sm sm:text-base', textClass]">{{ text }}</div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface CounterCardProps {
  number: any
  text: any
  textClass?: string
   centerText?: boolean
}

const props = defineProps<CounterCardProps>()
</script>
