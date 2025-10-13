<script setup lang="ts">
import { BookOpen, Users, GraduationCap, FilePlus } from 'lucide-vue-next';

interface Course {
  students_count?: number;
}

interface Stats {
  total_courses_as_instructor: number;
  total_students_enrolled: number;
  total_courses_created: number;
}

interface Props {
  courses: Course[];
  stats: Stats;
}

const props = defineProps<Props>();
</script>

<template>
  <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-xl p-6 border border-purple-200 dark:border-purple-800">
    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">Quick Stats</h2>
    
    <div class="space-y-4">
      <!-- Total Courses as Instructor -->
      <div class="p-4 bg-gradient-to-br from-purple-100 to-indigo-100 dark:from-purple-900/30 dark:to-indigo-900/30 rounded-xl">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Courses Teaching</span>
          <GraduationCap class="w-5 h-5 text-purple-600 dark:text-purple-400" />
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
          {{ stats.total_courses_as_instructor }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
          Assigned as instructor
        </p>
      </div>

      <!-- Total Students Enrolled -->
      <div class="p-4 bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 rounded-xl">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Students</span>
          <Users class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
          {{ stats.total_students_enrolled }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
          Enrolled in your courses
        </p>
      </div>

      <!-- Total Courses Created -->
      <div class="p-4 bg-gradient-to-br from-teal-100 to-cyan-100 dark:from-teal-900/30 dark:to-cyan-900/30 rounded-xl">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Courses Created</span>
          <FilePlus class="w-5 h-5 text-teal-600 dark:text-teal-400" />
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
          {{ stats.total_courses_created }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
          Created by you
        </p>
      </div>
    </div>
  </div>
</template>
