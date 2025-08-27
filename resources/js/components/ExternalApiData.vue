<!-- resources/js/Components/ExternalApiData.vue -->

<script setup>
import NumberCard from './NumberCard.vue';

const props = defineProps({
  data: {
    type: Object,
    default: () => ({}),
  },
})
</script>

<template>
  <div>
    <h2 class="text-xl font-semibold mb-4">External API Metrics</h2>

    <!-- Handle API errors gracefully -->
    <div v-if="data.error" class="text-red-600">
      Error: {{ data.error }}
    </div>

    <!-- Show metric cards if data is available -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
      <NumberCard
        title="Total Adjustments"
        :value="data.totalAdjustments ?? 'N/A'"
        format="currency"
        color="bg-purple-600"
        icon="🛠️"
      />
 

      <!-- Add more cards as needed -->
    </div>
  </div>
</template>
