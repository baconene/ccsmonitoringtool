<template>
  <div
    v-if="visible"
    class="fixed inset-0 flex items-center justify-center bg-black/50 dark:bg-black/70 z-50 p-4"
  >
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-2xl border border-gray-200 dark:border-gray-700">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Upload Documents</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Upload documents for this module
          </p>
        </div>
        <button
          @click="closeModal"
          class="p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          title="Close"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <form @submit.prevent="uploadDocuments" class="space-y-6">
        <!-- File Upload Area -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
            Select Files <span class="text-red-500">*</span>
          </label>
          <div
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
            class="relative border-2 border-dashed rounded-lg p-8 text-center transition-colors"
            :class="{
              'border-blue-500 bg-blue-50 dark:bg-blue-900/10': isDragging,
              'border-gray-300 dark:border-gray-600': !isDragging
            }"
          >
            <input
              ref="fileInput"
              type="file"
              multiple
              @change="handleFileSelect"
              class="hidden"
              accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.jpg,.jpeg,.png"
            />
            
            <div class="space-y-3">
              <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
              </div>
              
              <div>
                <button
                  type="button"
                  @click="fileInput?.click()"
                  class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium"
                >
                  Click to upload
                </button>
                <span class="text-gray-600 dark:text-gray-400"> or drag and drop</span>
              </div>
              
              <p class="text-xs text-gray-500 dark:text-gray-400">
                PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT, JPG, PNG (max 10MB each)
              </p>
            </div>
          </div>
        </div>

        <!-- Selected Files List -->
        <div v-if="selectedFiles.length > 0" class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
            Selected Files ({{ selectedFiles.length }})
          </label>
          <div class="space-y-2 max-h-48 overflow-y-auto">
            <div
              v-for="(file, index) in selectedFiles"
              :key="index"
              class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg"
            >
              <div class="flex items-center gap-3 flex-1 min-w-0">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                    {{ file.name }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ formatFileSize(file.size) }}
                  </p>
                </div>
              </div>
              <button
                type="button"
                @click="removeFile(index)"
                class="p-1 rounded hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
                title="Remove file"
              >
                <X class="w-4 h-4 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400" />
              </button>
            </div>
          </div>
        </div>

        <!-- Document Name/Description -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
            Description (Optional)
          </label>
          <textarea
            v-model="description"
            rows="3"
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors resize-none"
            placeholder="Add a description for these documents..."
          ></textarea>
        </div>

        <!-- Error Display -->
        <div v-if="uploadError" class="p-4 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800">
          <div class="flex items-start gap-2">
            <svg class="w-5 h-5 text-red-500 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-red-700 dark:text-red-400">{{ uploadError }}</p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
          <button
            type="button"
            @click="closeModal"
            class="w-full sm:w-auto px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors border border-gray-300 dark:border-gray-600"
            :disabled="isUploading"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="w-full sm:w-auto px-6 py-3 text-sm font-medium text-white bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            :disabled="selectedFiles.length === 0 || isUploading"
          >
            <svg v-if="isUploading" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span v-if="isUploading">Uploading...</span>
            <span v-else>Upload Documents</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { X } from 'lucide-vue-next';

const props = defineProps<{
  visible: boolean;
  moduleId: number;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'uploaded'): void;
}>();

const fileInput = ref<HTMLInputElement>();
const selectedFiles = ref<File[]>([]);
const description = ref('');
const isDragging = ref(false);
const isUploading = ref(false);
const uploadError = ref('');

const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB

function handleFileSelect(event: Event) {
  const target = event.target as HTMLInputElement;
  if (target.files) {
    addFiles(Array.from(target.files));
  }
}

function handleDrop(event: DragEvent) {
  isDragging.value = false;
  if (event.dataTransfer?.files) {
    addFiles(Array.from(event.dataTransfer.files));
  }
}

function addFiles(files: File[]) {
  uploadError.value = '';
  
  for (const file of files) {
    // Check file size
    if (file.size > MAX_FILE_SIZE) {
      uploadError.value = `File "${file.name}" exceeds 10MB limit`;
      continue;
    }
    
    // Check if file already added
    if (!selectedFiles.value.find(f => f.name === file.name && f.size === file.size)) {
      selectedFiles.value.push(file);
    }
  }
}

function removeFile(index: number) {
  selectedFiles.value.splice(index, 1);
  uploadError.value = '';
}

function formatFileSize(bytes: number): string {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

function uploadDocuments() {
  if (selectedFiles.value.length === 0) return;

  isUploading.value = true;
  uploadError.value = '';

  const formData = new FormData();
  selectedFiles.value.forEach((file, index) => {
    formData.append(`documents[${index}]`, file);
  });
  if (description.value) {
    formData.append('description', description.value);
  }

  router.post(`/modules/${props.moduleId}/documents`, formData, {
    onSuccess: () => {
      emit('uploaded');
      closeModal();
    },
    onError: (errors) => {
      console.error('Upload error:', errors);
      uploadError.value = 'Failed to upload documents. Please try again.';
    },
    onFinish: () => {
      isUploading.value = false;
    }
  });
}

function closeModal() {
  selectedFiles.value = [];
  description.value = '';
  uploadError.value = '';
  isDragging.value = false;
  emit('close');
}
</script>
