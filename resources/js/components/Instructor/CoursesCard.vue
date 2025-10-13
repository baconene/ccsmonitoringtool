<script setup lang="ts">
import { BookOpen, Users } from 'lucide-vue-next';

interface Course {
  id: number;
  title: string;
  description: string | null;
  students_count?: number;
}

interface Props {
  courses: Course[];
}

defineProps<Props>();
</script>

<template>
  <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-xl p-8 border border-purple-200 dark:border-purple-800">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-3">
      <div class="p-2 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-lg">
        <BookOpen class="w-6 h-6 text-white" />
      </div>
      Courses Taught
    </h2>

    <div v-if="courses && courses.length > 0" class="space-y-4">
      <div
        v-for="course in courses"
        :key="course.id"
        class="p-6 bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-xl border border-purple-200 dark:border-purple-800 hover:shadow-lg transition-all duration-200"
      >
        <div class="flex justify-between items-start mb-2">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ course.title }}
          </h3>
          <div v-if="course.students_count !== undefined" class="flex items-center gap-2 px-3 py-1 bg-white dark:bg-gray-800 rounded-full">
            <Users class="w-4 h-4 text-purple-600 dark:text-purple-400" />
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
              {{ course.students_count }}
            </span>
          </div>
        </div>
        <p v-if="course.description" class="text-gray-600 dark:text-gray-400 text-sm">
          {{ course.description }}
        </p>
      </div>
    </div>

    <div v-else class="text-center py-8">
      <BookOpen class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-3" />
      <p class="text-gray-500 dark:text-gray-400">No courses assigned yet</p>
    </div>
  </div>
</template>
