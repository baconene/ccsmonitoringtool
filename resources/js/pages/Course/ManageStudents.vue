<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft, Users, UserPlus, UserMinus, GripVertical, Search, X } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

interface Student {
  id: number;
  name: string;
  email: string;
  grade_level?: string;
  section?: string;
}

interface GradeLevel {
  id: number;
  name: string;
  display_name: string;
}

interface Course {
  id: number;
  name: string;
  title: string;
  description?: string;
  grade_level?: string;
  grade_levels?: GradeLevel[];
}

const props = defineProps<{
  course: Course;
  enrolledStudents: Student[];
  availableStudents: Student[];
}>();

// Breadcrumb items
const breadcrumbItems: BreadcrumbItem[] = [
  { title: 'Home', href: '/' },
  { title: 'Course Management', href: '/course-management' },
  { title: 'Manage Students', href: `/courses/${props.course.id}/manage-students` }
];

// State
const searchAvailable = ref('');
const searchEnrolled = ref('');
const selectedAvailable = ref<number[]>([]);
const selectedEnrolled = ref<number[]>([]);
const draggingStudent = ref<Student | null>(null);
const dragOver = ref<'enrolled' | 'available' | null>(null);
const processing = ref(false);

// Filtered students
const filteredAvailableStudents = computed(() => {
  if (!searchAvailable.value.trim()) return props.availableStudents;
  const query = searchAvailable.value.toLowerCase();
  return props.availableStudents.filter(student =>
    student.name.toLowerCase().includes(query) ||
    student.email.toLowerCase().includes(query) ||
    (student.grade_level && student.grade_level.toLowerCase().includes(query)) ||
    (student.section && student.section.toLowerCase().includes(query))
  );
});

const filteredEnrolledStudents = computed(() => {
  if (!searchEnrolled.value.trim()) return props.enrolledStudents;
  const query = searchEnrolled.value.toLowerCase();
  return props.enrolledStudents.filter(student =>
    student.name.toLowerCase().includes(query) ||
    student.email.toLowerCase().includes(query) ||
    (student.grade_level && student.grade_level.toLowerCase().includes(query)) ||
    (student.section && student.section.toLowerCase().includes(query))
  );
});

// Selection handlers
const toggleAvailableSelection = (studentId: number) => {
  const index = selectedAvailable.value.indexOf(studentId);
  if (index > -1) {
    selectedAvailable.value.splice(index, 1);
  } else {
    selectedAvailable.value.push(studentId);
  }
};

const toggleEnrolledSelection = (studentId: number) => {
  const index = selectedEnrolled.value.indexOf(studentId);
  if (index > -1) {
    selectedEnrolled.value.splice(index, 1);
  } else {
    selectedEnrolled.value.push(studentId);
  }
};

const selectAllAvailable = () => {
  selectedAvailable.value = filteredAvailableStudents.value.map(s => s.id);
};

const clearAvailableSelection = () => {
  selectedAvailable.value = [];
};

const selectAllEnrolled = () => {
  selectedEnrolled.value = filteredEnrolledStudents.value.map(s => s.id);
};

const clearEnrolledSelection = () => {
  selectedEnrolled.value = [];
};

// Drag and drop handlers
const handleDragStart = (student: Student, from: 'available' | 'enrolled') => {
  draggingStudent.value = student;
  // If dragging from available, select it if not selected
  if (from === 'available' && !selectedAvailable.value.includes(student.id)) {
    selectedAvailable.value = [student.id];
  }
  // If dragging from enrolled, select it if not selected
  if (from === 'enrolled' && !selectedEnrolled.value.includes(student.id)) {
    selectedEnrolled.value = [student.id];
  }
};

const handleDragEnd = () => {
  draggingStudent.value = null;
  dragOver.value = null;
};

const handleDragOver = (e: DragEvent, zone: 'enrolled' | 'available') => {
  e.preventDefault();
  dragOver.value = zone;
};

const handleDragLeave = () => {
  dragOver.value = null;
};

const handleDrop = (e: DragEvent, zone: 'enrolled' | 'available') => {
  e.preventDefault();
  if (!draggingStudent.value) return;

  if (zone === 'enrolled') {
    // Moving to enrolled list (enroll students)
    enrollStudents(selectedAvailable.value.length > 0 ? selectedAvailable.value : [draggingStudent.value.id]);
  } else {
    // Moving to available list (remove students)
    removeStudents(selectedEnrolled.value.length > 0 ? selectedEnrolled.value : [draggingStudent.value.id]);
  }

  handleDragEnd();
};

