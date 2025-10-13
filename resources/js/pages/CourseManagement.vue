<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { type BreadcrumbItem, type Activity } from '@/types';
import AddModuleModal from '@/module/AddModuleModal.vue';
import EditModuleModal from '@/module/EditModuleModal.vue';
import RemoveModuleModal from '@/module/RemoveModuleModal.vue';
import AddActivityToModuleModal from '@/module/AddActivityToModuleModal.vue';
import UploadDocumentModal from '@/module/UploadDocumentModal.vue';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import ModuleList from '@/module/ModuleList.vue';
import CourseBanner from '@/course/CourseBanner.vue';
import ModuleDetailsMain from '@/module/ModuleDetailsMain.vue';
import AddModuleButton from '@/module/AddModuleButton.vue';
import AddLessonModal from '@/lesson/AddLessonModal.vue';
import CourseModal from '@/course/CourseModal.vue';
import AddCourseButton from '@/course/AddCourseButton.vue';
import CosmicBackground from '@/components/CosmicBackground.vue';

// Breadcrumb items
const breadcrumbItems: BreadcrumbItem[] = [
  { title: 'Home', href: '/' },
  { title: 'Course Management', href: '/course-management' }
];

const props = defineProps<{
  courses: Array<{
    id: number;
    name: string;
    title: string;
    description?: string;
    instructor_id?: number;
    created_by?: number;
    course_code?: string;
    credits?: number;
    semester?: string;
    academic_year?: string;
    is_active?: boolean;
    enrollment_limit?: number;
    start_date?: string;
    end_date?: string;
    created_at: string;
    updated_at: string;
    students_count?: number;
    instructor?: {
      id: number;
      user?: {
        id: number;
        name: string;
        email: string;
      };
    };
    creator?: {
      id: number;
      name: string;
      email: string;
    };
    grade_levels: Array<{
      id: number;
      name: string;
      display_name: string;
      level: number;
    }>;
    modules: Array<{
      id: number;
      title?: string;
      description: string;
      sequence: number;
      completion_percentage?: number;
      module_type?: string;
      lessons?: Array<any>;
      activities?: Array<any>;
      course_id: number;
      created_by?: number;
      created_at: string;
      updated_at: string;
    }>;
  }>;
  coursesData?: {
    data: any[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
  };
  availableActivities?: Activity[];
}>();

// States
const searchText = ref('');
const selectedCourseId = ref<number | null>(props.courses[0]?.id || null);
const activeModuleId = ref<number | null>(null);

const showModuleForm = ref(false);
const showEditModuleModal = ref(false);
const showRemoveModuleModal = ref(false);
const showAddLessonModal = ref(false);
const showAddActivityModal = ref(false);
const showUploadDocumentModal = ref(false);

const inputFocused = ref(false);
const showCourseModal = ref(false);
const modalMode = ref<'create' | 'edit' | 'delete'>('create');
const editingCourse = ref<any>(null);

const showModuleList = ref(false);
const windowWidth = ref(window.innerWidth);

// Handle window resize
const handleResize = () => {
  windowWidth.value = window.innerWidth;
};

onMounted(() => {
  window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
});

// Computed
const filteredCourses = computed(() => {
  if (!searchText.value) {
    // Show all courses when input is empty
    return props.courses;
  }

  return props.courses.filter(
    c =>
      c.id.toString().includes(searchText.value) ||
      (c.name && c.name.toLowerCase().includes(searchText.value.toLowerCase())) ||
      (c.description && c.description.toLowerCase().includes(searchText.value.toLowerCase()))
  );
});

const selectedCourse = computed(
  () => props.courses.find(c => c.id === selectedCourseId.value) || null
);

const sortedModules = computed(() => {
  if (!selectedCourse.value) return [];
  return [...selectedCourse.value.modules].sort((a, b) => a.sequence - b.sequence);
});

const activeModule = computed(
  () => sortedModules.value.find(m => m.id === activeModuleId.value) || null
);

// Watch course changes
watch(selectedCourseId, () => {
  activeModuleId.value = sortedModules.value[0]?.id || null;
});

// Methods
function selectModule(moduleId: number) {
  activeModuleId.value = moduleId;
}

function selectCourse(courseId: number) {
  selectedCourseId.value = courseId;
  searchText.value = '';
  inputFocused.value = false;
}

function reloadCourses() {
  router.reload({ only: ['courses', 'availableActivities'] });
}

function openAddModuleModal() {
  if (!selectedCourseId.value) {
    alert('Please select a course first');
    return;
  }
  showModuleForm.value = true;
}

function handleBlur() {
  setTimeout(() => (inputFocused.value = false), 150);
}

// Course modal handlers
function openCreateCourse() {
  modalMode.value = 'create';
  editingCourse.value = null;
  showCourseModal.value = true;
}

function openEditCourse(course: any) {
  modalMode.value = 'edit';
  editingCourse.value = course;
  showCourseModal.value = true;
}

function openDeleteCourse(course: any) {
  modalMode.value = 'delete';
  editingCourse.value = course;
  showCourseModal.value = true;
}

function openManageStudents(course: any) {
  router.visit(`/courses/${course.id}/manage-students`);
}

// Enhanced refresh handler for course operations
function handleCourseRefresh(newCourseId?: number) {
    console.log('Course refresh triggered. New Course ID:', newCourseId);
  if (newCourseId) {
    console.log('New course created with ID:', newCourseId);
    selectedCourseId.value = newCourseId;
  }
  reloadCourses();
  showCourseModal.value = false;
}

// Handle activity removal from module
function handleRemoveActivity(data: { module: any; activityId: number }) {
  router.delete(`/modules/${data.module.id}/activities/${data.activityId}`, {
    onSuccess: () => {
      reloadCourses();
    },
    onError: (errors) => {
      console.error('Error removing activity:', errors);
      alert('Failed to remove activity from module');
    }
  });
}
</script>

<template>
  <Head title="Course Management" />
  <AppLayout :breadcrumbs="breadcrumbItems">
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-purple-50/30 to-pink-50/30 dark:from-gray-900 dark:via-purple-950/20 dark:to-pink-950/20 transition-colors relative overflow-hidden">
      <!-- Cosmic Background -->
      <CosmicBackground />
      
      <div class="container mx-auto px-4 py-6 max-w-7xl relative z-10">
        <div class="flex flex-col h-full gap-6">
          <!-- Header -->
          <div class="mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
              <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">Course Management</h1>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">Manage your courses, modules, and lessons</p>
              </div>
            </div>
          </div>

          <!-- Search -->
          <div class="relative">
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-start sm:items-center bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-purple-200/50 dark:border-purple-700/50 p-4 sm:p-5 transition-all hover:shadow-xl">
              <label for="course-search" class="font-semibold text-gray-900 dark:text-white whitespace-nowrap text-sm sm:text-base">
                Search Course:
              </label>
              <div class="flex-1 w-full flex flex-col sm:flex-row gap-2 sm:gap-3">
                <input
                  id="course-search"
                  v-model="searchText"
                  type="text"
                  placeholder="Type ID or description..."
                  @focus="inputFocused = true"
                  @blur="handleBlur"
                  class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 sm:px-4 py-2 sm:py-2.5 flex-1 w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:focus:border-purple-400 transition-all text-sm sm:text-base"
                />
                <AddCourseButton
                  @open="openCreateCourse"
                  class="w-full sm:w-auto"
                />
              </div>
            </div>

            <ul
              v-if="inputFocused"
              class="absolute top-full left-0 mt-2 w-full bg-white/95 dark:bg-gray-800/95 backdrop-blur-md border border-purple-200 dark:border-purple-700 rounded-xl shadow-2xl max-h-60 sm:max-h-80 overflow-y-auto z-50"
            >
              <li
                v-for="course in filteredCourses"
                :key="course.id"
                @mousedown.prevent="selectCourse(course.id)"
                class="px-4 py-3 cursor-pointer hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 dark:hover:from-purple-900/30 dark:hover:to-pink-900/30 text-gray-900 dark:text-white transition-all border-b border-gray-100 dark:border-gray-700 last:border-b-0"
              >
                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                  <span class="font-semibold text-sm sm:text-base">{{ course.name }}</span>
                  <span class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm">{{ course.description || course.name }}</span>
                </div>
              </li>
              <li v-if="!filteredCourses.length" class="px-4 py-3 text-gray-500 dark:text-gray-400 text-center text-sm">
                No courses found
              </li>
            </ul>
          </div>

          <!-- Course panel -->
          <div v-if="selectedCourse" class="flex flex-col gap-6">
            <CourseBanner
              :course="{
                ...selectedCourse,
                modules: selectedCourse.modules.map(module => ({
                  ...module,
                  completion_percentage: module.completion_percentage ?? 0
                }))
              }"
              @manageStudents="openManageStudents(selectedCourse)"
              @edit="openEditCourse(selectedCourse)"
              @delete="openDeleteCourse(selectedCourse)"
            />

            <div class="flex-1 flex flex-col xl:flex-row gap-4 sm:gap-6 min-h-[400px] sm:min-h-[600px]">
              <!-- Module list -->
              <div class="w-full xl:w-80 flex-shrink-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-purple-200/50 dark:border-purple-700/50 rounded-xl shadow-lg p-4 sm:p-5 flex flex-col transition-all hover:shadow-xl">
                <div class="mb-4">
                  <AddModuleButton @open="openAddModuleModal" class="w-full" />
                </div>

                <div class="flex justify-between items-center xl:hidden mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                  <h3 class="font-semibold text-gray-900 dark:text-white text-base sm:text-lg">Modules</h3>
                  <button
                    @click="showModuleList = !showModuleList"
                    class="text-xs sm:text-sm px-3 py-1.5 sm:py-2 border border-purple-300 dark:border-purple-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 dark:hover:from-purple-900/30 dark:hover:to-pink-900/30 transition-all shadow-sm hover:shadow-md"
                  >
                    {{ showModuleList ? 'Hide' : 'Show' }}
                  </button>
                </div>

                <div
                  v-show="showModuleList || windowWidth >= 1280"
                  class="flex-1 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-purple-300 dark:scrollbar-thumb-purple-700 scrollbar-track-transparent"
                >
                  <ModuleList
                    :modules="selectedCourse.modules.map(module => ({
                      ...module,
                      completion_percentage: module.completion_percentage ?? 0,
                      lessons: module.lessons ?? []
                    }))"
                    :activeModuleId="activeModuleId"
                    @selectModule="selectModule"
                  />
                </div>
              </div>

              <!-- Module details -->
              <div class="flex-1 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-purple-200/50 dark:border-purple-700/50 rounded-xl shadow-lg p-4 sm:p-6 transition-all hover:shadow-xl overflow-hidden">
                <ModuleDetailsMain
                  :module="activeModule ? { ...activeModule, created_by: activeModule.created_by ?? 0 } : null"
                  @edit="showEditModuleModal = true"
                  @remove="showRemoveModuleModal = true"
                  @add-lesson="showAddLessonModal = true"
                  @add-activity="showAddActivityModal = true"
                  @upload-document="showUploadDocumentModal = true"
                  @remove-activity="handleRemoveActivity"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <AddModuleModal
      v-if="selectedCourseId"
      :course-id="selectedCourseId"
      :show="showModuleForm"
      @close="showModuleForm = false"
      @saved="reloadCourses"
    />

    <EditModuleModal
      v-if="selectedCourseId"
      v-model:visible="showEditModuleModal"
      :course-id="selectedCourseId"
      :module-id="activeModule?.id"
      :defaults="{
        title: activeModule?.title,
        description: activeModule?.description,
        sequence: activeModule?.sequence,
        completion_percentage: activeModule?.completion_percentage,
        module_type: activeModule?.module_type,
        module_percentage: (activeModule as any)?.module_percentage
      }"
      @saved="reloadCourses"
    />

    <RemoveModuleModal
      :visible="showRemoveModuleModal"
      :module-id="activeModule?.id"
      title="Remove Module"
      message="Are you sure you want to remove this module?"
      @close="showRemoveModuleModal = false"
      @removed="reloadCourses"
    />

    <AddLessonModal
      :module-id="activeModule?.id ?? 0"
      :visible="showAddLessonModal"
      @close="showAddLessonModal = false"
      @saved="reloadCourses"
    />

    <!-- Course modal with proper refresh handling -->
    <CourseModal
      :open="showCourseModal"
      :mode="modalMode"
      :course="editingCourse"
      @close="showCourseModal = false"
      @refresh="handleCourseRefresh"
    />

    <!-- Add Activity Modal -->
    <AddActivityToModuleModal
      :visible="showAddActivityModal"
      :module-id="activeModule?.id ?? 0"
      :module-type="activeModule?.module_type || 'Mixed'"
      :available-activities="availableActivities || []"
      @close="showAddActivityModal = false"
      @added="reloadCourses"
    />

    <!-- Upload Document Modal -->
    <UploadDocumentModal
      :visible="showUploadDocumentModal"
      :module-id="activeModule?.id ?? 0"
      @close="showUploadDocumentModal = false"
      @uploaded="reloadCourses"
    />
  </AppLayout>
</template>

<style scoped>
.container {
  max-width: 1400px;
}

/* Custom scrollbar styling */
.scrollbar-thin {
  scrollbar-width: thin;
}

.scrollbar-thumb-purple-300 {
  scrollbar-color: rgb(216 180 254) transparent;
}

.dark .scrollbar-thumb-purple-700 {
  scrollbar-color: rgb(126 34 206) transparent;
}

/* Webkit scrollbar styling */
.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
  background: transparent;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
  background-color: rgb(216 180 254);
  border-radius: 3px;
}

.dark .scrollbar-thin::-webkit-scrollbar-thumb {
  background-color: rgb(126 34 206);
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background-color: rgb(192 132 252);
}

.dark .scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background-color: rgb(147 51 234);
}

/* Responsive breakpoints */
@media (max-width: 640px) {
  .container {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
  }
}

@media (min-width: 640px) and (max-width: 1024px) {
  .container {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
  }
}

@media (min-width: 1024px) {
  .container {
    padding-left: 2rem;
    padding-right: 2rem;
  }
}

/* Smooth transitions for dark mode */
* {
  transition-property: background-color, border-color, color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}
</style>
