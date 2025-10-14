<template>
  <div class="course-file-upload-section">
    <div class="section-header">
      <h2>Course Documents</h2>
      <p class="section-description">
        Upload course materials, syllabus, lecture notes, and other resources
      </p>
    </div>

    <!-- Upload Component -->
    <div class="upload-container">
      <DocumentUploader
        ref="uploaderRef"
        model-type="course"
        :foreign-key-id="course.id"
        :multiple="true"
        :max-file-size="20"
        accepted-types=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.jpg,.jpeg,.png"
        upload-url="/api/courses/documents/upload"
        :visibility="selectedVisibility"
        :is-required="isRequiredDocument"
        @upload-success="handleUploadSuccess"
        @upload-error="handleUploadError"
        @files-selected="handleFilesSelected"
      />

      <!-- Upload Options -->
      <div class="upload-options">
        <div class="option-group">
          <label for="visibility" class="option-label">
            Visibility
          </label>
          <select
            id="visibility"
            v-model="selectedVisibility"
            class="option-select"
          >
            <option value="public">Public</option>
            <option value="students">Students Only</option>
            <option value="instructors">Instructors Only</option>
            <option value="private">Private</option>
          </select>
        </div>

        <div class="option-group">
          <label class="option-checkbox">
            <input
              type="checkbox"
              v-model="isRequiredDocument"
            />
            <span>Mark as required</span>
          </label>
        </div>
      </div>
    </div>

    <!-- Uploaded Documents List -->
    <div v-if="documents.length > 0" class="documents-list">
      <div class="list-header">
        <h3>Uploaded Documents ({{ documents.length }})</h3>
        <button
          v-if="selectedDocuments.length > 0"
          @click="handleBulkDelete"
          class="btn-danger"
        >
          Delete Selected ({{ selectedDocuments.length }})
        </button>
      </div>

      <div class="table-container">
        <table class="documents-table">
          <thead>
            <tr>
              <th class="w-8">
                <input
                  type="checkbox"
                  :checked="allSelected"
                  @change="toggleSelectAll"
                />
              </th>
              <th>Document Name</th>
              <th>Type</th>
              <th>Size</th>
              <th>Visibility</th>
              <th>Required</th>
              <th>Uploaded By</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="doc in documents"
              :key="doc.id"
              :class="{ 'selected': selectedDocuments.includes(doc.id) }"
            >
              <td>
                <input
                  type="checkbox"
                  :checked="selectedDocuments.includes(doc.id)"
                  @change="toggleDocumentSelection(doc.id)"
                />
              </td>
              <td>
                <div class="document-name">
                  <component :is="getFileIcon(doc.extension)" class="file-icon" />
                  <span>{{ doc.name }}</span>
                </div>
              </td>
              <td>
                <span class="badge">{{ doc.extension.toUpperCase() }}</span>
              </td>
              <td>{{ doc.file_size_human }}</td>
              <td>
                <span class="visibility-badge" :class="`visibility-${doc.course_document?.visibility}`">
                  {{ doc.course_document?.visibility }}
                </span>
              </td>
              <td>
                <span v-if="doc.course_document?.is_required" class="text-green-600">
                  ✓ Yes
                </span>
                <span v-else class="text-gray-400">
                  No
                </span>
              </td>
              <td>{{ doc.uploader?.name || 'Unknown' }}</td>
              <td>{{ formatDate(doc.created_at) }}</td>
              <td>
                <div class="action-buttons">
                  <button
                    @click="downloadDocument(doc)"
                    class="btn-icon"
                    title="Download"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                  </button>
                  <button
                    @click="deleteDocument(doc)"
                    class="btn-icon text-red-600 hover:text-red-800"
                    title="Delete"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="empty-state">
      <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <p class="empty-text">No documents uploaded yet</p>
      <p class="empty-hint">Upload course materials using the form above</p>
    </div>

    <!-- Success/Error Messages -->
    <div v-if="message" :class="messageClass" class="message">
      {{ message }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import DocumentUploader from './DocumentUploader.vue';
import type { Document, CourseDocument, VisibilityType } from './types';

interface Props {
  course: {
    id: number;
    title: string;
    code: string;
  };
  documents: Array<Document & { course_document?: CourseDocument }>;
}

const props = defineProps<Props>();

// State
const uploaderRef = ref<InstanceType<typeof DocumentUploader> | null>(null);
const selectedVisibility = ref<VisibilityType>('students');
const isRequiredDocument = ref(false);
const selectedDocuments = ref<number[]>([]);
const message = ref('');
const messageType = ref<'success' | 'error'>('success');

// Computed
const allSelected = computed(() => {
  return props.documents.length > 0 && 
         selectedDocuments.value.length === props.documents.length;
});

const messageClass = computed(() => {
  return messageType.value === 'success' 
    ? 'message-success' 
    : 'message-error';
});

// Methods
const handleUploadSuccess = (files: File[]) => {
  message.value = `Successfully uploaded ${files.length} file(s)`;
  messageType.value = 'success';
  
  // Clear message after 5 seconds
  setTimeout(() => {
    message.value = '';
  }, 5000);
  
  // Reload page to show new documents
  router.reload({ only: ['documents'] });
};

const handleUploadError = (error: string) => {
  message.value = error;
  messageType.value = 'error';
  
  setTimeout(() => {
    message.value = '';
  }, 5000);
};

const handleFilesSelected = (files: File[]) => {
  console.log('Files selected:', files.map(f => f.name));
};

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedDocuments.value = [];
  } else {
    selectedDocuments.value = props.documents.map(doc => doc.id);
  }
};

