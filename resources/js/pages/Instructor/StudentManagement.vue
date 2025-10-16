<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { 
  Users, 
  Search, 
  Filter, 
  Download, 
  Eye, 
  BookOpen,
  TrendingUp,
  Award,
  GraduationCap,
  BarChart3,
  FileText,
  ChevronDown,
  RefreshCw,
  X
} from 'lucide-vue-next';
import axios from 'axios';

interface Course {
  id: number;
  name: string;
  title: string;
  total_students: number;
}

interface GradeLevel {
  id: number;
  name: string;
  display_name: string;
}

interface Student {
  id: number;
  user_id: number;
  student_id_text: string;
  name: string;
  email: string;
  section: string | null;
  grade_level: {
    id: number;
    name: string;
    display_name: string;
  } | null;
  enrolled_at: string;
  course_progress: number;
  is_completed: boolean;
  total_activities: number;
  completed_activities: number;
  submitted_activities: number;
  pending_activities: number;
  average_grade: number | null;
}

interface Statistics {
  total_students: number;
  total_enrollments: number;
  completed_enrollments: number;
  average_progress: number;
  grade_level_distribution: { display_name: string; count: number }[];
}

interface Props {
  courses: Course[];
  gradeLevels: GradeLevel[];
}

const props = defineProps<Props>();

// State
const selectedCourse = ref<Course | null>(null);
const students = ref<Student[]>([]);
const statistics = ref<Statistics | null>(null);
const loading = ref(false);
const searchQuery = ref('');
const selectedGradeLevel = ref<number | null>(null);
const selectedSection = ref<string | null>(null);
const sortBy = ref('name');
const sortOrder = ref<'asc' | 'desc'>('asc');
const showFilters = ref(false);
const isExporting = ref(false);
const courseSearchQuery = ref('');
const showCourseDropdown = ref(false);

// Computed
const filteredCourses = computed(() => {
  if (!courseSearchQuery.value) return props.courses;
  
  const query = courseSearchQuery.value.toLowerCase();
  return props.courses.filter(course => 
    course.title.toLowerCase().includes(query) || 
    course.name.toLowerCase().includes(query)
  );
});

const availableSections = computed(() => {
  const sections = new Set<string>();
  students.value.forEach(student => {
    if (student.section) {
      sections.add(student.section);
    }
  });
  return Array.from(sections).sort();
});

const filteredStudents = computed(() => {
  return students.value;
});

const statsCards = computed(() => {
  if (!statistics.value) return [];
  
  return [
    {
      title: 'Total Students',
      value: statistics.value.total_students,
      icon: Users,
      color: 'blue',
      trend: null,
    },
    {
      title: 'Total Enrollments',
      value: statistics.value.total_enrollments,
      icon: BookOpen,
      color: 'green',
      trend: null,
    },
    {
      title: 'Completed',
      value: statistics.value.completed_enrollments,
      icon: Award,
      color: 'purple',
      trend: null,
    },
    {
      title: 'Avg Progress',
      value: `${statistics.value.average_progress}%`,
      icon: TrendingUp,
      color: 'orange',
      trend: null,
    },
  ];
});

// Methods
const fetchStatistics = async () => {
  try {
    const response = await axios.get('/student-management/statistics');
    statistics.value = response.data;
  } catch (error) {
    console.error('Failed to fetch statistics:', error);
  }
};

const selectCourse = async (course: Course) => {
  selectedCourse.value = course;
  showCourseDropdown.value = false;
  courseSearchQuery.value = '';
  loading.value = true;
  
  try {
    const response = await axios.get(`/student-management/course/${course.id}/students`, {
      params: {
        search: searchQuery.value,
        grade_level: selectedGradeLevel.value,
        section: selectedSection.value,
        sort_by: sortBy.value,
        sort_order: sortOrder.value,
      }
    });
    
    students.value = response.data.students;
  } catch (error) {
    console.error('Failed to fetch students:', error);
  } finally {
    loading.value = false;
  }
};

const clearFilters = () => {
  searchQuery.value = '';
  selectedGradeLevel.value = null;
  selectedSection.value = null;
  refreshStudents();
};

const refreshStudents = () => {
  if (selectedCourse.value) {
    selectCourse(selectedCourse.value);
  }
};

const viewStudentProfile = (userId: number) => {
  // Navigate to existing student profile page (using user_id, not student.id)
  // Pass return URL to preserve Student Management state
  const returnUrl = window.location.pathname + window.location.search;
  router.visit(`/student/${userId}/details?returnUrl=${encodeURIComponent(returnUrl)}`);
};

const viewStudentActivities = async (student: Student) => {
  if (!selectedCourse.value) return;
  
  try {
    const response = await axios.get(
      `/student-management/course/${selectedCourse.value.id}/student/${student.id}/activities`
    );
    
    // Show modal with activity details
    console.log('Student activities:', response.data);
    // You can implement a modal here to show the activities
  } catch (error) {
    console.error('Failed to fetch student activities:', error);
  }
};

