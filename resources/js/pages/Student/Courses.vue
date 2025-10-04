<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';

// TypeScript interfaces
interface Course {
  id: number;
  title: string;
  description: string;
  instructor_name: string;
  progress: number;
  is_completed: boolean;
  enrolled_at: string;
  total_lessons: number;
  completed_lessons: number;
}

// Reactive state
const courses = ref<Course[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);
const searchQuery = ref('');

// Load student courses
const loadCourses = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    // Simulate API call with mock data
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    courses.value = [
      {
        id: 1,
        title: 'Introduction to Mathematics',
        description: 'Basic mathematical concepts and problem-solving techniques.',
        instructor_name: 'Dr. Smith',
        progress: 75,
        is_completed: false,
        enrolled_at: '2025-09-01',
        total_lessons: 12,
        completed_lessons: 9
      },
      {
        id: 2,
        title: 'Science Fundamentals',
        description: 'Exploring the basics of physics, chemistry, and biology.',
        instructor_name: 'Prof. Johnson',
        progress: 60,
        is_completed: false,
        enrolled_at: '2025-09-01',
        total_lessons: 15,
        completed_lessons: 9
      },
      {
        id: 3,
        title: 'English Literature',
        description: 'Study of classic and contemporary literary works.',
        instructor_name: 'Ms. Davis',
        progress: 100,
        is_completed: true,
        enrolled_at: '2025-08-15',
        total_lessons: 10,
        completed_lessons: 10
      },
      {
        id: 4,
        title: 'History and Culture',
        description: 'Understanding world history and cultural developments.',
        instructor_name: 'Dr. Wilson',
        progress: 40,
        is_completed: false,
        enrolled_at: '2025-09-10',
        total_lessons: 20,
        completed_lessons: 8
      }
    ];
    
  } catch (err) {
    error.value = 'Failed to load courses';
    console.error('Error:', err);
  } finally {
    loading.value = false;
  }
};

// Computed properties
const filteredCourses = computed(() => {
  if (!searchQuery.value) return courses.value;
  
  return courses.value.filter(course =>
    course.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    course.instructor_name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const completedCoursesCount = computed(() => 
  courses.value.filter(course => course.is_completed).length
);

const inProgressCoursesCount = computed(() => 
  courses.value.filter(course => !course.is_completed).length
);

const averageProgress = computed(() => {
  if (courses.value.length === 0) return 0;
  const total = courses.value.reduce((sum, course) => sum + course.progress, 0);
  return Math.round(total / courses.value.length);
});

// Load courses on mount
onMounted(() => {
  loadCourses();
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

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 dark:border-blue-400"></div>
          <span class="ml-3 text-gray-600 dark:text-gray-300">Loading your courses...</span>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-6">
          <div class="flex">
            <svg class="w-5 h-5 text-red-400 dark:text-red-300 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Error</h3>
              <p class="text-sm text-red-700 dark:text-red-300 mt-1">{{ error }}</p>
              <button @click="loadCourses" class="mt-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 font-medium">
                Try again
              </button>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div v-else>
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

                <!-- Lesson Stats -->
                <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-6">
                  <span>{{ course.completed_lessons }} / {{ course.total_lessons }} lessons</span>
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
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>