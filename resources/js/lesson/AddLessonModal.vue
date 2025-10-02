<template>
  <div v-if="visible" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
    <div class="bg-white rounded-lg p-6 w-1/3 z-50">
      <h3 class="text-lg font-bold mb-4">Add Lesson</h3>

      <form @submit.prevent="submitLesson" class="space-y-4">
        {{ moduleId }}
        <!-- Lesson Title -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Lesson Title</label>
          <input
            v-model="title"
            type="text"
            class="mt-1 block w-full border rounded p-2 text-gray-800"
            placeholder="Enter lesson title"
            required
          />
        </div>

        <!-- Lesson Description (Quill) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
          <div ref="editorContainer" class="border rounded min-h-[150px]"></div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-2 mt-2">
          <button
            type="button"
            @click="closeModal"
            class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700"
            :disabled="loading"
          >
            <span v-if="loading">Adding...</span>
            <span v-else>Add Lesson</span>
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
        placeholder: 'Enter lesson description...',
      });
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
