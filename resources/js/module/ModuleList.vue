<template>
  <div class="flex flex-col overflow-y-auto">
    <div
      v-for="module in sortedModules"
      :key="module.id"
      @click="selectModule(module.id)"
      :class="{
        'bg-blue-50 border-blue-500': activeModuleId === module.id
      }"
      class="border rounded-lg p-2 mb-2 cursor-pointer hover:bg-blue-100 flex flex-col"
    >
      <div class="flex justify-between items-center w-full">
        <!-- ✅ pass percentage -->
        <ModuleButton
          :seq="module.sequence"
          :text="module.description"
          :percentage="module.completion_percentage"
          :showEye="true"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import ModuleButton from '@/course/ModuleButton.vue';
import { computed } from 'vue';

const props = defineProps<{
  modules: Array<{
    id: number;
    description: string;
    sequence: number;
    completion_percentage: number;
    lessons: any[];
  }>;
  activeModuleId: number | null;
}>();

const emit = defineEmits<{
  (e: 'selectModule', moduleId: number): void;
}>();

const sortedModules = computed(() => {
  return [...props.modules].sort((a, b) => a.sequence - b.sequence);
});

function selectModule(moduleId: number) {
  emit('selectModule', moduleId);
}
</script>