// Enroll/Remove actions
const enrollStudents = (studentIds: number[]) => {
  if (studentIds.length === 0) return;
  if (processing.value) return;
  
  processing.value = true;
  router.post(
    `/courses/${props.course.id}/enroll-students`,
    { student_ids: studentIds },
    {
      preserveScroll: true,
      onSuccess: () => {
        selectedAvailable.value = [];
      },
      onFinish: () => {
        processing.value = false;
      }
    }
  );
};

const removeStudents = (studentIds: number[]) => {
  if (studentIds.length === 0) return;
  if (processing.value) return;

  if (!confirm(`Are you sure you want to remove ${studentIds.length} student(s) from this course?`)) {
    return;
  }
  
  processing.value = true;
  router.post(
    `/courses/${props.course.id}/remove-students`,
    { student_ids: studentIds },
    {
      preserveScroll: true,
      onSuccess: () => {
        selectedEnrolled.value = [];
      },
      onFinish: () => {
        processing.value = false;
      }
    }
  );
};

const enrollSelected = () => {
  enrollStudents(selectedAvailable.value);
};

const removeSelected = () => {
  removeStudents(selectedEnrolled.value);
};

const goBack = () => {
  router.visit('/course-management');
};
</script>

<template>
  <Head :title="`Manage Students - ${course.title || course.name}`" />

  <AppLayout :breadcrumbs="breadcrumbItems">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center gap-4 mb-4">
            <button
              @click="goBack"
              class="p-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              title="Go back"
            >
              <ArrowLeft class="w-5 h-5 text-gray-600 dark:text-gray-400" />
            </button>
            <div>
              <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Manage Students</h1>
              <p class="text-gray-600 dark:text-gray-400 mt-1">{{ course.title || course.name }}</p>
            </div>
          </div>

          <!-- Course Info -->
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start gap-3">
              <Users class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" />
              <div>
                <p class="text-sm text-blue-900 dark:text-blue-100">
                  <span v-if="course.grade_levels && course.grade_levels.length > 0" class="font-semibold">Grade Level Requirements: 
                    <span v-for="(gradeLevel, index) in course.grade_levels" :key="gradeLevel.id">
                      {{ gradeLevel.display_name }}<span v-if="index < course.grade_levels.length - 1">, </span>
                    </span>
                  </span>
                  <span v-else class="font-semibold">No grade level requirement</span>
                </p>
                <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                  <template v-if="course.grade_levels && course.grade_levels.length > 0">
                    Only students in {{ course.grade_levels.map((gl: GradeLevel) => gl.display_name).join(', ') }} can be enrolled in this course.
                  </template>
                  <template v-else>
                    All students are eligible for this course.
                  </template>
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Drag and Drop Area -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Available Students -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 flex flex-col">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                  <Users class="w-5 h-5" />
                  Available Students
                  <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ filteredAvailableStudents.length }})</span>
                </h2>
                <div class="flex gap-2">
                  <button
                    v-if="selectedAvailable.length > 0"
                    @click="clearAvailableSelection"
                    class="text-xs px-2 py-1 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white"
                  >
                    Clear
                  </button>
                  <button
                    @click="selectAllAvailable"
                    class="text-xs px-2 py-1 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
                  >
                    Select All
                  </button>
                </div>
              </div>

              <!-- Search -->
              <div class="relative">
                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input
                  v-model="searchAvailable"
                  type="text"
                  placeholder="Search students..."
                  class="w-full pl-10 pr-10 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                />
                <button
                  v-if="searchAvailable"
                  @click="searchAvailable = ''"
                  class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                >
                  <X class="w-4 h-4" />
                </button>
              </div>

              <!-- Action Button -->
              <button
                v-if="selectedAvailable.length > 0"
                @click="enrollSelected"
                :disabled="processing"
                class="mt-3 w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <UserPlus class="w-4 h-4" />
                Enroll {{ selectedAvailable.length }} Student(s)
              </button>
            </div>

            <!-- Student List -->
            <div
              class="flex-1 overflow-y-auto p-4 min-h-[400px] transition-colors"
              :class="{ 'bg-blue-50 dark:bg-blue-900/10': dragOver === 'available' }"
              @dragover="handleDragOver($event, 'available')"
              @dragleave="handleDragLeave"
              @drop="handleDrop($event, 'available')"
            >
              <div v-if="filteredAvailableStudents.length === 0" class="flex flex-col items-center justify-center h-full text-gray-400 dark:text-gray-500">
                <Users class="w-12 h-12 mb-2" />
                <p class="text-sm">{{ searchAvailable ? 'No students found' : 'No available students' }}</p>
              </div>
              <div v-else class="space-y-2">
                <div
                  v-for="student in filteredAvailableStudents"
                  :key="student.id"
                  draggable="true"
                  @dragstart="handleDragStart(student, 'available')"
                  @dragend="handleDragEnd"
                  class="group flex items-center gap-3 p-3 rounded-lg border transition-all cursor-move"
                  :class="selectedAvailable.includes(student.id)
                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                    : 'border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                  @click="toggleAvailableSelection(student.id)"
                >
                  <GripVertical class="w-4 h-4 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300" />
                  <input
                    type="checkbox"
                    :checked="selectedAvailable.includes(student.id)"
                    @click.stop="toggleAvailableSelection(student.id)"
                    class="w-4 h-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 cursor-pointer"
                  />
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ student.name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ student.email }}</p>
                    <div v-if="student.grade_level || student.section" class="flex gap-2 mt-1">
                      <span v-if="student.grade_level" class="text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded">
                        {{ student.grade_level }}
                      </span>
                      <span v-if="student.section" class="text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded">
                        {{ student.section }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Enrolled Students -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 flex flex-col">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                  <Users class="w-5 h-5" />
                  Enrolled Students
                  <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ filteredEnrolledStudents.length }})</span>
                </h2>
                <div class="flex gap-2">
                  <button
                    v-if="selectedEnrolled.length > 0"
                    @click="clearEnrolledSelection"
                    class="text-xs px-2 py-1 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white"
                  >
                    Clear
                  </button>
                  <button
                    @click="selectAllEnrolled"
                    class="text-xs px-2 py-1 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
                  >
                    Select All
                  </button>
                </div>
              </div>

              <!-- Search -->
              <div class="relative">
                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input
                  v-model="searchEnrolled"
                  type="text"
                  placeholder="Search enrolled students..."
                  class="w-full pl-10 pr-10 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                />
                <button
                  v-if="searchEnrolled"
                  @click="searchEnrolled = ''"
                  class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                >
                  <X class="w-4 h-4" />
                </button>
              </div>

              <!-- Action Button -->
              <button
                v-if="selectedEnrolled.length > 0"
                @click="removeSelected"
                :disabled="processing"
                class="mt-3 w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <UserMinus class="w-4 h-4" />
                Remove {{ selectedEnrolled.length }} Student(s)
              </button>
            </div>

            <!-- Student List -->
            <div
              class="flex-1 overflow-y-auto p-4 min-h-[400px] transition-colors"
              :class="{ 'bg-green-50 dark:bg-green-900/10': dragOver === 'enrolled' }"
              @dragover="handleDragOver($event, 'enrolled')"
              @dragleave="handleDragLeave"
              @drop="handleDrop($event, 'enrolled')"
            >
              <div v-if="filteredEnrolledStudents.length === 0" class="flex flex-col items-center justify-center h-full text-gray-400 dark:text-gray-500">
                <Users class="w-12 h-12 mb-2" />
                <p class="text-sm">{{ searchEnrolled ? 'No students found' : 'No enrolled students' }}</p>
              </div>
              <div v-else class="space-y-2">
                <div
                  v-for="student in filteredEnrolledStudents"
                  :key="student.id"
                  draggable="true"
                  @dragstart="handleDragStart(student, 'enrolled')"
                  @dragend="handleDragEnd"
                  class="group flex items-center gap-3 p-3 rounded-lg border transition-all cursor-move"
                  :class="selectedEnrolled.includes(student.id)
                    ? 'border-red-500 bg-red-50 dark:bg-red-900/20'
                    : 'border-gray-200 dark:border-gray-700 hover:border-red-300 dark:hover:border-red-700 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                  @click="toggleEnrolledSelection(student.id)"
                >
                  <GripVertical class="w-4 h-4 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300" />
                  <input
                    type="checkbox"
                    :checked="selectedEnrolled.includes(student.id)"
                    @click.stop="toggleEnrolledSelection(student.id)"
                    class="w-4 h-4 text-red-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-red-500 cursor-pointer"
                  />
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ student.name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ student.email }}</p>
                    <div v-if="student.grade_level || student.section" class="flex gap-2 mt-1">
                      <span v-if="student.grade_level" class="text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded">
                        {{ student.grade_level }}
                      </span>
                      <span v-if="student.section" class="text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded">
                        {{ student.section }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Instructions -->
        <div class="mt-6 bg-gray-100 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
          <h3 class="font-semibold text-gray-900 dark:text-white mb-2">How to use:</h3>
          <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
            <li>• <strong>Drag and Drop:</strong> Drag students between lists to enroll or remove them</li>
            <li>• <strong>Multi-Select:</strong> Click checkboxes or click on students to select multiple</li>
            <li>• <strong>Bulk Actions:</strong> Use "Enroll" or "Remove" buttons for selected students</li>
            <li>• <strong>Search:</strong> Use search bars to filter students by name, email, grade, or section</li>
          </ul>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
