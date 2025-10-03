<template>
  <div class="flex-1 p-6 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 overflow-y-auto shadow-sm">
    <template v-if="module">
      <!-- Module Header -->
      <div class="mb-6">
        <!-- Module Title and Actions -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-4">
          <div class="flex-1">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
              {{ module.description }}
            </h2>
            <div class="flex flex-wrap items-center gap-4 text-sm">
              <div class="flex items-center gap-1">
                <span class="text-gray-500 dark:text-gray-400">Sequence:</span>
                <span class="font-medium text-gray-700 dark:text-gray-300">{{ module.sequence }}</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="text-gray-500 dark:text-gray-400">Lessons:</span>
                <span class="font-medium text-gray-700 dark:text-gray-300">{{ module.lessons?.length || 0 }}</span>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center gap-2">
            <!-- Progress Badge -->
            <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-green-100 dark:bg-green-900/30">
              <div class="w-2 h-2 rounded-full bg-green-500 dark:bg-green-400"></div>
              <span class="text-sm font-medium text-green-700 dark:text-green-400">
                {{ module.completion_percentage }}% Complete
              </span>
            </div>

            <!-- Edit Module -->
            <button
              @click="$emit('edit', module)"
              class="p-2 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors group"
              title="Edit Module"
            >
              <Edit3 class="h-5 w-5 text-gray-600 dark:text-gray-400 group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition-colors" />
            </button>

       

            <!-- Remove Module -->
            <button
              @click="$emit('remove', module)"
              class="p-2 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors group"
              title="Remove Module"
            >
              <Trash class="h-5 w-5 text-gray-600 dark:text-gray-400 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors" />
            </button>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
          <div 
            class="h-full bg-gradient-to-r from-green-500 to-green-600 dark:from-green-400 dark:to-green-500 transition-all duration-500 ease-out"
            :style="{ width: `${module.completion_percentage || 0}%` }"
          ></div>
        </div>
      </div>

      <!-- Lessons Section -->
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            Lessons
                 <!-- Add Lesson -->
            <button
              @click="$emit('add-lesson', module)"
              class="p-2 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors group"
              title="Add Lesson"
            >
              <Plus class="h-5 w-5 text-gray-600 dark:text-gray-400 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors" />
            </button>
          </h3>
          
          <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ module.lessons?.length || 0 }} lesson{{ (module.lessons?.length || 0) === 1 ? '' : 's' }} total
          </div>
        </div>

        <!-- Lessons Container -->
        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
          <LessonList :module-id="module.id" :lessons="module.lessons" />
        </div>
      </div>
    </template>

    <!-- Empty State -->
    <template v-else>
      <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
          <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
        </div>
        
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
          No Module Selected
        </h3>
        
        <p class="text-gray-500 dark:text-gray-400 max-w-md">
          Select a module from the list on the left to view its details, manage lessons, and track progress.
        </p>
        
        <div class="mt-6 text-sm text-gray-400 dark:text-gray-500">
          💡 Tip: Double-click on any module to start editing
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import LessonList from "@/lesson/lessonList.vue";
import { Edit3, Trash, Plus } from "lucide-vue-next";

const props = defineProps<{
  module: {
    id: number;
    description: string;
    sequence: number;
    completion_percentage: number;
    lessons: Array<{
      id: number;
      title: string;
      description: string;
      documents: Array<{
        id: number;
        name: string;
        file_path: string;
        doc_type: string;
      }>;
    }>;
  } | null;
}>();

defineEmits<{
  (e: "edit", module: typeof props.module): void;
  (e: "remove", module: typeof props.module): void;
  (e: "add-lesson", module: typeof props.module): void;
}>();
</script>
