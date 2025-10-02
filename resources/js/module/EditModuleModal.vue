<template>
  <div v-if="visible" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
    <div class="bg-white rounded-lg p-4 w-1/2 max-h-[80vh] overflow-y-auto">
      
      <!-- Header -->
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold">{{ moduleId ? 'Edit Module' : 'Add Module' }}</h3>
        <button @click="closeModal" class="text-gray-500 hover:text-gray-700">✕</button>
      </div>

      <!-- ModuleForm component -->
      <ModuleForm
        :courseId="courseId"
        :moduleId="moduleId"
        :defaults="defaults"
        @saved="handleSaved"
      />
      
    </div>
  </div>
</template>

<script setup lang="ts">
import ModuleForm from '@/module/ModuleForm.vue';

const props = defineProps<{
  courseId: number;
  moduleId?: number;
  defaults?: {
    description?: string;
    sequence?: number;
    completion_percentage?: number;
  };
  visible: boolean; // controlled via v-model
}>()

const emit = defineEmits<{
  (e: 'update:visible', value: boolean): void;
  (e: 'saved'): void;
}>()

function closeModal() {
  emit('update:visible', false); // <-- this closes the modal
}

function handleSaved() {
  emit('saved');
  closeModal();
}
 
</script>
