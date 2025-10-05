<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// TypeScript interfaces
interface Course {
  id: number;
  title: string;
  description: string;
  instructor_name: string;
  progress: number;
  is_completed: boolean;
  enrolled_at: string;
  total_modules?: number;
  completed_modules?: number;
  total_lessons?: number;
  completed_lessons?: number;
}

interface Props {
  courses: Course[];
  stats?: {
    total_courses: number;
    completed_courses: number;
    in_progress: number;
    total_hours: number;
  };
}

const props = defineProps<Props>();

const searchQuery = ref('');

// Computed properties
const filteredCourses = computed(() => {
  if (!searchQuery.value) return props.courses;
  
  return props.courses.filter((course: Course) =>
    course.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    course.instructor_name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const completedCoursesCount = computed(() => 
  props.stats?.completed_courses ?? props.courses.filter((course: Course) => course.is_completed).length
);

const inProgressCoursesCount = computed(() => 
  props.stats?.in_progress ?? props.courses.filter((course: Course) => !course.is_completed).length
);

const averageProgress = computed(() => {
  if (props.courses.length === 0) return 0;
  const total = props.courses.reduce((sum: number, course: Course) => sum + course.progress, 0);
  return Math.round(total / props.courses.length);
});

// Get progress bar color based on percentage
const getProgressColor = (progress: number) => {
  if (progress >= 80) return 'bg-green-500';
  if (progress >= 60) return 'bg-blue-500';
  if (progress >= 40) return 'bg-yellow-500';
  return 'bg-red-500';
};

// Get status badge color
const getStatusColor = (isCompleted: boolean) => {
  return isCompleted 
    ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200'
    : 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200';
};
</script>

<template>
  <Head title="My Courses" />

  <AppLayout>
    <div class="p-6 min-h-screen bg-gray-50 dark:bg-gray-900">
      <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Courses</h1>
          <p class="text-gray-600 dark:text-gray-300 mt-2">Track your learning progress and access course materials.</p>
        </div>

        <!-- Content -->
        <div>
          <!-- Stats Grid -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Courses -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6">
              <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/50 rounded-full">
                  <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Courses</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ courses.length }}</p>
                </div>
              </div>
            </div>

            <!-- Completed Courses -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6">
              <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900/50 rounded-full">
                  <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Completed</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ completedCoursesCount }}</p>
                </div>
              </div>
            </div>

            <!-- Average Progress -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6">
              <div class="flex items-center">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/50 rounded-full">
                  <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Average Progress</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ averageProgress }}%</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Search Bar -->
          <div class="mb-6">
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
              <input 
                v-model="searchQuery"
                type="text" 
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Search courses by title or instructor..."
              >
            </div>
          </div>

          <!-- Courses Grid -->
          <div v-if="filteredCourses.length > 0" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            <div
              v-for="course in filteredCourses"
              :key="course.id"
              class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow"
            >
              <!-- Course Header -->
              <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                  <Link :href="`/student/courses/${course.id}`" class="flex-1 mr-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white line-clamp-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors cursor-pointer">
                      {{ course.title }}
                    </h3>
                  </Link>
                  <span :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(course.is_completed)}`">
                    {{ course.is_completed ? 'Completed' : 'In Progress' }}
                  </span>
                </div>
                
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-2">
                  {{ course.description }}
                </p>

                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  {{ course.instructor_name }}
                </div>

                <!-- Progress Bar -->
                <div class="mb-4">
                  <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ course.progress }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div 
                      :class="`h-2 rounded-full transition-all duration-300 ${getProgressColor(course.progress)}`"
                      :style="`width: ${course.progress}%`"
                    ></div>
                  </div>
                </div>

                <!-- Module Stats -->
                <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-6">
                  <span>{{ course.completed_modules || 0 }} / {{ course.total_modules || 0 }} modules</span>
                  <span>Enrolled: {{ new Date(course.enrolled_at).toLocaleDateString() }}</span>
                </div>

                <!-- Action Button -->
                <Link 
                  :href="`/student/courses/${course.id}`"
                  class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                >
                  {{ course.is_completed ? 'Review Course' : 'Continue Learning' }}
                </Link>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No courses found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              {{ searchQuery ? 'Try adjusting your search criteria.' : 'You are not enrolled in any courses yet.' }}
            </p>
          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>