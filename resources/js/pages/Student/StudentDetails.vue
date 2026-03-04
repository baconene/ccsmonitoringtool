<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, onMounted, computed } from 'vue';
import { ArrowLeft } from 'lucide-vue-next';
import axios from 'axios';
import RadarChart from '@/components/Charts/RadarChart.vue';
import BarChart from '@/components/Charts/BarChart.vue';
import { type BreadcrumbItem } from '@/types';

const props = defineProps<{
  student: {
    id: number;
    student_record_id?: number | null;
    name: string;
    email: string;
    role_name: string;
    role_display_name?: string;
    grade_level?: string;
    section?: string;
  };
  enrolledCourses: Array<{
    id: number;
    title: string;
    progress: number;
    total_lessons: number;
    completed_lessons: number;
    last_activity?: string;
  }>;
  gradeReportCourses?: Array<{
    id: number;
    title: string;
  }>;
  selectedGradeCourseId?: number | null;
  initialGradeReport?: any;
}>();

// Get return URL from query params
const urlParams = new URLSearchParams(window.location.search);
const returnUrl = urlParams.get('returnUrl') || '/role-management';

// Determine breadcrumb based on return URL
const breadcrumbItems = computed<BreadcrumbItem[]>(() => {
  const items: BreadcrumbItem[] = [{ title: 'Home', href: '/' }];
  
  if (returnUrl.includes('/student-management')) {
    items.push({ title: 'Student Management', href: returnUrl });
  } else {
    items.push({ title: 'User Management', href: '/role-management' });
  }
  
  items.push({ title: 'Student Details', href: `/student/${props.student.id}/details` });
  
  return items;
});

const loading = ref(true);
const activeTab = ref<'overview' | 'assessment' | 'grade-report'>('overview');
const isAssessmentLoading = ref(false);
const isGradeReportLoading = ref(false);
const selectedGradeCourseId = ref<number | null>(props.selectedGradeCourseId ?? null);
const gradeReportData = ref<any>(props.initialGradeReport ?? null);
const gradeReportError = ref<string | null>(null);

interface Assessment {
  student_id: number
  student_name: string
  overall_score: number
  readiness_level: string
  assessment_date: string
  courses: any[]
  strengths: any[]
  weaknesses: any[]
  radar_chart: {
    labels: string[]
    datasets: any[]
  }
  summary: {
    total_courses: number
    strengths_count: number
    weaknesses_count: number
    average_skill_score: number
  }
}

const assessment = ref<Assessment | null>(null);
const assessmentError = ref<string | null>(null);

const weaknessChartLabels = computed(() =>
  (assessment.value?.weaknesses || []).map((w: any) => w.skill_name)
);

const weaknessChartDatasets = computed(() => [
  {
    label: 'Skill Score',
    data: (assessment.value?.weaknesses || []).map((w: any) => w.score),
    borderColor: 'rgba(249, 115, 22, 1)',
    backgroundColor: 'rgba(249, 115, 22, 0.3)',
    borderWidth: 1,
  },
]);

const loadAssessment = async () => {
  if (!props.student.student_record_id) {
    assessmentError.value = 'No linked student record found for this user.';
    return;
  }

  isAssessmentLoading.value = true;
  assessmentError.value = null;

  try {
    const response = await axios.get(`/api/admin/student/${props.student.student_record_id}/assessment`);
    assessment.value = response.data;
  } catch (error) {
    console.error('Failed to load student assessment:', error);
    assessmentError.value = 'Failed to load student assessment data.';
  } finally {
    isAssessmentLoading.value = false;
  }
};

const loadGradeReport = async (courseId: number | null) => {
  if (!courseId) {
    gradeReportData.value = null;
    gradeReportError.value = 'No course available for grade report.';
    return;
  }

  isGradeReportLoading.value = true;
  gradeReportError.value = null;

  try {
    const response = await axios.get(`/api/admin/student/${props.student.id}/grade-report/${courseId}`);
    gradeReportData.value = response.data;
  } catch (error) {
    console.error('Failed to load grade report:', error);
    gradeReportError.value = 'Failed to load grade report for selected course.';
  } finally {
    isGradeReportLoading.value = false;
  }
};

const goBack = () => {
  router.visit(returnUrl);
};

onMounted(() => {
  loadAssessment();

  if (!gradeReportData.value && selectedGradeCourseId.value) {
    loadGradeReport(selectedGradeCourseId.value);
  }

  loading.value = false;
});

