<template>
  <div class="space-y-6 h-full flex flex-col">
  

    <!-- Existing Lessons -->
    <div class="space-y-6">
      <template v-for="(lesson, index) in localLessons" :key="lesson.id">
        <div class="lesson-card border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition-shadow">
          <!-- Lesson Header -->
          <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-b border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <!-- Lesson Number Badge -->
                <div class="w-8 h-8 rounded-full bg-blue-600 dark:bg-blue-500 text-white flex items-center justify-center text-sm font-semibold">
                  {{ index + 1 }}
                </div>
                
                <!-- Lesson Title -->
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ lesson.title }}</h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400">Lesson {{ index + 1 }} of {{ localLessons.length }}</p>
                </div>
              </div>
              
              <!-- Lesson Actions -->
              <div class="flex items-center gap-2">
     
                
                <button
                  class="p-2 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors group"
                  title="Delete lesson"
                >
                  <svg class="w-4 h-4 text-gray-600 dark:text-gray-400 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Lesson Content -->
          <div class="p-6 space-y-6">
            <!-- Content Section -->
            <div>
              <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h4 class="font-medium text-gray-900 dark:text-gray-100">Lesson Content</h4>
              </div>
              <ModuleDetails v-model="lesson.description" :lesson-id="lesson.id" />
            </div>

            <!-- Documents Section -->
            <div>
              <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
                <h4 class="font-medium text-gray-900 dark:text-gray-100">Related Documents</h4>
              </div>
              <RelatedDocumentContainer v-model="lesson.documents" />
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- Empty State - Add First Lesson -->
    <div v-if="localLessons.length === 0" class="flex flex-col items-center justify-center py-12">
      <!-- Empty State Icon -->
      <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
        <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
      </div>
      
      <!-- Empty State Content -->
      <div class="text-center max-w-md">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
          No Lessons Yet
        </h3>
        <p class="text-gray-500 dark:text-gray-400 mb-6">
          Start building your course by adding lessons and activities. Each lesson can contain rich content and supporting documents.
        </p>
        
        <!-- Add Lesson Button -->
        <button
          @click="showAddLessonModal = true"
          class="inline-flex items-center gap-3 px-6 py-3 bg-blue-600 dark:bg-blue-500 text-white rounded-xl hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors font-medium"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Add First Lesson
        </button>
      </div>
      
      <!-- Tips -->
      <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 max-w-md">
        <div class="flex items-start gap-3">
          <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div class="text-sm">
            <p class="font-medium text-blue-900 dark:text-blue-100 mb-1">Pro Tip</p>
            <p class="text-blue-700 dark:text-blue-300">
              Organize your content with clear lesson titles and attach relevant documents to enhance the learning experience.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Lesson Modal -->
    <AddLessonModal
      :visible="showAddLessonModal"
      :module-id="moduleId"
      @close="showAddLessonModal = false"
      @added="refreshLessons"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch, onMounted } from 'vue';
import ModuleDetails from '@/course/ModuleDetails.vue';
import RelatedDocumentContainer from '@/course/RelatedDocumentContainer.vue';
import AddLessonModal from './AddLessonModal.vue';

const props = defineProps<{
  moduleId: number;
  lessons: Array<{
    id: number;
    title: string;
    description: string;
    documents: Array<{ id: number; name: string; file_path: string; doc_type: string }>;
  }>;
}>();

const emit = defineEmits<{
  (e: 'update:lessons', lessons: typeof props.lessons): void;
}>();

const showAddLessonModal = ref(false);

// Local state - ensure all lessons have a documents array
const localLessons = reactive([...props.lessons.map(lesson => ({
  ...lesson,
  documents: lesson.documents || []
}))]);

// Keep in sync if parent updates
watch(
  () => props.lessons,
  (newVal) => {
    const normalizedLessons = newVal.map(lesson => ({
      ...lesson,
      documents: lesson.documents || []
    }));
    localLessons.splice(0, localLessons.length, ...normalizedLessons);
  }
);

// 🔥 Refresh lessons from backend
async function refreshLessons() {
  try {
    const res = await fetch(`/modules/${props.moduleId}/lessons`);
    if (!res.ok) throw new Error('Failed to fetch lessons');
    const data = await res.json();

    // Ensure all lessons have a documents array
    const normalizedData = data.map(lesson => ({
      ...lesson,
      documents: lesson.documents || []
    }));

    localLessons.splice(0, localLessons.length, ...normalizedData);
    emit('update:lessons', [...normalizedData]);
  } catch (err) {
    console.error('Error refreshing lessons:', err);
  } finally {
    showAddLessonModal.value = false;
  }
}

// Optionally load on mount
onMounted(() => {
  refreshLessons();
});
</script>
