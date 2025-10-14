<template>
  <!-- Full Screen Document Viewer Modal -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isOpen"
        class="fixed inset-0 z-50 overflow-hidden bg-black bg-opacity-90"
        @click.self="close"
      >
        <!-- Header -->
        <div class="absolute top-0 left-0 right-0 bg-gray-900 bg-opacity-95 border-b border-gray-700 px-6 py-4 flex items-center justify-between">
          <div class="flex items-center gap-4">
            <FileText class="w-6 h-6 text-indigo-400" />
            <div>
              <h2 class="text-lg font-semibold text-white">{{ document?.name || 'Document Viewer' }}</h2>
              <div class="flex items-center gap-3 text-sm text-gray-400">
                <span v-if="document?.file_size_human">{{ document.file_size_human }}</span>
                <span v-if="document?.extension" class="uppercase">{{ document.extension }}</span>
                <span v-if="document?.uploaded_by">Uploaded by {{ document.uploaded_by }}</span>
              </div>
            </div>
          </div>
          
          <div class="flex items-center gap-2">
            <!-- Download Button -->
            <a
              v-if="document?.id"
              :href="`/documents/${document.id}/download`"
              target="_blank"
              class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors"
              title="Download document"
            >
              <Download class="w-4 h-4" />
              Download
            </a>
            
            <!-- Close Button -->
            <button
              @click="close"
              class="p-2 rounded-lg hover:bg-gray-800 text-gray-400 hover:text-white transition-colors"
              title="Close viewer"
            >
              <X class="w-6 h-6" />
            </button>
          </div>
        </div>

        <!-- Viewer Content -->
        <div class="absolute inset-0 top-20 p-6 overflow-hidden">
          <div class="w-full h-full bg-white rounded-lg shadow-2xl overflow-hidden">
            <!-- Loading State -->
            <div v-if="loading" class="flex flex-col items-center justify-center h-full">
              <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-indigo-600 mb-4"></div>
              <p class="text-gray-600">Loading document...</p>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="flex flex-col items-center justify-center h-full text-center p-8">
              <AlertTriangle class="w-16 h-16 text-red-500 mb-4" />
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Failed to Load Document</h3>
              <p class="text-gray-600 mb-6">{{ error }}</p>
              <button
                @click="close"
                class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors"
              >
                Close Viewer
              </button>
            </div>

            <!-- Office Document Viewer (Word, Excel, PowerPoint, PDF) -->
            <iframe
              v-else-if="viewerUrl"
              :src="viewerUrl"
              class="w-full h-full border-0"
              frameborder="0"
              @load="loading = false"
              @error="handleError"
            ></iframe>

            <!-- Image Viewer -->
            <div
              v-else-if="isImage && documentUrl"
              class="flex items-center justify-center h-full p-8 bg-gray-100"
            >
              <img
                :src="documentUrl"
                :alt="document?.name"
                class="max-w-full max-h-full object-contain"
                @load="loading = false"
                @error="handleError"
              />
            </div>

            <!-- Text File Viewer -->
            <div
              v-else-if="isText"
              class="h-full overflow-auto p-8"
            >
              <pre class="whitespace-pre-wrap font-mono text-sm">{{ textContent }}</pre>
            </div>

            <!-- Unsupported File Type -->
            <div v-else class="flex flex-col items-center justify-center h-full text-center p-8">
              <FileText class="w-16 h-16 text-gray-400 mb-4" />
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Preview Not Available</h3>
              <p class="text-gray-600 mb-6">
                This file type cannot be previewed in the browser.
                <br />
                Please download the file to view it.
              </p>
              <a
                v-if="document?.id"
                :href="`/documents/${document.id}/download`"
                target="_blank"
                class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors"
              >
                <Download class="w-5 h-5" />
                Download File
              </a>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { FileText, Download, X, AlertTriangle } from 'lucide-vue-next';

interface DocumentData {
  id?: number;
  name: string;
  file_path?: string;
  file_size_human?: string;
  mime_type?: string;
  extension?: string;
  uploaded_by?: string;
}

const props = defineProps<{
  isOpen: boolean;
  document: DocumentData | null;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
}>();

// State
const loading = ref(true);
const error = ref<string | null>(null);
const textContent = ref('');

// Computed
const documentUrl = computed(() => {
  if (!props.document?.id) return null;
  return `/documents/${props.document.id}/view`;
});

const isImage = computed(() => {
  const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
  return imageExtensions.includes(props.document?.extension?.toLowerCase() || '');
});

const isText = computed(() => {
  const textExtensions = ['txt', 'csv', 'log', 'md', 'json', 'xml'];
  return textExtensions.includes(props.document?.extension?.toLowerCase() || '');
});

const isPdf = computed(() => {
  return props.document?.extension?.toLowerCase() === 'pdf';
});

const isOfficeDocument = computed(() => {
  const officeExtensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
  return officeExtensions.includes(props.document?.extension?.toLowerCase() || '');
});

const viewerUrl = computed(() => {
  if (!documentUrl.value) return null;
  
  // PDF files - use browser's native PDF viewer via Laravel route
  if (isPdf.value) {
    return documentUrl.value;
  }
  
  // Office documents - try Microsoft Office Online Viewer
  // Note: This requires the file to be publicly accessible via direct URL
  // If symlink is not working, this will fail gracefully and show download option
  if (isOfficeDocument.value) {
    // Use the Laravel route instead of storage path for better compatibility
    const encodedUrl = encodeURIComponent(window.location.origin + documentUrl.value);
    return `https://view.officeapps.live.com/op/embed.aspx?src=${encodedUrl}`;
  }
  
  return null;
});

// Methods
function close() {
  emit('close');
}

function handleError() {
  loading.value = false;
  error.value = 'Failed to load the document. The file may be corrupted or the format is not supported.';
}

async function loadTextContent() {
  if (!isText.value || !documentUrl.value) return;
  
  loading.value = true;
  error.value = null;
  
  try {
    const response = await fetch(documentUrl.value);
    if (!response.ok) throw new Error('Failed to load text file');
    textContent.value = await response.text();
  } catch (err) {
    error.value = 'Failed to load text content';
  } finally {
    loading.value = false;
  }
}

// Watch for document changes
watch(
  () => props.isOpen,
  (newValue) => {
    if (newValue) {
      loading.value = true;
      error.value = null;
      textContent.value = '';
      
      if (isText.value) {
        loadTextContent();
      }
    }
  }
);

// Handle ESC key to close
watch(
  () => props.isOpen,
  (isOpen) => {
    if (isOpen) {
      const handleEscape = (e: KeyboardEvent) => {
        if (e.key === 'Escape') close();
      };
      document.addEventListener('keydown', handleEscape);
      return () => document.removeEventListener('keydown', handleEscape);
    }
  }
);
</script>
