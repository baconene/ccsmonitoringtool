<template>
  <div class="document-uploader">
    <!-- Upload Area -->
    <div
      class="upload-area"
      :class="{ 'drag-over': isDragging, 'disabled': uploading }"
      @dragover.prevent="handleDragOver"
      @dragleave.prevent="handleDragLeave"
      @drop.prevent="handleDrop"
      @click="triggerFileInput"
    >
      <input
        ref="fileInput"
        type="file"
        :multiple="multiple"
        :accept="acceptedTypes"
        class="hidden"
        @change="handleFileSelect"
        :disabled="uploading"
      />

      <div class="upload-content">
        <svg
          v-if="!uploading"
          class="upload-icon"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
          />
        </svg>

        <div v-if="uploading" class="flex items-center justify-center">
          <svg
            class="animate-spin h-8 w-8 text-blue-600"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            ></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
        </div>

        <p class="upload-text">
          <span v-if="!uploading" class="font-semibold">Click to upload</span>
          <span v-else class="font-semibold">Uploading...</span>
          <span v-if="!uploading"> or drag and drop</span>
        </p>

        <p v-if="!uploading" class="upload-hint">
          {{ acceptedTypesText }} (Max {{ maxFileSizeMB }}MB)
        </p>

        <p v-if="uploading" class="text-sm text-gray-500 mt-2">
          {{ uploadProgress }}%
        </p>
      </div>
    </div>

    <!-- Error Messages -->
    <div v-if="errors.length > 0" class="mt-3 space-y-1">
      <div
        v-for="(error, index) in errors"
        :key="index"
        class="text-sm text-red-600 flex items-center"
      >
        <svg
          class="w-4 h-4 mr-1"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
            clip-rule="evenodd"
          />
        </svg>
        {{ error }}
      </div>
    </div>

    <!-- File Preview List -->
    <div v-if="selectedFiles.length > 0" class="mt-4 space-y-2">
      <h4 class="text-sm font-medium text-gray-700">
        Selected Files ({{ selectedFiles.length }})
      </h4>
      <div class="space-y-2">
        <div
          v-for="(file, index) in selectedFiles"
          :key="index"
          class="file-preview"
        >
          <div class="flex items-center space-x-3">
            <!-- File Icon -->
            <div class="flex-shrink-0">
              <svg
                v-if="isImage(file)"
                class="w-10 h-10 text-blue-500"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                  clip-rule="evenodd"
                />
              </svg>
              <svg
                v-else-if="isPdf(file)"
                class="w-10 h-10 text-red-500"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                  clip-rule="evenodd"
                />
              </svg>
              <svg
                v-else
                class="w-10 h-10 text-gray-500"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>

            <!-- File Info -->
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">
                {{ file.name }}
              </p>
              <p class="text-xs text-gray-500">
                {{ formatFileSize(file.size) }} • {{ file.type || 'Unknown type' }}
              </p>
            </div>

            <!-- Remove Button -->
            <button
              type="button"
              @click="removeFile(index)"
              class="flex-shrink-0 text-red-600 hover:text-red-800"
              :disabled="uploading"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path
                  fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Upload Button -->
    <div v-if="selectedFiles.length > 0 && !autoUpload" class="mt-4">
      <button
        type="button"
        @click="uploadFiles"
        :disabled="uploading"
        class="btn-primary"
      >
        <span v-if="!uploading">Upload {{ selectedFiles.length }} File(s)</span>
        <span v-else>Uploading...</span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

interface Props {
  modelType: 'course' | 'activity' | 'lesson' | 'module' | 'report' | 'project' | 'assessment';
  foreignKeyId: number;
  foreignKeyName?: string;
  uploadUrl?: string;
  multiple?: boolean;
  maxFileSize?: number; // in MB
  acceptedTypes?: string;
  autoUpload?: boolean;
  visibility?: 'public' | 'students' | 'instructors' | 'private';
  isRequired?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  foreignKeyName: '',
  uploadUrl: '/api/documents/upload',
  multiple: true,
  maxFileSize: 10,
  acceptedTypes: '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif',
  autoUpload: false,
  visibility: 'students',
  isRequired: false,
});

const emit = defineEmits<{
  (e: 'upload-success', files: any[]): void;
  (e: 'upload-error', error: string): void;
  (e: 'files-selected', files: File[]): void;
}>();

// State
const fileInput = ref<HTMLInputElement | null>(null);
const selectedFiles = ref<File[]>([]);
const isDragging = ref(false);
const uploading = ref(false);
const uploadProgress = ref(0);
const errors = ref<string[]>([]);

// Computed
const maxFileSizeBytes = computed(() => props.maxFileSize * 1024 * 1024);
const maxFileSizeMB = computed(() => props.maxFileSize);

const acceptedTypesText = computed(() => {
  if (props.acceptedTypes === '*') return 'Any file type';
  const types = props.acceptedTypes.split(',').map(t => t.trim().toUpperCase());
  return types.join(', ');
});

const fkName = computed(() => {
  if (props.foreignKeyName) return props.foreignKeyName;
  return `${props.modelType}_id`;
});

