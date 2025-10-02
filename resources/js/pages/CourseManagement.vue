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
    <div class="flex flex-col h-full p-4 gap-4">
      <!-- Search -->
      <div class="relative flex gap-2 items-center">
        <label for="course-search" class="font-semibold">
          Search Course (ID or description):
        </label>
        <input
          id="course-search"
          v-model="searchText"
          type="text"
          placeholder="Type ID or description..."
          @focus="inputFocused = true"
          @blur="handleBlur"
          class="border rounded px-2 py-1 flex-1"
        />
        <AddCourseButton
          @open="openCreateCourse"
        />

        <ul
          v-if="inputFocused"
          class="absolute top-full left-0 mt-1 w-full bg-white border rounded shadow-lg max-h-60 overflow-y-auto z-50"
        >
          <li
            v-for="course in filteredCourses"
            :key="course.id"
            @mousedown.prevent="selectCourse(course.id)"
            class="px-2 py-1 cursor-pointer hover:bg-blue-100"
          >
            <span class="font-semibold">ID: {{ course.id }}</span> -
            {{ course.description || course.name }}
          </li>
          <li v-if="!filteredCourses.length" class="px-2 py-1 text-gray-500">
            No courses found
          </li>
        </ul>
      </div>

      <!-- Course panel -->
      <div v-if="selectedCourse" class="flex flex-col gap-2">
        <CourseBanner
          :course="selectedCourse"
          @edit="openEditCourse(selectedCourse)"
          @delete="openDeleteCourse(selectedCourse)"
        />

        <div class="flex-1 flex flex-col md:flex-row gap-4 h-[600px]">
          <!-- Module list -->
          <div class="w-full md:w-64 flex-shrink-0 border rounded p-2 flex flex-col">
            <div class="mb-2">
              <AddModuleButton @open="showModuleForm = true" />
            </div>

            <div class="flex justify-between items-center md:hidden">
              <h3 class="font-semibold">Modules</h3>
              <button
                @click="showModuleList = !showModuleList"
                class="text-sm px-2 py-1 border rounded"
              >
                {{ showModuleList ? 'Hide' : 'Show' }}
              </button>
            </div>

            <div
              v-show="showModuleList || windowWidth >= 768"
              class="flex-1 overflow-y-auto mt-2 md:mt-0"
            >
              <ModuleList
                :modules="selectedCourse.modules"
                :activeModuleId="activeModuleId"
                @selectModule="selectModule"
              />
            </div>
          </div>

          <!-- Module details -->
          <div class="flex-1 border rounded p-2">
            <ModuleDetailsMain
              :module="activeModule"
              @edit="showEditModuleModal = true"
              @remove="showRemoveModuleModal = true"
              @add-lesson="showAddLessonModal = true"
            />
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

    </div>
  </AppLayout>
</template>
