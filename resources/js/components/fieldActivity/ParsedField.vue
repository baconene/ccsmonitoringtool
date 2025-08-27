<template>
  <div class="mb-2">
    <p v-if="typeof value !== 'object' || value === null" class="text-gray-700">
      <span class="font-semibold">{{ label }}:</span> {{ value }}
    </p>

    <div v-else-if="Array.isArray(value)">
      <p class="font-semibold text-gray-700">{{ label }}:</p>
      <ul class="ml-4 list-disc">
        <li v-for="(item, index) in value" :key="index">
          <ParsedField :label="`${label} [${index + 1}]`" :value="item" />
        </li>
      </ul>
    </div>

    <div v-else class="ml-2">
      <p class="font-semibold text-gray-700">{{ label }}:</p>
      <div class="ml-4 border-l border-gray-300 pl-4">
        <ParsedField
          v-for="(subVal, subKey) in value"
          :key="subKey"
          :label="subKey"
          :value="subVal"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  value: [String, Object, Array]
})
</script>