// Methods
const triggerFileInput = () => {
  if (!uploading.value) {
    fileInput.value?.click();
  }
};

const handleDragOver = (e: DragEvent) => {
  if (!uploading.value) {
    isDragging.value = true;
  }
};

const handleDragLeave = (e: DragEvent) => {
  isDragging.value = false;
};

const handleDrop = (e: DragEvent) => {
  isDragging.value = false;
  if (uploading.value) return;

  const files = Array.from(e.dataTransfer?.files || []);
  processFiles(files);
};

const handleFileSelect = (e: Event) => {
  const target = e.target as HTMLInputElement;
  const files = Array.from(target.files || []);
  processFiles(files);
};

const processFiles = (files: File[]) => {
  errors.value = [];

  // Validate files
  const validFiles: File[] = [];
  for (const file of files) {
    // Check file size
    if (file.size > maxFileSizeBytes.value) {
      errors.value.push(
        `${file.name} exceeds the maximum file size of ${props.maxFileSize}MB`
      );
      continue;
    }

    // Check file type if not accepting all types
    if (props.acceptedTypes !== '*') {
      const fileExt = '.' + file.name.split('.').pop()?.toLowerCase();
      const acceptedExts = props.acceptedTypes.toLowerCase().split(',').map(t => t.trim());
      
      if (!acceptedExts.includes(fileExt)) {
        errors.value.push(`${file.name} is not an accepted file type`);
        continue;
      }
    }

    validFiles.push(file);
  }

  // Add to selected files
  if (props.multiple) {
    selectedFiles.value = [...selectedFiles.value, ...validFiles];
  } else {
    selectedFiles.value = validFiles.slice(0, 1);
  }

  emit('files-selected', selectedFiles.value);

  // Auto upload if enabled
  if (props.autoUpload && validFiles.length > 0) {
    uploadFiles();
  }
};

const removeFile = (index: number) => {
  selectedFiles.value.splice(index, 1);
};

const uploadFiles = async () => {
  if (selectedFiles.value.length === 0) return;

  uploading.value = true;
  uploadProgress.value = 0;
  errors.value = [];

  try {
    const formData = new FormData();

    // Add model info
    formData.append('model_type', props.modelType);
    formData.append('foreign_key_id', props.foreignKeyId.toString());
    formData.append('foreign_key_name', fkName.value);
    formData.append('visibility', props.visibility);
    formData.append('is_required', props.isRequired ? '1' : '0');

    // Add files
    selectedFiles.value.forEach((file, index) => {
      formData.append(`files[${index}]`, file);
    });

    // Upload using Inertia router
    router.post(props.uploadUrl, formData, {
      forceFormData: true,
      preserveScroll: true,
      onProgress: (progress) => {
        if (progress) {
          uploadProgress.value = Math.round((progress.percentage || 0));
        }
      },
      onSuccess: (page) => {
        emit('upload-success', selectedFiles.value);
        selectedFiles.value = [];
        uploadProgress.value = 0;
        
        // Reset file input
        if (fileInput.value) {
          fileInput.value.value = '';
        }
      },
      onError: (uploadErrors) => {
        const errorMessages = Object.values(uploadErrors).flat() as string[];
        emit('upload-error', errorMessages.join(', '));
        errors.value = errorMessages;
      },
      onFinish: () => {
        uploading.value = false;
      },
    });
  } catch (error: any) {
    uploading.value = false;
    const errorMessage = error.message || 'An error occurred during upload';
    errors.value = [errorMessage];
    emit('upload-error', errorMessage);
  }
};

const isImage = (file: File) => {
  return file.type.startsWith('image/');
};

const isPdf = (file: File) => {
  return file.type === 'application/pdf';
};

const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

// Expose methods for parent component
defineExpose({
  uploadFiles,
  selectedFiles,
});
</script>

<style scoped>
.document-uploader {
  width: 100%;
}

.upload-area {
  position: relative;
  border: 2px dashed #d1d5db;
  border-radius: 0.5rem;
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
}

.upload-area:hover:not(.disabled) {
  border-color: #60a5fa;
  background-color: #eff6ff;
}

.upload-area.drag-over {
  border-color: #3b82f6;
  background-color: #dbeafe;
}

.upload-area.disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

.upload-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.upload-icon {
  width: 3rem;
  height: 3rem;
  color: #9ca3af;
  margin-bottom: 0.75rem;
}

.upload-text {
  font-size: 0.875rem;
  color: #4b5563;
}

.upload-hint {
  font-size: 0.75rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

.file-preview {
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 0.75rem;
  background-color: white;
  transition: background-color 0.2s;
}

.file-preview:hover {
  background-color: #f9fafb;
}

.btn-primary {
  width: 100%;
  padding: 0.5rem 1rem;
  background-color: #2563eb;
  color: white;
  border-radius: 0.5rem;
  border: none;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-primary:hover:not(:disabled) {
  background-color: #1d4ed8;
}

.btn-primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.hidden {
  display: none;
}
</style>
