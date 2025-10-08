<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';

// TypeScript interfaces
interface EnrolledCourse {
  id: number;
  title: string;
  instructor: string;
  progress: number;
  nextClass: string;
}

interface Assignment {
  id: number;
  title: string;
  course: string;
  dueDate: string;
  status: 'pending' | 'completed';
  activityType: string;
}

interface Grade {
  course: string;
  assignment: string;
  score: number;
}

interface ScheduleItem {
  id: number;
  course: string;
  type: string;
  date: string;
  time: string;
  room: string;
}

// Get authenticated user from Inertia page props
const page = usePage();
const user = page.props.auth.user;

// Reactive state for student-specific data
const enrolledCourses = ref<EnrolledCourse[]>([]);
const assignments = ref<Assignment[]>([]);
const overdueActivities = ref<Assignment[]>([]);
const grades = ref<Grade[]>([]);
const schedule = ref<ScheduleItem[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

// Computed properties for student stats  
const totalCourses = computed(() => enrolledCourses.value.length);
const pendingAssignments = computed(() => assignments.value.filter(a => a.status === 'pending').length);

// Load real student data from API
const loadStudentData = async () => {
  loading.value = true;
  error.value = null;

  try {
    const response = await axios.get('/api/dashboard/student-data');
    const data = response.data;
    
    // Update reactive data with API response
    enrolledCourses.value = data.enrolledCourses || [];
    assignments.value = data.assignments || [];
    overdueActivities.value = data.overdueActivities || [];
    grades.value = data.grades || [];
    schedule.value = data.schedule || [];

  } catch (err: any) {
    error.value = 'Failed to load student data';
    console.error('Error loading student data:', err);
    
    // Fallback to empty arrays
    enrolledCourses.value = [];
    assignments.value = [];
    overdueActivities.value = [];
    grades.value = [];
    schedule.value = [];
  } finally {
    loading.value = false;
  }
};

// Load data on component mount
onMounted(() => {
  loadStudentData();
});

// Refresh data function
const refreshData = async () => {
  await loadStudentData();
};

// Expose refresh function for parent components
defineExpose({
  refreshData
});
</script>

<template>
  <div class="student-dashboard">
    <!-- Welcome Header -->
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
        Welcome back, {{ user?.name || 'Student' }}!
      </h1>
      <p class="text-gray-600 dark:text-gray-300 mt-2">Here's your academic progress and upcoming activities.</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 dark:border-blue-400"></div>
      <span class="ml-3 text-gray-600 dark:text-gray-300">Loading your dashboard...</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-6">
      <div class="flex">
        <svg class="w-5 h-5 text-red-400 dark:text-red-300 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Error loading dashboard</h3>
          <p class="text-sm text-red-700 dark:text-red-300 mt-1">{{ error }}</p>
          <button @click="refreshData" class="mt-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 font-medium">
            Try again
          </button>
        </div>
      </div>
    </div>



    <!-- Main Content Grid -->
    <div v-else class="grid grid-cols-1 xl:grid-cols-3 gap-6">
      <!-- Enrolled Courses -->
      <div class="xl:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-colors">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">My Courses</h2>
          <div class="space-y-4">
            <div
              v-for="course in enrolledCourses"
              :key="course.id"
              class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:shadow-md dark:hover:shadow-lg transition-shadow bg-gray-50 dark:bg-gray-700"
            >
              <div class="flex justify-between items-start mb-2">
                <div>
                  <h3 class="font-semibold text-lg text-gray-900 dark:text-white">{{ course.title }}</h3>
                  <p class="text-gray-600 dark:text-gray-300 text-sm">Instructor: {{ course.instructor }}</p>
                </div>
                <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">{{ course.progress }}% Complete</span>
              </div>
              
              <!-- Progress Bar -->
              <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mb-3">
                <div 
                  class="bg-blue-600 dark:bg-blue-400 h-2 rounded-full transition-all duration-300" 
                  :style="`width: ${course.progress}%`"
                ></div>
              </div>
              
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                  Next class: {{ course.nextClass }}
                </span>
                <button 
                  @click="router.visit(`/student/courses/${course.id}`)"
                  class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium transition-colors"
                >
                  View Course
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Assignments & Schedule -->
      <div class="xl:col-span-1 space-y-6">
        <!-- Overdue Activities -->
        <div v-if="overdueActivities.length > 0" class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-colors">
          <h2 class="text-xl font-semibold text-red-600 dark:text-red-400 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            Overdue Activities
          </h2>
          <div class="space-y-3">
            <div
              v-for="activity in overdueActivities"
              :key="activity.id"
              class="border-l-4 border-red-500 dark:border-red-400 pl-4 py-2 bg-red-50 dark:bg-red-900/20 rounded-r-md"
            >
              <div class="flex items-start justify-between mb-1 gap-2">
                <p class="font-medium text-gray-900 dark:text-white flex-1 min-w-0">{{ activity.title }}</p>
                <span class="px-2 py-1 text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200 rounded-full whitespace-nowrap flex-shrink-0">
                  {{ activity.activityType }}
                </span>
              </div>
              <p class="text-sm text-gray-600 dark:text-gray-300">{{ activity.course }}</p>
              <p class="text-sm text-red-600 dark:text-red-400 font-medium">Due: {{ activity.dueDate }}</p>
            </div>
          </div>
        </div>

        <!-- Pending Assignments -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-colors">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Pending Activities</h2>
          <div class="space-y-3">
            <div
              v-for="assignment in assignments.filter((a: Assignment) => a.status === 'pending')"
              :key="assignment.id"
              class="border-l-4 border-orange-500 dark:border-orange-400 pl-4 py-2"
            >
              <div class="flex items-start justify-between mb-1 gap-2">
                <p class="font-medium text-gray-900 dark:text-white flex-1 min-w-0">{{ assignment.title }}</p>
                <span class="px-2 py-1 text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200 rounded-full whitespace-nowrap flex-shrink-0">
                  {{ assignment.activityType }}
                </span>
              </div>
              <p class="text-sm text-gray-600 dark:text-gray-300">{{ assignment.course }}</p>
              <p class="text-sm text-orange-600 dark:text-orange-400">Due: {{ assignment.dueDate }}</p>
            </div>
            <div v-if="assignments.filter((a: Assignment) => a.status === 'pending').length === 0" class="text-gray-500 dark:text-gray-400 text-center py-4">
              No pending activities
            </div>
          </div>
        </div>

        <!-- Upcoming Schedule -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-colors">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Upcoming Classes</h2>
          <div class="space-y-3">
            <div
              v-for="item in schedule"
              :key="item.id"
              class="border-l-4 border-purple-500 dark:border-purple-400 pl-4 py-2"
            >
              <p class="font-medium text-gray-900 dark:text-white">{{ item.course }}</p>
              <p class="text-sm text-gray-600 dark:text-gray-300">{{ item.type }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ item.date }} at {{ item.time }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ item.room }}</p>
            </div>
            <div v-if="schedule.length === 0" class="text-gray-500 dark:text-gray-400 text-center py-4">
              No upcoming classes
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.student-dashboard {
  min-height: 0;
}
</style>