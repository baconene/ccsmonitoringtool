<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { 
  Users, 
  BookOpen, 
  Zap, 
  TrendingUp, 
  AlertCircle, 
  ChartBar,
  Clock,
  CheckCircle,
  BarChart3,
  Activity,
  Award,
  Calendar
} from 'lucide-vue-next';

// Get authenticated user from Inertia page props
const page = usePage();
const user = page.props.auth.user;

// Interfaces
interface AdminStats {
  totalUsers: number;
  totalInstructors: number;
  totalStudents: number;
  totalCourses: number;
  totalActivities: number;
  totalSchedules: number;
  activeEnrollments: number;
  completedActivities: number;
  pendingReviews: number;
  systemHealth: number;
}

interface CourseStats {
  id: number;
  title: string;
  studentCount: number;
  activityCount: number;
  instructorName: string;
  status: 'active' | 'draft' | 'archived';
}

interface RecentActivity {
  id: number;
  type: 'enrollment' | 'submission' | 'course_created' | 'user_added';
  description: string;
  timestamp: string;
  userInitials: string;
}

// Reactive state
const stats = ref<AdminStats>({
  totalUsers: 0,
  totalInstructors: 0,
  totalStudents: 0,
  totalCourses: 0,
  totalActivities: 0,
  totalSchedules: 0,
  activeEnrollments: 0,
  completedActivities: 0,
  pendingReviews: 0,
  systemHealth: 100
});

