<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import AddModuleModal from '@/module/AddModuleModal.vue';
import EditModuleModal from '@/module/EditModuleModal.vue';
import RemoveModuleModal from '@/module/RemoveModuleModal.vue';
import { ref, computed, watch } from 'vue';
import ModuleList from '@/module/ModuleList.vue';
import CourseBanner from '@/course/CourseBanner.vue';
import ModuleDetailsMain from '@/module/ModuleDetailsMain.vue';
import AddModuleButton from '@/module/AddModuleButton.vue';
import AddLessonModal from '@/lesson/AddLessonModal.vue';
import CourseModal from '@/course/CourseModal.vue';
import AddCourseButton from '@/course/AddCourseButton.vue';

const props = defineProps<{
  courses: Array<{
    id: number;
    name: string;
    description?: string;
    modules: Array<{
      id: number;
      description: string;
      sequence: number;
      completion_percentage: number;
      lessons: Array<any>;
    }>;
  }>;
}>();

// States
const searchText = ref('');
const selectedCourseId = ref<number | null>(props.courses[0]?.id || null);
const activeModuleId = ref<number | null>(null);

const showModuleForm = ref(false);
const showEditModuleModal = ref(false);
const showRemoveModuleModal = ref(false);
const showAddLessonModal = ref(false);

const inputFocused = ref(false);
const showCourseModal = ref(false);
const modalMode = ref<'create' | 'edit' | 'delete'>('create');
const editingCourse = ref<any>(null);

const showModuleList = ref(false);
const windowWidth = ref(window.innerWidth);

window.addEventListener('resize', () => {
  windowWidth.value = window.innerWidth;
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
  router.reload({ only: ['courses'] });
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
</script>

<template>
  <Head title="Course Management" />
  <AppLayout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors">
      <div class="container mx-auto px-4 py-6 max-w-7xl">
        <div class="flex flex-col h-full gap-6">
          <!-- Header -->
          <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Course Management</h1>
            <p class="text-gray-600 dark:text-gray-300">Manage your courses, modules, and lessons</p>
          </div>

          <!-- Search -->
          <div class="relative">
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors">
              <label for="course-search" class="font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                Search Course:
              </label>
              <div class="flex-1 flex gap-2">
                <input
                  id="course-search"
                  v-model="searchText"
                  type="text"
                  placeholder="Type ID or description..."
                  @focus="inputFocused = true"
                  @blur="handleBlur"
                  class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 flex-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
                />
                <AddCourseButton
                  @open="openCreateCourse"
                />
              </div>
            </div>

            <ul
              v-if="inputFocused"
              class="absolute top-full left-0 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-y-auto z-50"
            >
              <li
                v-for="course in filteredCourses"
                :key="course.id"
                @mousedown.prevent="selectCourse(course.id)"
                class="px-4 py-2 cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white transition-colors"
              >
                <span class="font-semibold">ID: {{ course.id }}</span> -
                {{ course.description || course.name }}
              </li>
              <li v-if="!filteredCourses.length" class="px-4 py-2 text-gray-500 dark:text-gray-400">
                No courses found
              </li>
            </ul>
          </div>

          <!-- Course panel -->
          <div v-if="selectedCourse" class="flex flex-col gap-6">
            <CourseBanner
              :course="selectedCourse"
              @edit="openEditCourse(selectedCourse)"
              @delete="openDeleteCourse(selectedCourse)"
            />

            <div class="flex-1 flex flex-col xl:flex-row gap-6 min-h-[600px]">
              <!-- Module list -->
              <div class="w-full xl:w-80 flex-shrink-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 flex flex-col transition-colors">
                <div class="mb-4">
                  <AddModuleButton @open="showModuleForm = true" />
                </div>

                <div class="flex justify-between items-center xl:hidden mb-4">
                  <h3 class="font-semibold text-gray-900 dark:text-white">Modules</h3>
                  <button
                    @click="showModuleList = !showModuleList"
                    class="text-sm px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                  >
                    {{ showModuleList ? 'Hide' : 'Show' }}
                  </button>
                </div>

                <div
                  v-show="showModuleList || windowWidth >= 1280"
                  class="flex-1 overflow-y-auto"
                >
                  <ModuleList
                    :modules="selectedCourse.modules"
                    :activeModuleId="activeModuleId"
                    @selectModule="selectModule"
                  />
                </div>
              </div>

              <!-- Module details -->
              <div class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 transition-colors">
                <ModuleDetailsMain
                  :module="activeModule"
                  @edit="showEditModuleModal = true"
                  @remove="showRemoveModuleModal = true"
                  @add-lesson="showAddLessonModal = true"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <AddModuleModal
      :course-id="selectedCourseId!"
      :show="showModuleForm"
      @close="showModuleForm = false"
      @saved="reloadCourses"
    />

    <EditModuleModal
      v-model:visible="showEditModuleModal"
      :course-id="selectedCourseId!"
      :module-id="activeModule?.id"
      :defaults="{
        description: activeModule?.description,
        sequence: activeModule?.sequence,
        completion_percentage: activeModule?.completion_percentage
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
  </AppLayout>
</template>
