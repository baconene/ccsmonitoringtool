<template>
  <div v-if="visible" class="fixed inset-0 flex items-center justify-center bg-black/50 dark:bg-black/70 z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700">
      
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
          <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
          <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Add New Lesson</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Create a new lesson for this module</p>
          </div>
        </div>
        <button 
          @click="closeModal" 
          class="p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          title="Close"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form @submit.prevent="submitLesson" class="space-y-6">
        <!-- Lesson Title -->
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
            Lesson Title <span class="text-red-500">*</span>
          </label>
          <input
            v-model="title"
            type="text"
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
            placeholder="Enter a descriptive lesson title..."
            required
          />
          <p class="text-xs text-gray-500 dark:text-gray-400">
            Choose a clear, descriptive title that summarizes the lesson content.
          </p>
        </div>

        <!-- Lesson Description (Quill) -->
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
            Lesson Description
          </label>
          <div class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden bg-white dark:bg-gray-900">
            <div ref="editorContainer" class="min-h-[200px] quill-editor-container"></div>
          </div>
          <p class="text-xs text-gray-500 dark:text-gray-400">
            Provide detailed content for the lesson. Use the toolbar to format your text.
          </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
          <button
            type="button"
            @click="closeModal"
            class="w-full sm:w-auto px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
            :disabled="loading"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="w-full sm:w-auto px-6 py-3 text-sm font-medium text-white bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 flex items-center justify-center gap-2"
            :disabled="loading"
          >
            <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span v-if="loading">Creating Lesson...</span>
            <span v-else>Create Lesson</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, nextTick, watch } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import axios from 'axios';

const props = defineProps<{
  visible: boolean;
  moduleId: number;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'added', lesson: { id: number; title: string; description: string; documents: any[] }): void;
}>();

const title = ref('');
const editorContainer = ref<HTMLDivElement | null>(null);
let quill: Quill | null = null;
const loading = ref(false);

// Initialize Quill when modal opens
watch(() => props.visible, async (val) => {
  if (val) {
    await nextTick();
    if (editorContainer.value) {
      quill = new Quill(editorContainer.value, {
        theme: 'snow',
        modules: {
          toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'align': [] }],
            ['link', 'blockquote'],
            ['clean']
          ],
        },
        placeholder: 'Enter lesson description and content...',
      });
      
      // Apply theme-aware styling to Quill
      const toolbar = quill.container.querySelector('.ql-toolbar') as HTMLElement;
      const editor = quill.container.querySelector('.ql-editor') as HTMLElement;
      const container = quill.container as HTMLElement;
      
      const isDarkMode = document.documentElement.classList.contains('dark');
      
      if (toolbar) {
        toolbar.style.backgroundColor = isDarkMode ? '#1f2937' : '#ffffff';
        toolbar.style.borderColor = isDarkMode ? '#4b5563' : '#d1d5db';
        toolbar.style.color = isDarkMode ? '#f9fafb' : '#1f2937';
      }
      
      if (editor) {
        editor.style.backgroundColor = isDarkMode ? '#111827' : '#ffffff';
        editor.style.color = isDarkMode ? '#ffffff' : '#1f2937';
      }
      
      if (container) {
        container.style.backgroundColor = isDarkMode ? '#111827' : '#ffffff';
      }
      
      quill.focus();
    }
  } else {
    resetForm();
  }
});

function resetForm() {
  title.value = '';
  if (quill) {
    quill.setContents([]);
    quill = null;
  }
}

function closeModal() {
  emit('close');
  resetForm();
}

async function submitLesson() {
  if (!props.moduleId) return;

  loading.value = true;
  const description = quill?.root.innerHTML || '';

  try {
    const response = await axios.post('/lessons', {
      title: title.value,
      description,
      module_id: props.moduleId,
      documents: [], // optional, add documents if needed
    });

    // Emit lesson data returned from backend
    emit('added', response.data.lesson || {
      id: response.data.id,
      title: title.value,
      description,
      documents: [],
    });

    closeModal();
  } catch (err) {
    console.error('Failed to add lesson:', err);
    alert('Failed to add lesson. Check console for details.');
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
/* Quill editor styling */
.quill-editor-container :deep(.ql-container) {
  font-size: 14px;
  line-height: 1.6;
}

.quill-editor-container :deep(.ql-editor) {
  padding: 16px;
}

.quill-editor-container :deep(.ql-toolbar) {
  border-bottom: 1px solid #e5e7eb;
  padding: 8px 16px;
}

/* Dark mode styles for Quill */
:global(.dark) .quill-editor-container :deep(.ql-toolbar) {
  border-bottom-color: #4b5563 !important;
  background-color: #1f2937 !important;
  color: #f9fafb !important;
}

:global(.dark) .quill-editor-container :deep(.ql-editor) {
  background-color: #111827 !important;
  color: #ffffff !important;
}

:global(.dark) .quill-editor-container :deep(.ql-container) {
  background-color: #111827 !important;
}

/* Toolbar button styles for dark mode */
:global(.dark) .quill-editor-container :deep(.ql-toolbar .ql-stroke) {
  stroke: #d1d5db;
}

:global(.dark) .quill-editor-container :deep(.ql-toolbar .ql-fill) {
  fill: #d1d5db;
}

:global(.dark) .quill-editor-container :deep(.ql-toolbar button:hover .ql-stroke) {
  stroke: #3b82f6;
}

:global(.dark) .quill-editor-container :deep(.ql-toolbar button:hover .ql-fill) {
  fill: #3b82f6;
}

:global(.dark) .quill-editor-container :deep(.ql-toolbar button.ql-active .ql-stroke) {
  stroke: #3b82f6;
}

:global(.dark) .quill-editor-container :deep(.ql-toolbar button.ql-active .ql-fill) {
  fill: #3b82f6;
}

/* Dropdown styles for dark mode */
:global(.dark) .quill-editor-container :deep(.ql-toolbar .ql-picker) {
  color: #d1d5db;
}

:global(.dark) .quill-editor-container :deep(.ql-toolbar .ql-picker-options) {
  background-color: #1f2937;
  border-color: #4b5563;
}

:global(.dark) .quill-editor-container :deep(.ql-toolbar .ql-picker-item:hover) {
  background-color: #374151;
}
</style>