const exportReport = async (format: 'csv' | 'excel' = 'csv') => {
  if (!selectedCourse.value) return;
  
  isExporting.value = true;
  
  try {
    const response = await axios.get(
      `/student-management/course/${selectedCourse.value.id}/export`,
      {
        params: {
          format,
          grade_level: selectedGradeLevel.value,
          section: selectedSection.value,
        },
        responseType: 'blob',
      }
    );
    
    // Create download link
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `student_report_${selectedCourse.value.name}_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    link.remove();
  } catch (error) {
    console.error('Failed to export report:', error);
  } finally {
    isExporting.value = false;
  }
};

const toggleSort = (field: string) => {
  if (sortBy.value === field) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortBy.value = field;
    sortOrder.value = 'asc';
  }
  refreshStudents();
};

const getProgressColor = (progress: number) => {
  if (progress >= 80) return 'bg-green-500';
  if (progress >= 50) return 'bg-blue-500';
  if (progress >= 25) return 'bg-yellow-500';
  return 'bg-red-500';
};

const getGradeColor = (grade: number) => {
  if (grade >= 90) return 'text-green-600 dark:text-green-400';
  if (grade >= 80) return 'text-blue-600 dark:text-blue-400';
  if (grade >= 70) return 'text-yellow-600 dark:text-yellow-400';
  return 'text-red-600 dark:text-red-400';
};

const handleCourseDropdownBlur = () => {
  const timeoutId = window.setTimeout(() => {
    showCourseDropdown.value = false;
  }, 200);
  return timeoutId;
};

// Lifecycle
onMounted(() => {
  fetchStatistics();
});
</script>

<template>
  <AppLayout>
    <Head title="Student Management" />
    
    <div class="py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center gap-3 mb-2">
                <Users class="w-8 h-8 text-indigo-600" />
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                  Student Management
                </h1>
              </div>
              <p class="text-gray-600 dark:text-gray-400">
                Monitor and track your students' progress across all courses
              </p>
            </div>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div v-if="statistics" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div
            v-for="stat in statsCards"
            :key="stat.title"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
                  {{ stat.title }}
                </p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                  {{ stat.value }}
                </p>
              </div>
              <div :class="`p-3 rounded-lg bg-${stat.color}-100 dark:bg-${stat.color}-900/30`">
                <component :is="stat.icon" :class="`w-6 h-6 text-${stat.color}-600 dark:text-${stat.color}-400`" />
              </div>
            </div>
          </div>
        </div>

        <!-- Course Selection -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
          <div class="flex items-center gap-3 mb-4">
            <BookOpen class="w-5 h-5 text-gray-600 dark:text-gray-400" />
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
              Select a Course
            </h2>
          </div>
          
          <!-- Searchable Dropdown -->
          <div class="relative max-w-md">
            <div class="relative">
              <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none z-10" />
              <input
                v-model="courseSearchQuery"
                @focus="showCourseDropdown = true"
                @blur="handleCourseDropdownBlur"
                type="text"
                :placeholder="selectedCourse ? selectedCourse.title : 'Search and select a course...'"
                class="w-full pl-10 pr-10 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100"
              />
              <ChevronDown 
                :class="['absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 transition-transform pointer-events-none', showCourseDropdown ? 'rotate-180' : '']" 
              />
            </div>
            
            <!-- Dropdown Menu -->
            <Transition
              enter-active-class="transition ease-out duration-100"
              enter-from-class="opacity-0 scale-95"
              enter-to-class="opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="opacity-100 scale-100"
              leave-to-class="opacity-0 scale-95"
            >
              <div 
                v-if="showCourseDropdown && filteredCourses.length > 0" 
                class="absolute z-50 mt-2 w-full bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-80 overflow-y-auto"
              >
                <button
                  v-for="course in filteredCourses"
                  :key="course.id"
                  @click="selectCourse(course)"
                  :class="[
                    'w-full p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border-b border-gray-100 dark:border-gray-700 last:border-b-0',
                    selectedCourse?.id === course.id ? 'bg-indigo-50 dark:bg-indigo-900/20' : ''
                  ]"
                >
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">
                        {{ course.title }}
                      </h3>
                      <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ course.name }}
                      </p>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 ml-4">
                      <Users class="w-4 h-4" />
                      <span>{{ course.total_students }}</span>
                    </div>
                  </div>
                </button>
              </div>
            </Transition>
            
            <!-- No results -->
            <div 
              v-if="showCourseDropdown && courseSearchQuery && filteredCourses.length === 0" 
              class="absolute z-50 mt-2 w-full bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-4 text-center text-gray-500 dark:text-gray-400"
            >
              No courses found
            </div>
          </div>
          
          <!-- Selected Course Display -->
          <div v-if="selectedCourse" class="mt-4 p-4 bg-indigo-50 dark:bg-indigo-900/20 border-2 border-indigo-200 dark:border-indigo-800 rounded-lg">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                  <BookOpen class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                  <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                    {{ selectedCourse.title }}
                  </h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 ml-7">
                  {{ selectedCourse.name }}
                </p>
                <div class="mt-2 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 ml-7">
                  <Users class="w-4 h-4" />
                  <span>{{ selectedCourse.total_students }} students enrolled</span>
                </div>
              </div>
              <button
                @click="selectedCourse = null; students = []"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                title="Clear selection"
              >
                <X class="w-5 h-5" />
              </button>
            </div>
          </div>
        </div>

        <!-- Student List -->
        <div v-if="selectedCourse" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <!-- Toolbar -->
          <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
              <div class="flex-1 max-w-md">
                <div class="relative">
                  <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                  <input
                    v-model="searchQuery"
                    @input="refreshStudents"
                    type="text"
                    placeholder="Search by name, email, or student ID..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                  />
                </div>
              </div>
              
              <div class="flex items-center gap-3">
                <button
                  @click="showFilters = !showFilters"
                  class="flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                >
                  <Filter class="w-4 h-4" />
                  <span>Filters</span>
                  <ChevronDown :class="['w-4 h-4 transition-transform', showFilters ? 'rotate-180' : '']" />
                </button>
                
                <button
                  @click="refreshStudents"
                  :disabled="loading"
                  class="flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors disabled:opacity-50"
                >
                  <RefreshCw :class="['w-4 h-4', loading ? 'animate-spin' : '']" />
                  <span>Refresh</span>
                </button>
                
                <button
                  @click="exportReport('csv')"
                  :disabled="isExporting || students.length === 0"
                  class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors disabled:opacity-50"
                >
                  <Download class="w-4 h-4" />
                  <span v-if="isExporting">Exporting...</span>
                  <span v-else>Export CSV</span>
                </button>
              </div>
            </div>
            
            <!-- Filters -->
            <Transition
              enter-active-class="transition ease-out duration-200"
              enter-from-class="opacity-0 -translate-y-2"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition ease-in duration-150"
              leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 -translate-y-2"
            >
              <div v-if="showFilters" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div class="flex items-center justify-between mb-3">
                  <h3 class="font-medium text-gray-900 dark:text-gray-100">Filters</h3>
                  <button
                    @click="clearFilters"
                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1"
                  >
                    <X class="w-4 h-4" />
                    Clear all
                  </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Grade Level
                    </label>
                    <select
                      v-model="selectedGradeLevel"
                      @change="refreshStudents"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                    >
                      <option :value="null">All Grade Levels</option>
                      <option v-for="level in props.gradeLevels" :key="level.id" :value="level.id">
                        {{ level.display_name }}
                      </option>
                    </select>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Section
                    </label>
                    <select
                      v-model="selectedSection"
                      @change="refreshStudents"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                    >
                      <option :value="null">All Sections</option>
                      <option v-for="section in availableSections" :key="section" :value="section">
                        {{ section }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>
            </Transition>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="flex items-center justify-center py-12">
            <div class="flex flex-col items-center gap-4">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
              <p class="text-gray-600 dark:text-gray-400">Loading students...</p>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else-if="students.length === 0" class="text-center py-12">
            <Users class="w-16 h-16 text-gray-400 mx-auto mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
              No Students Found
            </h3>
            <p class="text-gray-600 dark:text-gray-400">
              {{ searchQuery || selectedGradeLevel || selectedSection ? 'Try adjusting your filters' : 'No students enrolled in this course yet' }}
            </p>
          </div>

          <!-- Student Table -->
          <div v-else class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Student
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Grade Level
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Section
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Progress
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Activities
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Avg Grade
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Enrolled
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr
                  v-for="student in filteredStudents"
                  :key="student.id"
                  class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <Users class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                          {{ student.name }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                          {{ student.email }}
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500">
                          ID: {{ student.student_id_text }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span v-if="student.grade_level" class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                      {{ student.grade_level.display_name }}
                    </span>
                    <span v-else class="text-sm text-gray-500 dark:text-gray-400">N/A</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span v-if="student.section" class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                      {{ student.section }}
                    </span>
                    <span v-else class="text-sm text-gray-500 dark:text-gray-400">N/A</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                      <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 w-24">
                        <div
                          :class="[getProgressColor(student.course_progress), 'h-2 rounded-full transition-all']"
                          :style="{ width: `${student.course_progress}%` }"
                        ></div>
                      </div>
                      <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ Math.round(student.course_progress) }}%
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm">
                      <div class="text-gray-900 dark:text-gray-100">
                        {{ student.completed_activities }}/{{ student.total_activities }} completed
                      </div>
                      <div class="text-gray-500 dark:text-gray-400">
                        {{ student.submitted_activities }} submitted
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span v-if="student.average_grade !== null" :class="['text-sm font-semibold', getGradeColor(student.average_grade)]">
                      {{ student.average_grade.toFixed(1) }}%
                    </span>
                    <span v-else class="text-sm text-gray-500 dark:text-gray-400">N/A</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ new Date(student.enrolled_at).toLocaleDateString() }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center gap-2">
                      <button
                        @click="viewStudentProfile(student.user_id)"
                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                        title="View Profile"
                      >
                        <Eye class="w-5 h-5" />
                      </button>
                      <button
                        @click="viewStudentActivities(student)"
                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                        title="View Activities"
                      >
                        <FileText class="w-5 h-5" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
