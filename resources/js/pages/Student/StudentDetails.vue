<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, onMounted, computed, watch } from 'vue';
import { ArrowLeft, Plus, Pencil, Trash2, X } from 'lucide-vue-next';
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
const activeTab = ref<'overview' | 'assessment' | 'skills' | 'grade-report'>('overview');
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

interface AssessmentDetail {
  id: number;
  student_id: number;
  course_id: number;
  assessed_by_user_id: number | null;
  description: string;
  course_mostyle: string | null;
  created_at: string;
  updated_at: string;
  course?: {
    id: number;
    title?: string | null;
    name?: string | null;
  };
  assessed_by?: {
    id: number;
    name?: string | null;
    email?: string | null;
  } | null;
}

type AssessmentModalMode = 'create' | 'view' | 'edit';

const assessmentDetails = ref<AssessmentDetail[]>([]);
const isAssessmentDetailsLoading = ref(false);
const assessmentDetailsError = ref<string | null>(null);
const assessmentPage = ref(1);
const showAssessmentModal = ref(false);
const assessmentModalMode = ref<AssessmentModalMode>('view');
const selectedAssessmentDetail = ref<AssessmentDetail | null>(null);
const isSavingAssessmentDetail = ref(false);
const isDeletingAssessmentDetail = ref(false);

const assessmentForm = ref({
  course_id: null as number | null,
  course_mostyle: '',
  description: '',
});

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

const ASSESSMENT_CARDS_PER_PAGE = 12;

const sortedAssessmentDetails = computed(() => {
  return [...assessmentDetails.value].sort((a, b) => {
    const aTime = new Date(a.created_at).getTime();
    const bTime = new Date(b.created_at).getTime();
    return bTime - aTime;
  });
});

const totalAssessmentPages = computed(() => {
  const total = sortedAssessmentDetails.value.length;
  return Math.max(1, Math.ceil(total / ASSESSMENT_CARDS_PER_PAGE));
});

const paginatedAssessmentCards = computed(() => {
  const start = (assessmentPage.value - 1) * ASSESSMENT_CARDS_PER_PAGE;
  const end = start + ASSESSMENT_CARDS_PER_PAGE;
  return sortedAssessmentDetails.value.slice(start, end);
});

const canGoToPreviousAssessmentPage = computed(() => assessmentPage.value > 1);
const canGoToNextAssessmentPage = computed(() => assessmentPage.value < totalAssessmentPages.value);

const nextAssessmentPage = () => {
  if (canGoToNextAssessmentPage.value) {
    assessmentPage.value += 1;
  }
};

const previousAssessmentPage = () => {
  if (canGoToPreviousAssessmentPage.value) {
    assessmentPage.value -= 1;
  }
};

watch(totalAssessmentPages, (totalPages) => {
  if (assessmentPage.value > totalPages) {
    assessmentPage.value = totalPages;
  }
});

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

const getCourseDisplayName = (detail: AssessmentDetail) => {
  return detail.course?.title || detail.course?.name || `Course #${detail.course_id}`;
};

const getAssessorDisplay = (detail: AssessmentDetail) => {
  if (detail.assessed_by?.name) {
    return `${detail.assessed_by.name} (#${detail.assessed_by.id})`;
  }

  if (detail.assessed_by_user_id) {
    return `User #${detail.assessed_by_user_id}`;
  }

  return 'Unknown assessor';
};

const resetAssessmentForm = () => {
  assessmentForm.value = {
    course_id: null,
    course_mostyle: '',
    description: '',
  };
};

const loadAssessmentDetails = async () => {
  if (!props.student.student_record_id) {
    assessmentDetailsError.value = 'No linked student record found for this user.';
    return;
  }

  isAssessmentDetailsLoading.value = true;
  assessmentDetailsError.value = null;

  try {
    const response = await axios.get(`/api/student/${props.student.student_record_id}/assessment-details`);
    assessmentDetails.value = response.data.assessments || [];
    assessmentPage.value = 1;
  } catch (error) {
    console.error('Failed to load assessment details:', error);
    assessmentDetailsError.value = 'Failed to load assessment details.';
  } finally {
    isAssessmentDetailsLoading.value = false;
  }
};

const openCreateAssessmentModal = () => {
  assessmentModalMode.value = 'create';
  selectedAssessmentDetail.value = null;
  resetAssessmentForm();
  showAssessmentModal.value = true;
};