const courses = ref<CourseStats[]>([]);
const recentActivities = ref<RecentActivity[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

// Computed properties
const systemStatus = computed(() => {
  if (stats.value.systemHealth >= 90) return 'Excellent';
  if (stats.value.systemHealth >= 70) return 'Good';
  if (stats.value.systemHealth >= 50) return 'Fair';
  return 'Poor';
});

const systemStatusColor = computed(() => {
  if (stats.value.systemHealth >= 90) return 'text-green-600 dark:text-green-400';
  if (stats.value.systemHealth >= 70) return 'text-blue-600 dark:text-blue-400';
  if (stats.value.systemHealth >= 50) return 'text-yellow-600 dark:text-yellow-400';
  return 'text-red-600 dark:text-red-400';
});

const systemStatusBg = computed(() => {
  if (stats.value.systemHealth >= 90) return 'bg-green-100 dark:bg-green-900/30';
  if (stats.value.systemHealth >= 70) return 'bg-blue-100 dark:bg-blue-900/30';
  if (stats.value.systemHealth >= 50) return 'bg-yellow-100 dark:bg-yellow-900/30';
  return 'bg-red-100 dark:bg-red-900/30';
});

// Load admin data
const loadAdminData = async () => {
  try {
    loading.value = true;
    error.value = null;

    // Fetch admin stats
    const statsResponse = await axios.get('/api/dashboard/admin-stats');
    if (statsResponse.data && statsResponse.data.stats) {
      stats.value = statsResponse.data.stats;
    }

    // Fetch course statistics
    const coursesResponse = await axios.get('/api/dashboard/admin-courses');
    if (coursesResponse.data && coursesResponse.data.courses) {
      courses.value = coursesResponse.data.courses.slice(0, 10); // Top 10 courses
    }

    // Fetch recent activities
    const activitiesResponse = await axios.get('/api/dashboard/admin-activities');
    if (activitiesResponse.data && activitiesResponse.data.activities) {
      recentActivities.value = activitiesResponse.data.activities.slice(0, 8);
    }
  } catch (err: any) {
    console.error('Error loading admin dashboard:', err);
    error.value = err.response?.data?.message || 'Failed to load dashboard data';
  } finally {
    loading.value = false;
  }
};

// Navigate functions
const navigateToCourseManagement = () => {
  router.visit('/course-management');
};

const navigateToUserManagement = () => {
  router.visit('/role-management');
};

const navigateToStudentManagement = () => {
  router.visit('/student-management');
};

const navigateToAssessment = () => {
  router.visit('/assessment-tool');
};

const navigateToSchedule = () => {
  router.visit('/schedule');
};

// Load data on component mount
onMounted(() => {
  loadAdminData();
});

// Refresh function
const refreshData = () => {
  loadAdminData();
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4 sm:p-6 lg:p-8">
    <!-- Welcome Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
            Admin Dashboard
          </h1>
          <p class="text-gray-600 dark:text-gray-300 mt-2 text-sm sm:text-base">System Overview & Management Hub</p>
        </div>
        <button
          @click="refreshData"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors shadow-lg hover:shadow-xl"
        >
          Refresh
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-blue-600"></div>
      <span class="ml-4 text-gray-600 dark:text-gray-300 text-lg">Loading admin dashboard...</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50/80 dark:bg-red-900/20 backdrop-blur-sm border border-red-200 dark:border-red-700 rounded-xl p-6 mb-6">
      <div class="flex items-start gap-4">
        <AlertCircle class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" />
        <div class="flex-1">
          <h3 class="text-lg font-semibold text-red-800 dark:text-red-200">Error loading dashboard</h3>
          <p class="text-red-700 dark:text-red-300 mt-2">{{ error }}</p>
          <button 
            @click="loadAdminData" 
            class="mt-4 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium"
          >
            Try Again
          </button>
        </div>
      </div>
    </div>

    <!-- Dashboard Content -->
    <div v-else>
      <!-- System Health Card -->
      <div :class="[systemStatusBg, 'rounded-xl shadow-lg border border-blue-200/50 dark:border-blue-700/50 p-6 mb-8 transition-all']">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 dark:text-gray-300 text-sm font-medium">System Health</p>
            <h2 :class="['text-4xl font-bold mt-2', systemStatusColor]">{{ stats.systemHealth }}%</h2>
            <p :class="['mt-1 text-sm font-medium', systemStatusColor]">{{ systemStatus }}</p>
          </div>
          <div class="text-right">
            <BarChart3 :class="['w-16 h-16', systemStatusColor]" />
          </div>
        </div>
      </div>

      <!-- Main Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-blue-200/50 dark:border-blue-700/50 p-5 sm:p-6 transition-all hover:shadow-xl hover:scale-105">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Total Users</p>
              <p class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent mt-2">
                {{ stats.totalUsers }}
              </p>
              <div class="flex gap-2 mt-3">
                <span class="text-xs px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded">
                  {{ stats.totalInstructors }} Instructors
                </span>
                <span class="text-xs px-2 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded">
                  {{ stats.totalStudents }} Students
                </span>
              </div>
            </div>
            <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
              <Users class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
            </div>
          </div>
        </div>

        <!-- Total Courses -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-indigo-200/50 dark:border-indigo-700/50 p-5 sm:p-6 transition-all hover:shadow-xl hover:scale-105">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Active Courses</p>
              <p class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-indigo-600 to-indigo-800 bg-clip-text text-transparent mt-2">
                {{ stats.totalCourses }}
              </p>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
                {{ stats.activeEnrollments }} active enrollments
              </p>
            </div>
            <div class="p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg">
              <BookOpen class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
            </div>
          </div>
        </div>

        <!-- Total Activities -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-purple-200/50 dark:border-purple-700/50 p-5 sm:p-6 transition-all hover:shadow-xl hover:scale-105">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Total Activities</p>
              <p class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent mt-2">
                {{ stats.totalActivities }}
              </p>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
                {{ stats.completedActivities }} completed
              </p>
            </div>
            <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg">
              <Zap class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
            </div>
          </div>
        </div>

        <!-- Pending Reviews -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-orange-200/50 dark:border-orange-700/50 p-5 sm:p-6 transition-all hover:shadow-xl hover:scale-105">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Pending Reviews</p>
              <p class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-800 bg-clip-text text-transparent mt-2">
                {{ stats.pendingReviews }}
              </p>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
                Awaiting instructor action
              </p>
            </div>
            <div class="p-3 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg">
              <CheckCircle class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
            </div>
          </div>
        </div>
      </div>

      <!-- Secondary Stats Row -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-8">
        <!-- Schedules -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-teal-200/50 dark:border-teal-700/50 p-5 sm:p-6 transition-all hover:shadow-xl">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Total Schedules</h3>
            <Calendar class="w-5 h-5 text-teal-600 dark:text-teal-400" />
          </div>
          <p class="text-3xl font-bold text-teal-600 dark:text-teal-400">{{ stats.totalSchedules }}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">System-wide events</p>
        </div>

        <!-- Active Enrollments -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-rose-200/50 dark:border-rose-700/50 p-5 sm:p-6 transition-all hover:shadow-xl">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Active Enrollments</h3>
            <TrendingUp class="w-5 h-5 text-rose-600 dark:text-rose-400" />
          </div>
          <p class="text-3xl font-bold text-rose-600 dark:text-rose-400">{{ stats.activeEnrollments }}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Student-course links</p>
        </div>

        <!-- System Uptime -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-emerald-200/50 dark:border-emerald-700/50 p-5 sm:p-6 transition-all hover:shadow-xl">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Server Status</h3>
            <Activity class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
          </div>
          <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">99.9%</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Uptime this month</p>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 mb-8">
        <!-- Quick Actions -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-gray-200/50 dark:border-gray-700/50 p-6">
          <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Quick Actions</h2>
          <div class="space-y-3">
            <button
              @click="navigateToUserManagement"
              class="w-full flex items-center gap-3 px-4 py-3 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors border border-blue-200 dark:border-blue-700"
            >
              <Users class="w-5 h-5 text-blue-600 dark:text-blue-400" />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Manage Users</span>
            </button>
            <button
              @click="navigateToCourseManagement"
              class="w-full flex items-center gap-3 px-4 py-3 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-lg transition-colors border border-indigo-200 dark:border-indigo-700"
            >
              <BookOpen class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Course Management</span>
            </button>
            <button
              @click="navigateToStudentManagement"
              class="w-full flex items-center gap-3 px-4 py-3 bg-purple-50 dark:bg-purple-900/30 hover:bg-purple-100 dark:hover:bg-purple-900/50 rounded-lg transition-colors border border-purple-200 dark:border-purple-700"
            >
              <Award class="w-5 h-5 text-purple-600 dark:text-purple-400" />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Student Management</span>
            </button>
            <button
              @click="navigateToSchedule"
              class="w-full flex items-center gap-3 px-4 py-3 bg-teal-50 dark:bg-teal-900/30 hover:bg-teal-100 dark:hover:bg-teal-900/50 rounded-lg transition-colors border border-teal-200 dark:border-teal-700"
            >
              <Calendar class="w-5 h-5 text-teal-600 dark:text-teal-400" />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Schedule Management</span>
            </button>
            <button
              @click="navigateToAssessment"
              class="w-full flex items-center gap-3 px-4 py-3 bg-rose-50 dark:bg-rose-900/30 hover:bg-rose-100 dark:hover:bg-rose-900/50 rounded-lg transition-colors border border-rose-200 dark:border-rose-700"
            >
              <ChartBar class="w-5 h-5 text-rose-600 dark:text-rose-400" />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Assessment Tool</span>
            </button>
          </div>
        </div>

        <!-- Recent Activities -->
        <div class="lg:col-span-2 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-gray-200/50 dark:border-gray-700/50 p-6">
          <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Recent Activities</h2>
          <div class="space-y-4 max-h-80 overflow-y-auto">
            <div v-if="recentActivities.length === 0" class="text-center py-8">
              <p class="text-gray-500 dark:text-gray-400">No recent activities</p>
            </div>
            <div
              v-for="activity in recentActivities"
              :key="activity.id"
              class="flex items-start gap-4 pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0"
            >
              <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center flex-shrink-0">
                <span class="text-xs font-bold text-white">{{ activity.userInitials }}</span>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ activity.description }}</p>
                <div class="flex items-center gap-2 mt-1">
                  <Clock class="w-4 h-4 text-gray-400" />
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ activity.timestamp }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Courses Table -->
      <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-gray-200/50 dark:border-gray-700/50 p-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Top Active Courses</h2>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-200 dark:border-gray-700">
                <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300 text-sm">Course Name</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300 text-sm">Instructor</th>
                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-300 text-sm">Students</th>
                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-300 text-sm">Activities</th>
                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-300 text-sm">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="course in courses"
                :key="course.id"
                class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
              >
                <td class="py-3 px-4">
                  <p class="font-medium text-gray-800 dark:text-gray-200 text-sm truncate">{{ course.title }}</p>
                </td>
                <td class="py-3 px-4">
                  <p class="text-gray-600 dark:text-gray-400 text-sm">{{ course.instructorName }}</p>
                </td>
                <td class="py-3 px-4 text-center">
                  <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-sm font-medium">
                    {{ course.studentCount }}
                  </span>
                </td>
                <td class="py-3 px-4 text-center">
                  <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full text-sm font-medium">
                    {{ course.activityCount }}
                  </span>
                </td>
                <td class="py-3 px-4 text-center">
                  <span
                    :class="[
                      'px-3 py-1 rounded-full text-xs font-bold uppercase',
                      {
                        'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300': course.status === 'active',
                        'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300': course.status === 'draft',
                        'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300': course.status === 'archived'
                      }
                    ]"
                  >
                    {{ course.status }}
                  </span>
                </td>
              </tr>
              <tr v-if="courses.length === 0">
                <td colspan="5" class="py-8 text-center text-gray-500 dark:text-gray-400">
                  No courses found
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.flex-1 {
  min-height: 0;
}
</style>
