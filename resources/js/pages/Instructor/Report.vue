<template>
  <Head title="Instructor Grade Report" />
  <AppLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Instructor Grade Report
      </h2>
    </template>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
      <!-- Course Selection -->
      <div class="bg-white shadow-sm rounded-lg mb-6">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Select Course</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <button
              v-for="course in teachingCourses"
              :key="course.id"
              @click="selectCourse(course.id)"
              :class="[
                'p-4 border rounded-lg text-left transition-colors duration-200',
                selectedCourseId === course.id
                  ? 'border-blue-500 bg-blue-50'
                  : 'border-gray-300 hover:border-gray-400'
              ]"
            >
              <h4 class="font-medium text-gray-900">{{ course.title }}</h4>
              <p class="text-sm text-gray-500 mt-1">{{ course.description }}</p>
              <div class="flex justify-between mt-2 text-xs text-gray-400">
                <span>{{ course.students_count }} students</span>
                <span>{{ course.modules_count }} modules</span>
              </div>
            </button>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="bg-white shadow-sm rounded-lg">
        <div class="p-6 text-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
          <p class="mt-2 text-gray-600">Loading course report...</p>
        </div>
      </div>

      <!-- Course Report Content -->
      <div v-else-if="courseReport" class="space-y-6">
        <!-- Course Overview -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-gray-900">
                {{ courseReport.course.title }} - Overview
              </h3>
              <div class="flex space-x-2">
                <button
                  @click="exportPDF"
                  class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                  <DocumentArrowDownIcon class="h-4 w-4 mr-1" />
                  Export PDF
                </button>
                <button
                  @click="exportCSV"
                  class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <DocumentArrowDownIcon class="h-4 w-4 mr-1" />
                  Export CSV
                </button>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
              <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ courseReport.class_statistics.average_grade }}%</div>
                <div class="text-sm text-gray-600">Class Average</div>
              </div>
              <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ courseReport.class_statistics.highest_grade }}%</div>
                <div class="text-sm text-gray-600">Highest Grade</div>
              </div>
              <div class="bg-red-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-red-600">{{ courseReport.class_statistics.lowest_grade }}%</div>
                <div class="text-sm text-gray-600">Lowest Grade</div>
              </div>
              <div class="bg-emerald-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-emerald-600">{{ courseReport.class_statistics.passing_count }}</div>
                <div class="text-sm text-gray-600">Passing</div>
              </div>
              <div class="bg-orange-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-orange-600">{{ courseReport.class_statistics.failing_count }}</div>
                <div class="text-sm text-gray-600">Failing</div>
              </div>
              <div class="bg-purple-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">{{ courseReport.class_statistics.completion_rate }}%</div>
                <div class="text-sm text-gray-600">Completion Rate</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Grade Distribution Chart -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Grade Distribution</h4>
            <div class="space-y-3">
              <div
                v-for="(data, grade) in gradeDistribution"
                :key="grade"
                class="flex items-center"
              >
                <div class="w-20 text-sm font-medium text-gray-700">{{ grade }}</div>
                <div class="flex-1 mx-4">
                  <div class="bg-gray-200 rounded-full h-4">
                    <div
                      :class="getGradeBarClass(grade)"
                      class="h-4 rounded-full transition-all duration-300"
                      :style="{ width: `${data.percentage}%` }"
                    ></div>
                  </div>
                </div>
                <div class="w-20 text-sm text-gray-600">
                  {{ data.count }} ({{ data.percentage }}%)
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Student Performance Table -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-4">
              <h4 class="text-lg font-medium text-gray-900">Student Performance</h4>
              <div class="flex space-x-2">
                <select
                  v-model="sortBy"
                  @change="sortStudents"
                  class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                  <option value="rank">Sort by Rank</option>
                  <option value="name">Sort by Name</option>
                  <option value="grade">Sort by Grade</option>
                  <option value="completion">Sort by Completion</option>
                </select>
                <select
                  v-model="filterStatus"
                  @change="filterStudents"
                  class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                  <option value="all">All Students</option>
                  <option value="completed">Completed</option>
                  <option value="in_progress">In Progress</option>
                  <option value="not_started">Not Started</option>
                </select>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Rank
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Student
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Grade
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Letter
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Progress
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr
                    v-for="(student, index) in filteredStudents"
                    :key="student.student_id"
                  >
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ getRank(student, index) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="h-8 w-8 flex-shrink-0">
                          <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
                            <span class="text-xs font-medium text-white">
                              {{ student.student_name.charAt(0).toUpperCase() }}
                            </span>
                          </div>
                        </div>
                        <div class="ml-3">
                          <div class="text-sm font-medium text-gray-900">
                            {{ student.student_name }}
                          </div>
                          <div class="text-sm text-gray-500">
                            {{ student.student_email }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-1 mr-3">
                          <div class="bg-gray-200 rounded-full h-2">
                            <div
                              :class="getGradeProgressClass(student.overall_grade)"
                              class="h-2 rounded-full transition-all duration-300"
                              :style="{ width: `${student.overall_grade}%` }"
                            ></div>
                          </div>
                        </div>
                        <span
                          :class="getGradeTextClass(student.overall_grade)"
                          class="text-sm font-medium"
                        >
                          {{ student.overall_grade }}%
                        </span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="getLetterGradeClass(student.overall_letter_grade)"
                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                      >
                        {{ student.overall_letter_grade }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="getStatusClass(student.completion_status)"
                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                      >
                        {{ student.completion_status.replace('_', ' ').toUpperCase() }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div>Modules: {{ student.completed_modules }}/{{ student.module_count }}</div>
                      <div>Activities: {{ student.activity_summary.completed }}/{{ student.activity_summary.total }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <button
                        @click="viewStudentDetail(student.student_id)"
                        class="text-blue-600 hover:text-blue-900"
                      >
                        View Details
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Performance Insights -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Performance Insights</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h5 class="text-sm font-medium text-gray-700 mb-2">Top Performers</h5>
                <div class="space-y-2">
                  <div
                    v-for="student in topPerformers"
                    :key="student.student_id"
                    class="flex justify-between items-center p-2 bg-green-50 rounded"
                  >
                    <span class="text-sm font-medium">{{ student.student_name }}</span>
                    <span class="text-sm text-green-600">{{ student.overall_grade }}%</span>
                  </div>
                </div>
              </div>
              <div>
                <h5 class="text-sm font-medium text-gray-700 mb-2">Students at Risk</h5>
                <div class="space-y-2">
                  <div
                    v-for="student in studentsAtRisk"
                    :key="student.student_id"
                    class="flex justify-between items-center p-2 bg-red-50 rounded"
                  >
                    <span class="text-sm font-medium">{{ student.student_name }}</span>
                    <span class="text-sm text-red-600">{{ student.overall_grade }}%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { DocumentArrowDownIcon } from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  teachingCourses: Array,
  courseReport: Object,
  selectedCourse: String
})

// Reactive data
const selectedCourseId = ref(props.selectedCourse || null)
const courseReport = ref(props.courseReport || null)
const loading = ref(false)
const sortBy = ref('rank')
const filterStatus = ref('all')

// Computed properties
const gradeDistribution = computed(() => {
  if (!courseReport.value?.students) return {}
  
  const total = courseReport.value.students.length
  const distribution = {
    'A (90-100%)': { count: 0, percentage: 0 },
    'B (80-89%)': { count: 0, percentage: 0 },
    'C (70-79%)': { count: 0, percentage: 0 },
    'D (60-69%)': { count: 0, percentage: 0 },
    'F (Below 60%)': { count: 0, percentage: 0 }
  }
  
  courseReport.value.students.forEach(student => {
    const grade = student.overall_grade
    if (grade >= 90) distribution['A (90-100%)'].count++
    else if (grade >= 80) distribution['B (80-89%)'].count++
    else if (grade >= 70) distribution['C (70-79%)'].count++
    else if (grade >= 60) distribution['D (60-69%)'].count++
    else distribution['F (Below 60%)'].count++
  })
  
  Object.keys(distribution).forEach(key => {
    distribution[key].percentage = total > 0 ? Math.round((distribution[key].count / total) * 100) : 0
  })
  
  return distribution
})

const filteredStudents = computed(() => {
  if (!courseReport.value?.students) return []
  
  let students = [...courseReport.value.students]
  
  // Filter by status
  if (filterStatus.value !== 'all') {
    students = students.filter(student => student.completion_status === filterStatus.value)
  }
  
  // Sort students
  students.sort((a, b) => {
    switch (sortBy.value) {
      case 'name':
        return a.student_name.localeCompare(b.student_name)
      case 'grade':
        return b.overall_grade - a.overall_grade
      case 'completion':
        return b.completed_modules - a.completed_modules
      default: // rank
        return b.overall_grade - a.overall_grade
    }
  })
  
  return students
})

const topPerformers = computed(() => {
  if (!courseReport.value?.students) return []
  return [...courseReport.value.students]
    .sort((a, b) => b.overall_grade - a.overall_grade)
    .slice(0, 5)
})

const studentsAtRisk = computed(() => {
  if (!courseReport.value?.students) return []
  return courseReport.value.students
    .filter(student => student.overall_grade < 70)
    .sort((a, b) => a.overall_grade - b.overall_grade)
    .slice(0, 5)
})

// Methods
const selectCourse = (courseId) => {
  if (selectedCourseId.value === courseId) return
  
  selectedCourseId.value = courseId
  loading.value = true
  
  router.get(route('instructor.report'), { course_id: courseId }, {
    preserveState: true,
    onSuccess: (page) => {
      courseReport.value = page.props.courseReport
      loading.value = false
    },
    onError: () => {
      loading.value = false
    }
  })
}

const exportPDF = () => {
  const url = route('instructor.report.pdf', { course_id: selectedCourseId.value })
  window.open(url, '_blank')
}

const exportCSV = () => {
  const url = route('instructor.report.csv', { course_id: selectedCourseId.value })
  window.open(url, '_blank')
}

const sortStudents = () => {
  // Trigger reactivity
  filterStatus.value = filterStatus.value
}

const filterStudents = () => {
  // Trigger reactivity
  sortBy.value = sortBy.value
}

const getRank = (student, index) => {
  if (sortBy.value === 'rank' || sortBy.value === 'grade') {
    return index + 1
  }
  return '-'
}

const getGradeBarClass = (grade) => {
  if (grade.includes('A')) return 'bg-green-500'
  if (grade.includes('B')) return 'bg-blue-500'
  if (grade.includes('C')) return 'bg-yellow-500'
  if (grade.includes('D')) return 'bg-orange-500'
  return 'bg-red-500'
}

const getGradeProgressClass = (grade) => {
  if (grade >= 90) return 'bg-green-500'
  if (grade >= 80) return 'bg-blue-500'
  if (grade >= 70) return 'bg-yellow-500'
  if (grade >= 60) return 'bg-orange-500'
  return 'bg-red-500'
}

const getGradeTextClass = (grade) => {
  if (grade >= 90) return 'text-green-600'
  if (grade >= 80) return 'text-blue-600'
  if (grade >= 70) return 'text-yellow-600'
  if (grade >= 60) return 'text-orange-600'
  return 'text-red-600'
}

const getLetterGradeClass = (letterGrade) => {
  switch (letterGrade) {
    case 'A': return 'bg-green-100 text-green-800'
    case 'B': return 'bg-blue-100 text-blue-800'
    case 'C': return 'bg-yellow-100 text-yellow-800'
    case 'D': return 'bg-orange-100 text-orange-800'
    case 'F': return 'bg-red-100 text-red-800'
    default: return 'bg-gray-100 text-gray-800'
  }
}

const getStatusClass = (status) => {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-800'
    case 'in_progress':
      return 'bg-yellow-100 text-yellow-800'
    case 'not_started':
      return 'bg-gray-100 text-gray-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const viewStudentDetail = (studentId) => {
  // Navigate to student detail view
  router.get(route('instructor.student.detail', { 
    course_id: selectedCourseId.value, 
    student_id: studentId 
  }))
}

onMounted(() => {
  // If no course is selected and we have available courses, select the first one
  if (!selectedCourseId.value && props.teachingCourses.length > 0) {
    selectCourse(props.teachingCourses[0].id)
  }
})
</script>