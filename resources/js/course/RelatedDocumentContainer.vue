<template>
  <div class="border border-gray-200 dark:border-gray-600 rounded p-4 w-full bg-white dark:bg-gray-800">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <h2 class="font-semibold text-gray-800 dark:text-gray-200">Course Documents</h2>
      <button
        @click="showUploader = !showUploader"
        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors shadow-sm hover:shadow-md"
      >
        <svg v-if="!showUploader" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        {{ showUploader ? 'Hide Uploader' : 'Upload Files' }}
      </button>
    </div>

    <!-- Document Uploader -->
    <div v-if="showUploader" class="mb-4">
      <DocumentUploader
        :model-type="modelType"
        :foreign-key-id="foreignKeyId"
        :multiple="true"
        :max-file-size="maxFileSize"
        :accepted-types="acceptedTypes"
        :visibility="visibility"
        :is-required="isRequired"
        @upload-success="handleUploadSuccess"
        @upload-error="handleUploadError"
        @files-selected="handleFilesSelected"
      />
    </div>

    <!-- Success/Error Messages -->
    <div v-if="message" :class="messageClass" class="mb-4 p-3 rounded text-sm">
      {{ message }}
    </div>

    <!-- Document List -->
    <div class="space-y-2">
      <div
        v-for="(doc, index) in validDocuments"
        :key="doc.id || index"
        class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors group cursor-pointer"
        @click="openViewer(doc)"
      >
        <!-- Left: Icon + Name (Clickable) -->
        <div class="flex items-center space-x-3 flex-1 min-w-0">
          <FileText class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" />
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
              {{ doc.name }}
            </p>
            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
              <span v-if="doc.file_size_human">{{ doc.file_size_human }}</span>
              <span v-if="doc.extension" class="uppercase">{{ doc.extension }}</span>
              <span v-if="doc.doc_type" class="capitalize">{{ doc.doc_type }}</span>
            </div>
          </div>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-2 ml-4">
          <!-- View Button -->
          <button
            v-if="doc.id"
            @click.stop="openViewer(doc)"
            class="p-2 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors group/view"
            title="View document"
          >
            <Eye class="w-4 h-4 text-gray-600 dark:text-gray-400 group-hover/view:text-indigo-600 dark:group-hover/view:text-indigo-400 transition-colors" />
          </button>

          <!-- Download Button -->
          <a
            v-if="doc.id"
            :href="`/documents/${doc.id}/download`"
            target="_blank"
            @click.stop
            class="p-2 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors group/download"
            title="Download document"
          >
            <Download class="w-4 h-4 text-gray-600 dark:text-gray-400 group-hover/download:text-blue-600 dark:group-hover/download:text-blue-400 transition-colors" />
          </a>

          <!-- Delete Button -->
          <button
            @click.stop="confirmDelete(index, doc)"
            class="p-2 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors group/delete"
            title="Delete document"
          >
            <Trash2 class="w-4 h-4 text-gray-600 dark:text-gray-400 group-hover/delete:text-red-600 dark:group-hover/delete:text-red-400 transition-colors" />
          </button>
        </div>
      </div>

      <p v-if="validDocuments.length === 0" class="text-sm text-gray-400 dark:text-gray-500 italic text-center py-4">
        No documents uploaded yet. Click "Upload Files" to add documents.
      </p>
    </div>

    <!-- Document Viewer -->
    <DocumentViewer
      :is-open="showViewer"
      :document="selectedDocument"
      @close="closeViewer"
    />

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="showDeleteModal = false"
    >
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex items-start gap-4">
          <div class="flex-shrink-0 w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
            <AlertTriangle class="w-6 h-6 text-red-600 dark:text-red-400" />
          </div>
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Delete Document</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
              Are you sure you want to delete "<strong>{{ documentToDelete?.doc.name }}</strong>"? This action cannot be undone.
            </p>
            <div class="flex justify-end gap-3">
              <button
                @click="showDeleteModal = false"
                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors"
              >
                Cancel
              </button>
              <button
                @click="executeDelete"
                :disabled="isDeleting"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 disabled:bg-red-400 disabled:cursor-not-allowed rounded-lg transition-colors"
              >
                {{ isDeleting ? 'Deleting...' : 'Delete' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed } from "vue";
import { router } from "@inertiajs/vue3";
import { FileText, Download, Trash2, AlertTriangle, Eye } from "lucide-vue-next";
import DocumentUploader from "../document/DocumentUploader.vue";
import DocumentViewer from "../components/DocumentViewer.vue";

interface DocumentData {
  id?: number;
  name: string;
  doc_type?: string;
  file_path?: string;
  file_size?: number;
  file_size_human?: string;
  mime_type?: string;
  extension?: string;
  uploaded_by?: string;
  created_at?: string;
}

const props = withDefaults(defineProps<{
  modelValue?: DocumentData[];
  modelType: 'course' | 'activity' | 'lesson' | 'module' | 'report' | 'project' | 'assessment';
  foreignKeyId: number;
  maxFileSize?: number;
  acceptedTypes?: string;
  visibility?: 'public' | 'students' | 'instructors' | 'private';
  isRequired?: boolean;
}>(), {
  modelValue: () => [],
  maxFileSize: 20,
  acceptedTypes: '.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.gif',
  visibility: 'students',
  isRequired: false,
});

const emit = defineEmits<{
  (e: "update:modelValue", value: DocumentData[]): void;
  (e: "document-uploaded", files: File[]): void;
  (e: "document-deleted", documentId: number): void;
}>();

// State
const documents = ref<DocumentData[]>([...(props.modelValue || [])]);
const showUploader = ref(false);
const message = ref('');
const messageType = ref<'success' | 'error'>('success');
const showDeleteModal = ref(false);
const documentToDelete = ref<{ index: number; doc: DocumentData } | null>(null);
const isDeleting = ref(false);
const showViewer = ref(false);
const selectedDocument = ref<DocumentData | null>(null);

// Computed
const messageClass = computed(() => {
  return messageType.value === 'success'
    ? 'bg-green-100 text-green-800 border border-green-300 dark:bg-green-900 dark:text-green-200'
    : 'bg-red-100 text-red-800 border border-red-300 dark:bg-red-900 dark:text-red-200';
});

// Filter out invalid/deleted documents (those without proper name or id)
const validDocuments = computed(() => {
  return documents.value.filter(doc => 
    doc.id && 
    doc.name && 
    doc.name !== 'Untitled Document' &&
    doc.name.trim() !== ''
  );
});

// Watch for external changes
watch(
  () => props.modelValue,
  (newVal) => {
    console.log('🔍 RelatedDocumentContainer watch triggered:', {
      modelType: props.modelType,
      foreignKeyId: props.foreignKeyId,
      newVal,
      isArray: Array.isArray(newVal),
      length: newVal?.length
    });
    
    // Handle both flattened and nested document structures
    if (newVal && Array.isArray(newVal)) {
      documents.value = newVal.map((item: any) => {
        // If item has a nested 'document' property (pivot structure), flatten it
        if (item.document && typeof item.document === 'object') {
          console.log('🔄 Flattening nested document structure:', item);
          const doc = item.document;
          return {
            id: doc.id,
            name: doc.name || doc.original_name,
            original_name: doc.original_name,
            file_path: doc.file_path,
            file_size: doc.file_size,
            file_size_human: doc.file_size_human,
            mime_type: doc.mime_type,
            extension: doc.extension,
            document_type: doc.document_type,
            doc_type: doc.document_type || doc.doc_type,
            uploaded_by: doc.uploaded_by || (doc.uploader ? doc.uploader.name : 'Unknown'),
            visibility: item.visibility,
            is_required: item.is_required,
          };
        }
        // Already flattened structure, use as-is
        return item;
      }).filter((doc: any) => doc.id); // Filter out items without valid document
    } else {
      documents.value = [];
    }
    
    console.log('📄 documents.value after update:', documents.value);
  },
  { deep: true, immediate: true }
);

// Methods
function handleUploadSuccess(files: File[]) {
  message.value = `Successfully uploaded ${files.length} file(s)`;
  messageType.value = 'success';
  showUploader.value = false;
  
  // Clear message after 5 seconds
  setTimeout(() => {
    message.value = '';
  }, 5000);
  
  // Emit event
  emit('document-uploaded', files);
  
  // Reload to get updated documents list while preserving state
  router.reload({
    only: ['courses'], // Only reload courses data
  });
}

function handleUploadError(error: string) {
  message.value = error;
  messageType.value = 'error';
  
  setTimeout(() => {
    message.value = '';
  }, 5000);
}

function handleFilesSelected(files: File[]) {
  console.log('Files selected:', files.map(f => f.name));
}

// Show delete confirmation modal
function confirmDelete(index: number, doc: DocumentData) {
  documentToDelete.value = { index, doc };
  showDeleteModal.value = true;
}

// Execute deletion
function executeDelete() {
  if (!documentToDelete.value) return;
  
  const { index, doc } = documentToDelete.value;
  
  if (!doc.id) {
    // Just remove from local array if no ID
    documents.value.splice(index, 1);
    emit("update:modelValue", documents.value);
    showDeleteModal.value = false;
    documentToDelete.value = null;
    return;
  }

  isDeleting.value = true;

  // Delete from server
  router.delete(`/documents/${doc.id}`, {
    onSuccess: () => {
      documents.value.splice(index, 1);
      emit("update:modelValue", documents.value);
      emit("document-deleted", doc.id!);
      
      message.value = 'Document deleted successfully';
      messageType.value = 'success';
      
      setTimeout(() => {
        message.value = '';
      }, 3000);
      
      showDeleteModal.value = false;
      documentToDelete.value = null;
      isDeleting.value = false;
      
      // Reload to get updated documents list
      router.reload({
        only: ['courses'], // Only reload courses data
      });
    },
    onError: (errors) => {
      message.value = 'Failed to delete document';
      messageType.value = 'error';
      
      setTimeout(() => {
        message.value = '';
      }, 5000);
      
      isDeleting.value = false;
    },
    onFinish: () => {
      isDeleting.value = false;
    }
  });
}

// Open document viewer
function openViewer(doc: DocumentData) {
  selectedDocument.value = doc;
  showViewer.value = true;
}

// Close document viewer
function closeViewer() {
  showViewer.value = false;
  selectedDocument.value = null;
}
</script>
