<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        Lessons
        <button
          @click="$emit('add')"
          class="p-2 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors group"
          title="Add Lesson"
        >
          <Plus class="h-5 w-5 text-gray-600 dark:text-gray-400 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors" />
        </button>
      </h3>
      
      <div class="text-sm text-gray-500 dark:text-gray-400">
        {{ lessons.length }} lesson{{ lessons.length === 1 ? '' : 's' }} total
      </div>
    </div>

    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
      <LessonList 
        :module-id="moduleId" 
        :lessons="lessons" 
        @update:lessons="handleLessonsUpdate"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import LessonList from "@/lesson/lessonList.vue";
import { Plus } from "lucide-vue-next";

defineProps<{
  moduleId: number;
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
}>();

const emit = defineEmits<{
  (e: 'add'): void;
  (e: 'update:lessons', lessons: any[]): void;
}>();

// Forward lessons update to parent
function handleLessonsUpdate(updatedLessons: any[]) {
  emit('update:lessons', updatedLessons);
}
</script>
