<template>
  <div class="bg-white shadow rounded-lg p-6 flex flex-col items-center">
    <div class="text-gray-500 text-sm mb-2">{{ title }}</div>
    <div class="text-3xl font-bold text-gray-800">{{ formattedValue }}</div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [Number, String],
    required: true
  },
  format: {
    type: String,
    default: 'number' // or 'currency', 'percent'
  }
})

const formattedValue = computed(() => {
  if (props.format === 'currency') {
    return new Intl.NumberFormat(undefined, {
      style: 'currency',
      currency: 'USD'
    }).format(Number(props.value))
  }
  if (props.format === 'percent') {
    return `${Number(props.value).toFixed(2)}%`
  }
  return Number(props.value).toLocaleString()
})
</script>