const openAssessmentDetailModal = (detail: AssessmentDetail) => {
  assessmentModalMode.value = 'view';
  selectedAssessmentDetail.value = detail;
  assessmentForm.value = {
    course_id: detail.course_id,
    course_mostyle: detail.course_mostyle || '',
    description: detail.description,
  };
  showAssessmentModal.value = true;
};

const switchToEditMode = () => {
  assessmentModalMode.value = 'edit';
};

const closeAssessmentModal = () => {
  showAssessmentModal.value = false;
  selectedAssessmentDetail.value = null;
  resetAssessmentForm();
};

const saveAssessmentDetail = async () => {
  if (!props.student.student_record_id) {
    return;
  }

  if (!assessmentForm.value.course_id || !assessmentForm.value.description.trim()) {
    assessmentDetailsError.value = 'Course and description are required.';
    return;
  }

  isSavingAssessmentDetail.value = true;
  assessmentDetailsError.value = null;

  const payload = {
    course_id: assessmentForm.value.course_id,
    course_mostyle: assessmentForm.value.course_mostyle.trim() || null,
    description: assessmentForm.value.description.trim(),
  };

  try {
    if (assessmentModalMode.value === 'create') {
      await axios.post(`/api/student/${props.student.student_record_id}/assessment-details`, payload);
    } else if (assessmentModalMode.value === 'edit' && selectedAssessmentDetail.value) {
      await axios.put(
        `/api/student/${props.student.student_record_id}/assessment-details/${selectedAssessmentDetail.value.id}`,
        payload,
      );
    }

    await loadAssessmentDetails();
    closeAssessmentModal();
  } catch (error) {
    console.error('Failed to save assessment detail:', error);
    assessmentDetailsError.value = 'Failed to save assessment detail.';
  } finally {
    isSavingAssessmentDetail.value = false;
  }
};

