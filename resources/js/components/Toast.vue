<script setup>
import { useToast } from "@/composables/useToast"

const { message, show } = useToast()
</script>

<template>
  <div
    v-if="show"
    class="fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded shadow"
  >
    {{ message }}
  </div>
</template>