const getReadinessLevelColor = (level: string) => {
  switch (level) {
    case 'Advanced':
      return 'bg-gradient-to-br from-green-500 to-emerald-600';
    case 'Proficient':
      return 'bg-gradient-to-br from-blue-500 to-blue-600';
    case 'Developing':
      return 'bg-gradient-to-br from-orange-500 to-orange-600';
    case 'Not Ready':
      return 'bg-gradient-to-br from-red-500 to-red-600';
    default:
      return 'bg-gray-500';
  }
};

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const getStatusClass = (status: string) => {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
    case 'in_progress':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300';
    case 'not_started':
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300';
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300';
  }
};
</script>

<template>
  <Head :title="`Student Details - ${student.name}`" />

  <AppLayout :breadcrumbs="breadcrumbItems">
    <div class="p-6 min-h-screen bg-gray-50 dark:bg-gray-900">
      <div class="max-w-7xl mx-auto">
        <!-- Page Header with Back Button -->
        <div class="mb-6">
          <!-- Page Title with Back Button -->
          <div class="flex items-center space-x-4">
            <button
              @click="goBack"
              class="p-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              title="Go back"
            >
              <ArrowLeft class="w-5 h-5 text-gray-600 dark:text-gray-400" />
            </button>
            <div>
              <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Student Details</h1>
              <p class="text-gray-600 dark:text-gray-400 mt-1">View student information and course progress</p>
            </div>
          </div>
        </div>

        <!-- Student Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
          <div class="flex items-start justify-between">
            <div class="flex items-center">
              <div class="h-20 w-20 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                <span class="text-white text-3xl font-semibold">{{ student.name[0].toUpperCase() }}</span>
              </div>
              <div class="ml-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ student.name }}</h2>
                <p class="text-gray-600 dark:text-gray-300 mt-1">{{ student.email }}</p>
                <div class="flex items-center space-x-2 mt-3">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200">
                    {{ student.role_display_name || 'Student' }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Additional Student Information -->
          <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Student ID</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">#{{ student.id }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Grade Level</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                  {{ student.grade_level || 'Not specified' }}
                </p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Section</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                  {{ student.section || 'Not assigned' }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
          <nav class="-mb-px flex space-x-6">
            <button
              @click="activeTab = 'overview'"
              :class="activeTab === 'overview'
                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
              class="whitespace-nowrap border-b-2 py-3 px-1 text-sm font-medium transition-colors"
            >
              Overview & Course Progress
            </button>
            <button
              @click="activeTab = 'assessment'"
              :class="activeTab === 'assessment'
                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
              class="whitespace-nowrap border-b-2 py-3 px-1 text-sm font-medium transition-colors"
            >
              Assessment & Skills
            </button>
            <button
              @click="activeTab = 'grade-report'"
              :class="activeTab === 'grade-report'
                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
              class="whitespace-nowrap border-b-2 py-3 px-1 text-sm font-medium transition-colors"
            >
              Grade Report
            </button>
          </nav>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 dark:border-blue-400"></div>
        </div>

        <!-- Overview Tab -->
        <div v-else-if="activeTab === 'overview'">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Enrolled Courses</h2>
          
          <div v-if="enrolledCourses.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
              v-for="course in enrolledCourses"
              :key="course.id"
              class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700"
            >
              <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ course.title }}</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                  {{ course.completed_lessons }}/{{ course.total_lessons }} lessons
                </span>
              </div>

              <!-- Progress Bar -->
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mb-4">
                <div
                  class="bg-blue-600 dark:bg-blue-500 h-2.5 rounded-full"
                  :style="{ width: `${course.progress}%` }"
                ></div>
              </div>

              <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600 dark:text-gray-300">
                  {{ course.progress }}% Complete
                </span>
                <span v-if="course.last_activity" class="text-gray-500 dark:text-gray-400">
                  Last activity: {{ course.last_activity }}
                </span>
              </div>
            </div>
          </div>

          <!-- No Courses Message -->
          <div
            v-else
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 text-center"
          >
            <p class="text-gray-600 dark:text-gray-300">This student is not enrolled in any courses.</p>
          </div>
        </div>

        <!-- Assessment Tab -->
        <div v-else-if="activeTab === 'assessment'" class="space-y-6">
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Assessment Overview</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              Student Grade: <span class="font-semibold">{{ student.grade_level || 'Not specified' }}</span>
            </p>
          </div>

          <div v-if="isAssessmentLoading" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-10 text-center">
            <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 animate-spin">
              <span class="sr-only">Loading</span>
            </div>
            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Loading assessment data...</p>
          </div>

          <div v-else-if="assessmentError" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <p class="text-sm text-red-700 dark:text-red-300">{{ assessmentError }}</p>
          </div>

          <template v-else-if="assessment">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Overall Score</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ assessment.overall_score }}%</p>
              </div>
              <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Readiness</p>
                <span :class="getReadinessLevelColor(assessment.readiness_level)" class="inline-flex mt-2 px-3 py-1 rounded-full text-xs font-semibold text-white">
                  {{ assessment.readiness_level }}
                </span>
              </div>
              <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Strengths</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ assessment.summary.strengths_count }}</p>
              </div>
              <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Weaknesses</p>
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ assessment.summary.weaknesses_count }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Skill Radar</h3>
                <div v-if="assessment.radar_chart.labels && assessment.radar_chart.labels.length > 0" class="h-80 flex items-center justify-center">
                  <RadarChart :labels="assessment.radar_chart.labels" :datasets="assessment.radar_chart.datasets" />
                </div>
                <div v-else class="h-80 flex items-center justify-center text-sm text-gray-500 dark:text-gray-400">
                  No radar data available
                </div>
              </div>

              <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Areas for Improvement</h3>
                <div v-if="assessment.weaknesses && assessment.weaknesses.length > 0" class="h-80">
                  <BarChart :labels="weaknessChartLabels" :datasets="weaknessChartDatasets" />
                </div>
                <div v-else class="h-80 flex items-center justify-center text-sm text-gray-500 dark:text-gray-400">
                  No weaknesses identified
                </div>
              </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Course Assessment Progress</h3>
              <div v-if="assessment.courses && assessment.courses.length > 0" class="space-y-4">
                <div v-for="course in assessment.courses" :key="course.course_id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                  <div class="flex items-center justify-between mb-3">
                    <p class="font-medium text-gray-900 dark:text-white">{{ course.course_name }}</p>
                    <p class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ course.score }}%</p>
                  </div>
                  <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-blue-600 dark:bg-blue-500 h-2.5 rounded-full" :style="{ width: `${course.score}%` }"></div>
                  </div>
                </div>
              </div>
              <p v-else class="text-sm text-gray-500 dark:text-gray-400">No course assessment progress yet.</p>
              <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                Last updated: {{ formatDate(assessment.assessment_date) }}
              </p>
            </div>
          </template>
        </div>

        <!-- Grade Report Tab -->
        <div v-else class="space-y-6">
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
              <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Student Grade Report</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View grade report for the selected course.</p>
              </div>
              <div class="w-full md:w-80">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Course</label>
                <select
                  v-model="selectedGradeCourseId"
                  @change="loadGradeReport(selectedGradeCourseId)"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100"
                >
                  <option :value="null">Select course</option>
                  <option v-for="course in (gradeReportCourses || [])" :key="course.id" :value="course.id">
                    {{ course.title }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <div v-if="isGradeReportLoading" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-10 text-center border border-gray-200 dark:border-gray-700">
            <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 animate-spin"></div>
            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Loading grade report...</p>
          </div>

          <div v-else-if="gradeReportError" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <p class="text-sm text-red-700 dark:text-red-300">{{ gradeReportError }}</p>
          </div>

          <template v-else-if="gradeReportData">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Course Grade</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ gradeReportData.overall_grade ?? 0 }}%</p>
              </div>
              <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Letter Grade</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ gradeReportData.overall_letter_grade || 'N/A' }}</p>
              </div>
              <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Activities Completed</p>
                <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ gradeReportData.activity_summary?.completed || 0 }}</p>
              </div>
              <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                <span :class="getStatusClass(gradeReportData.completion_status)" class="inline-flex mt-2 px-3 py-1 rounded-full text-xs font-semibold">
                  {{ (gradeReportData.completion_status || 'unknown').replace('_', ' ').toUpperCase() }}
                </span>
              </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Module Performance</h3>
              <div v-if="gradeReportData.modules && gradeReportData.modules.length > 0" class="space-y-3">
                <div
                  v-for="module in gradeReportData.modules"
                  :key="module.module_id"
                  class="border border-gray-200 dark:border-gray-700 rounded-lg p-4"
                >
                  <div class="flex justify-between items-center mb-2">
                    <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ module.module_title }}</h4>
                    <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ module.module_score || 0 }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mb-2">
                    <div class="bg-blue-600 dark:bg-blue-500 h-2.5 rounded-full" :style="{ width: `${module.module_score || 0}%` }"></div>
                  </div>
                  <span :class="getStatusClass(module.completion_status)" class="inline-flex px-2 py-1 rounded-full text-xs font-semibold">
                    {{ (module.completion_status || 'unknown').replace('_', ' ').toUpperCase() }}
                  </span>
                </div>
              </div>
              <p v-else class="text-sm text-gray-500 dark:text-gray-400">No module grades available yet.</p>
            </div>
          </template>
        </div>
      </div>
    </div>
  </AppLayout>
</template>