const deleteAssessmentDetail = async () => {
  if (!props.student.student_record_id || !selectedAssessmentDetail.value) {
    return;
  }

  isDeletingAssessmentDetail.value = true;
  assessmentDetailsError.value = null;

  try {
    await axios.delete(
      `/api/student/${props.student.student_record_id}/assessment-details/${selectedAssessmentDetail.value.id}`,
    );

    await loadAssessmentDetails();
    closeAssessmentModal();
  } catch (error) {
    console.error('Failed to delete assessment detail:', error);
    assessmentDetailsError.value = 'Failed to delete assessment detail.';
  } finally {
    isDeletingAssessmentDetail.value = false;
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
  loadAssessmentDetails();

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
          <nav class="-mb-px flex flex-wrap gap-4">
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
              Assessment
            </button>
            <button
              @click="activeTab = 'skills'"
              :class="activeTab === 'skills'
                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
              class="whitespace-nowrap border-b-2 py-3 px-1 text-sm font-medium transition-colors"
            >
              Skills
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
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 sm:p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
              <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Assessments</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                  {{ sortedAssessmentDetails.length }} total entries
                </p>
              </div>
              <button
                @click="openCreateAssessmentModal"
                class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium"
                title="Add assessment"
              >
                <Plus class="w-4 h-4" />
                <span class="sm:hidden">New</span>
                <span class="hidden sm:inline">Add</span>
              </button>
            </div>

            <div v-if="isAssessmentDetailsLoading" class="text-sm text-gray-500 dark:text-gray-400">
              Loading assessment detail cards...
            </div>

            <div v-else-if="assessmentDetailsError" class="text-sm text-red-600 dark:text-red-400">
              {{ assessmentDetailsError }}
            </div>

            <div v-else-if="paginatedAssessmentCards.length > 0" class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
              <button
                v-for="detail in paginatedAssessmentCards"
                :key="detail.id"
                @click="openAssessmentDetailModal(detail)"
                class="text-left p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition-all bg-gray-50/60 dark:bg-gray-900/20 h-44 flex flex-col"
              >
                <div class="flex items-center justify-between gap-2">
                  <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ formatDate(detail.created_at) }}</p>
                  <span class="text-[11px] px-2 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 truncate max-w-32">
                    {{ getCourseDisplayName(detail) }}
                  </span>
                </div>
                <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-200 line-clamp-2">{{ detail.description }}</p>
                <p class="mt-2 text-[11px] text-gray-500 dark:text-gray-400 truncate">
                  Assessed by: {{ getAssessorDisplay(detail) }}
                </p>
                <p v-if="detail.course_mostyle" class="mt-auto pt-2 text-[11px] text-gray-500 dark:text-gray-400 line-clamp-1">
                  {{ detail.course_mostyle }}
                </p>
              </button>
              </div>

              <div v-if="totalAssessmentPages > 1" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  Showing {{ (assessmentPage - 1) * ASSESSMENT_CARDS_PER_PAGE + 1 }}
                  to {{ Math.min(assessmentPage * ASSESSMENT_CARDS_PER_PAGE, sortedAssessmentDetails.length) }}
                  of {{ sortedAssessmentDetails.length }}
                </p>

                <div class="flex items-center gap-2">
                  <button
                    @click="previousAssessmentPage"
                    :disabled="!canGoToPreviousAssessmentPage"
                    class="px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-200 disabled:opacity-50"
                  >
                    Previous
                  </button>
                  <span class="text-sm text-gray-600 dark:text-gray-300 min-w-16 text-center">
                    {{ assessmentPage }} / {{ totalAssessmentPages }}
                  </span>
                  <button
                    @click="nextAssessmentPage"
                    :disabled="!canGoToNextAssessmentPage"
                    class="px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-200 disabled:opacity-50"
                  >
                    Next
                  </button>
                </div>
              </div>
            </div>

            <div v-else class="text-sm text-gray-500 dark:text-gray-400">
              No assessment details yet. Click "Add" to create one.
            </div>
          </div>
        </div>

        <!-- Skills Tab -->
        <div v-else-if="activeTab === 'skills'" class="space-y-6">
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
        <div v-else-if="activeTab === 'grade-report'" class="space-y-6">
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

        <div
          v-if="showAssessmentModal"
          class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
        >
          <div class="w-full max-w-2xl bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                <span v-if="assessmentModalMode === 'create'">Add Assessment Detail</span>
                <span v-else-if="assessmentModalMode === 'edit'">Edit Assessment Detail</span>
                <span v-else>Assessment Detail</span>
              </h3>
              <button @click="closeAssessmentModal" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                <X class="w-5 h-5 text-gray-500" />
              </button>
            </div>

            <div class="p-6 space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Course</label>
                <select
                  v-model="assessmentForm.course_id"
                  :disabled="assessmentModalMode === 'view'"
                  class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                >
                  <option :value="null">Select course</option>
                  <option v-for="course in enrolledCourses" :key="course.id" :value="course.id">
                    {{ course.title }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Course Mostyle</label>
                <input
                  v-model="assessmentForm.course_mostyle"
                  :disabled="assessmentModalMode === 'view'"
                  type="text"
                  placeholder="e.g. Module-focused, Practical, Theory"
                  class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <textarea
                  v-model="assessmentForm.description"
                  :disabled="assessmentModalMode === 'view'"
                  rows="5"
                  class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                  placeholder="Enter assessment details..."
                ></textarea>
              </div>

              <div v-if="assessmentModalMode === 'view' && selectedAssessmentDetail" class="text-xs text-gray-500 dark:text-gray-400">
                Created: {{ formatDate(selectedAssessmentDetail.created_at) }}
              </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
              <div>
                <button
                  v-if="assessmentModalMode === 'view' && selectedAssessmentDetail"
                  @click="deleteAssessmentDetail"
                  :disabled="isDeletingAssessmentDetail"
                  class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium disabled:opacity-50"
                >
                  <Trash2 class="w-4 h-4" />
                  {{ isDeletingAssessmentDetail ? 'Deleting...' : 'Delete' }}
                </button>
              </div>

              <div class="flex items-center gap-2">
                <button
                  v-if="assessmentModalMode === 'view'"
                  @click="switchToEditMode"
                  class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-200"
                >
                  <Pencil class="w-4 h-4" />
                  Edit
                </button>
                <button
                  v-if="assessmentModalMode !== 'view'"
                  @click="saveAssessmentDetail"
                  :disabled="isSavingAssessmentDetail"
                  class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium disabled:opacity-50"
                >
                  {{ isSavingAssessmentDetail ? 'Saving...' : 'Save' }}
                </button>
                <button
                  @click="closeAssessmentModal"
                  class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-200"
                >
                  Close
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>