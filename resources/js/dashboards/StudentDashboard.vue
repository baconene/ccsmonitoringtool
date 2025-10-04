<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

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
const grades = ref<Grade[]>([]);
const schedule = ref<ScheduleItem[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

// Computed properties for student stats
const totalCourses = computed(() => enrolledCourses.value.length);
const pendingAssignments = computed(() => assignments.value.filter(a => a.status === 'pending').length);
const averageGrade = computed(() => {
  if (grades.value.length === 0) return 0;
  const total = grades.value.reduce((sum, grade) => sum + grade.score, 0);
  return Math.round(total / grades.value.length);
});
const upcomingClasses = computed(() => schedule.value.filter(s => new Date(s.date) >= new Date()).length);

// Mock data for student dashboard
const loadStudentData = async () => {
  loading.value = true;
  error.value = null;

  try {
    // Simulate API calls with mock data
    await new Promise(resolve => setTimeout(resolve, 1000)); // Simulate loading
    
    // Mock enrolled courses
    enrolledCourses.value = [
      {
        id: 1,
        title: 'Mathematics 101',
        instructor: 'Dr. Smith',
        progress: 75,
        nextClass: '2025-10-05 10:00 AM'
      },
      {
        id: 2,
        title: 'Science Fundamentals',
        instructor: 'Prof. Johnson',
        progress: 60,
        nextClass: '2025-10-04 2:00 PM'
      },
      {
        id: 3,
        title: 'English Literature',
        instructor: 'Ms. Davis',
        progress: 85,
        nextClass: '2025-10-06 9:00 AM'
      }
    ];

    // Mock assignments
    assignments.value = [
      {
        id: 1,
        title: 'Math Homework Chapter 5',
        course: 'Mathematics 101',
        dueDate: '2025-10-08',
        status: 'pending'
      },
      {
        id: 2,
        title: 'Science Lab Report',
        course: 'Science Fundamentals',
        dueDate: '2025-10-10',
        status: 'pending'
      },
      {
        id: 3,
        title: 'Essay: Shakespeare Analysis',
        course: 'English Literature',
        dueDate: '2025-10-07',
        status: 'completed'
      }
    ];

    // Mock grades
    grades.value = [
      { course: 'Mathematics 101', assignment: 'Quiz 1', score: 88 },
      { course: 'Science Fundamentals', assignment: 'Lab 1', score: 92 },
      { course: 'English Literature', assignment: 'Essay 1', score: 85 }
    ];

    // Mock schedule
    schedule.value = [
      {
        id: 1,
        course: 'Mathematics 101',
        type: 'Lecture',
        date: '2025-10-05',
        time: '10:00 AM',
        room: 'Room 101'
      },
      {
        id: 2,
        course: 'Science Fundamentals',
        type: 'Lab',
        date: '2025-10-04',
        time: '2:00 PM',
        room: 'Lab 205'
      }
    ];

  } catch (err) {
    error.value = 'Failed to load student data';
    console.error('Error loading student data:', err);
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

    <!-- Stats Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <!-- Enrolled Courses -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-colors">
        <div class="flex items-center">
          <div class="p-3 bg-blue-100 dark:bg-blue-900/50 rounded-full">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Enrolled Courses</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ totalCourses }}</p>
          </div>
        </div>
      </div>

      <!-- Pending Assignments -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-colors">
        <div class="flex items-center">
          <div class="p-3 bg-orange-100 dark:bg-orange-900/50 rounded-full">
            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Pending Assignments</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ pendingAssignments }}</p>
          </div>
        </div>
      </div>

      <!-- Average Grade -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-colors">
        <div class="flex items-center">
          <div class="p-3 bg-green-100 dark:bg-green-900/50 rounded-full">
            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Average Grade</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ averageGrade }}%</p>
          </div>
        </div>
      </div>

      <!-- Upcoming Classes -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-colors">
        <div class="flex items-center">
          <div class="p-3 bg-purple-100 dark:bg-purple-900/50 rounded-full">
            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Upcoming Classes</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ upcomingClasses }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
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
                <button class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium transition-colors">
                  View Course
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Assignments & Schedule -->
      <div class="xl:col-span-1 space-y-6">
        <!-- Pending Assignments -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-colors">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Pending Assignments</h2>
          <div class="space-y-3">
            <div
              v-for="assignment in assignments.filter(a => a.status === 'pending')"
              :key="assignment.id"
              class="border-l-4 border-orange-500 dark:border-orange-400 pl-4 py-2"
            >
              <p class="font-medium text-gray-900 dark:text-white">{{ assignment.title }}</p>
              <p class="text-sm text-gray-600 dark:text-gray-300">{{ assignment.course }}</p>
              <p class="text-sm text-orange-600 dark:text-orange-400">Due: {{ assignment.dueDate }}</p>
            </div>
            <div v-if="pendingAssignments === 0" class="text-gray-500 dark:text-gray-400 text-center py-4">
              No pending assignments
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