const toggleDocumentSelection = (docId: number) => {
  const index = selectedDocuments.value.indexOf(docId);
  if (index > -1) {
    selectedDocuments.value.splice(index, 1);
  } else {
    selectedDocuments.value.push(docId);
  }
};

const downloadDocument = (doc: Document) => {
  window.open(`/api/documents/${doc.id}/download`, '_blank');
};

const deleteDocument = (doc: Document) => {
  if (confirm(`Are you sure you want to delete "${doc.name}"?`)) {
    router.delete(`/api/documents/${doc.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        message.value = 'Document deleted successfully';
        messageType.value = 'success';
      },
      onError: () => {
        message.value = 'Failed to delete document';
        messageType.value = 'error';
      },
    });
  }
};

const handleBulkDelete = () => {
  if (confirm(`Delete ${selectedDocuments.value.length} selected document(s)?`)) {
    router.delete('/api/documents/bulk-delete', {
      data: { document_ids: selectedDocuments.value },
      preserveScroll: true,
      onSuccess: () => {
        message.value = `${selectedDocuments.value.length} document(s) deleted`;
        messageType.value = 'success';
        selectedDocuments.value = [];
      },
      onError: () => {
        message.value = 'Failed to delete documents';
        messageType.value = 'error';
      },
    });
  }
};

const getFileIcon = (extension: string) => {
  // Return appropriate icon component based on extension
  return 'div'; // Placeholder
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>

<style scoped>
.course-file-upload-section {
  padding: 1.5rem;
  background: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.section-header h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.5rem;
}

.section-description {
  color: #6b7280;
  font-size: 0.875rem;
}

.upload-container {
  margin-top: 1.5rem;
}

.upload-options {
  margin-top: 1rem;
  display: flex;
  gap: 1.5rem;
  align-items: center;
}

.option-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.option-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
}

.option-select {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
}

.option-checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-size: 0.875rem;
}

.documents-list {
  margin-top: 2rem;
}

.list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.list-header h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
}

.btn-danger {
  padding: 0.5rem 1rem;
  background-color: #dc2626;
  color: white;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  border: none;
  cursor: pointer;
}

.btn-danger:hover {
  background-color: #b91c1c;
}

.table-container {
  overflow-x: auto;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
}

.documents-table {
  width: 100%;
  border-collapse: collapse;
}

.documents-table th {
  background-color: #f9fafb;
  padding: 0.75rem 1rem;
  text-align: left;
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.documents-table td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #e5e7eb;
  font-size: 0.875rem;
}

.documents-table tr:hover {
  background-color: #f9fafb;
}

.documents-table tr.selected {
  background-color: #eff6ff;
}

.document-name {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.file-icon {
  width: 1.25rem;
  height: 1.25rem;
  flex-shrink: 0;
}

.badge {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  background-color: #e5e7eb;
  color: #374151;
  border-radius: 0.25rem;
  font-size: 0.75rem;
  font-weight: 500;
}

.visibility-badge {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.75rem;
  font-weight: 500;
}

.visibility-public {
  background-color: #dbeafe;
  color: #1e40af;
}

.visibility-students {
  background-color: #d1fae5;
  color: #065f46;
}

.visibility-instructors {
  background-color: #fef3c7;
  color: #92400e;
}

.visibility-private {
  background-color: #f3f4f6;
  color: #374151;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-icon {
  padding: 0.25rem;
  background: none;
  border: none;
  cursor: pointer;
  color: #6b7280;
}

.btn-icon:hover {
  color: #111827;
}

.empty-state {
  text-align: center;
  padding: 3rem;
}

.empty-icon {
  width: 4rem;
  height: 4rem;
  color: #d1d5db;
  margin: 0 auto 1rem;
}

.empty-text {
  font-size: 1.125rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.empty-hint {
  font-size: 0.875rem;
  color: #6b7280;
}

.message {
  margin-top: 1rem;
  padding: 0.75rem 1rem;
  border-radius: 0.375rem;
  font-size: 0.875rem;
}

.message-success {
  background-color: #d1fae5;
  color: #065f46;
  border: 1px solid #10b981;
}

.message-error {
  background-color: #fee2e2;
  color: #991b1b;
  border: 1px solid #ef4444;
}

.w-8 {
  width: 2rem;
}
</style>
