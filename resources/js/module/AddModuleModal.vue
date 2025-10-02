<script setup lang="ts">
import ModuleForm from '@/module/ModuleForm.vue';
import { defineProps, defineEmits } from 'vue';

// Props
const props = defineProps<{
  courseId: number;
  show: boolean;
}>();

// Events
const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'saved'): void;
}>();

function handleSaved() {
  emit('saved');
  emit('close');
}
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
  >
    <div class="bg-white rounded-lg p-4 w-1/2 max-h-[80vh] overflow-y-auto">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold">Add Module</h3>
        <button
          @click="$emit('close')"
          class="text-gray-500 hover:text-gray-700"
        >
          ✕
        </button>
      </div>

      <!-- Module form -->
      <ModuleForm :course-id="courseId" @saved="handleSaved" />
    </div>
  </div>
</template>
