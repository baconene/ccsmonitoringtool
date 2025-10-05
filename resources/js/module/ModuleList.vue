<template>
  <div class="flex flex-col space-y-3 p-1 max-h-full">
    <div
      v-for="module in sortedModules"
      :key="module.id"
      @click="selectModule(module.id)"
      :class="{
        'bg-blue-50 dark:bg-blue-900/50 border-blue-500 dark:border-blue-400 shadow-md': activeModuleId === module.id,
        'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 shadow-sm': activeModuleId !== module.id
      }"
      class="border rounded-xl p-3 sm:p-4 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:shadow-md transition-all duration-200 group flex-shrink-0"
    >
      <div class="flex items-center justify-between w-full min-w-0">
        <!-- Module Sequence -->
        <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
          <div class="flex-shrink-0 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
            <span class="text-xs sm:text-sm font-semibold text-blue-700 dark:text-blue-300">
              {{ module.sequence }}
            </span>
          </div>
          
            <!-- Module Info -->
            <div class="flex-1 min-w-0 overflow-hidden">
            <h3 class="text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors truncate" 
              :title="module.description">
              {{ module.description }}
            </h3>
            <div class="flex items-center justify-between mt-1">
              <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
              {{ module.lessons?.length || 0 }} lesson{{ (module.lessons?.length || 0) === 1 ? '' : 's' }}
              </p>
              <div class="flex items-center gap-2 ml-2">
              <span 
                class="text-xs font-medium text-green-600 dark:text-green-400 whitespace-nowrap"
                :title="`This module makes up ${module.completion_percentage || 0}% of the course`"
              >
                {{ module.completion_percentage || 0 }}%
              </span>
              <div class="w-12 sm:w-16 h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                <div 
                class="h-full bg-green-500 dark:bg-green-400 rounded-full transition-all duration-300"
                :style="{ width: `${module.completion_percentage || 0}%` }"
                ></div>
              </div>
              </div>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="modules.length === 0" class="text-center py-8 flex-shrink-0">
      <div class="text-gray-400 dark:text-gray-500 mb-2">
        <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
      </div>
      <p class="text-sm text-gray-500 dark:text-gray-400">No modules available</p>
      <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Add modules to get started</p>
    </div>
   
</template>

<script setup lang="ts">
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
