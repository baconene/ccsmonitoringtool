<template>
  <div>
    <!-- Readonly View -->
    <div
      v-if="!editing"
      class="p-2 border rounded cursor-pointer hover:bg-gray-50"
      @dblclick="startEditing"
    >
      <div v-html="modelValue"></div>
    </div>

    <!-- Inline Edit Mode -->
    <div
      v-else
      class="p-2 border rounded bg-white flex flex-col gap-2"
    >
      <!-- Quill Editor -->
      <div ref="quillEditor" class="border rounded"></div>

      <!-- Footer actions -->
      <div class="flex justify-end gap-2">
        <button
          @click="stopEditing"
          class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
          :disabled="saving"
        >
          Cancel
        </button>
        <button
          @click="saveContent"
          class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700"
          :disabled="saving"
        >
          {{ saving ? 'Saving...' : 'Save' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, nextTick } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import axios from 'axios';

const props = defineProps<{
  modelValue: string;
  lessonId: number;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void;
}>();

const editing = ref(false);
const quillEditor = ref<HTMLDivElement | null>(null);
let quill: Quill | null = null;
const saving = ref(false);

function startEditing() {
  editing.value = true;
  nextTick(() => {
    if (quillEditor.value) {
      quill = new Quill(quillEditor.value, {
        theme: 'snow',
        modules: {
          toolbar: [
            ['bold', 'italic', 'underline'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['link', 'image'],
          ],
        },
      });
      quill.root.innerHTML = props.modelValue;
    }
  });
}

async function saveContent() {
  if (!quill) return;
  const content = quill.root.innerHTML;
  saving.value = true;

  try {
    await axios.put(`lessons/${props.lessonId}`, {
      description: content,
    });

    emit('update:modelValue', content);
    stopEditing();
  } catch (error) {
    console.error('Failed to save lesson:', error);
    alert('Failed to save lesson content.');
  } finally {
    saving.value = false;
  }
}

function stopEditing() {
  editing.value = false;
  quill = null;
}
</script>

<style scoped>
.ql-container {
  min-height: 200px;
}
</style>
