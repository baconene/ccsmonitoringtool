<template>
  <div class="space-y-4">
    <div class="flex items-center gap-2 mb-3">
      <FileText class="w-5 h-5 text-purple-600 dark:text-purple-400" />
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Module Documents</h3>
      <span v-if="normalizedDocuments && normalizedDocuments.length > 0" class="text-sm text-gray-500 dark:text-gray-400">
        ({{ normalizedDocuments.length }})
      </span>
    </div>

    <!-- Use RelatedDocumentContainer Component -->
    <RelatedDocumentContainer
      :model-value="normalizedDocuments"
      model-type="module"
      :foreign-key-id="moduleId"
      :max-file-size="20"
      accepted-types=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.gif"
      visibility="students"
      :is-required="false"
      @document-uploaded="handleDocumentUploaded"
      @document-deleted="handleDocumentDeleted"
    />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { FileText } from "lucide-vue-next";
import RelatedDocumentContainer from '@/course/RelatedDocumentContainer.vue';

interface ModuleDocument {
  id: number;
  document_id: number;
  module_id: number;
  visibility: string;
  is_required: boolean;
  order?: number;
  document?: {
    id: number;
    name: string;
    original_name: string;
    file_path: string;
    file_size: number;
    file_size_human: string;
    mime_type: string;
    extension: string;
    document_type: string;
    uploaded_by: number;
    uploader?: {
      id: number;
      name: string;
      email: string;
    };
    created_at: string;
    updated_at: string;
  };
}

const props = defineProps<{
  documents?: ModuleDocument[];
  moduleId: number;
}>();

const emit = defineEmits<{
  (e: 'uploaded'): void;
  (e: 'deleted'): void;
}>();

// Normalize module documents to the format expected by RelatedDocumentContainer
const normalizedDocuments = computed(() => {
  if (!props.documents) return [];
  
  return props.documents.map(doc => ({
    id: doc.document?.id,
    name: doc.document?.name || 'Untitled Document',
    doc_type: doc.document?.document_type || 'document',
    file_path: doc.document?.file_path || '',
    file_size: doc.document?.file_size,
    file_size_human: doc.document?.file_size_human,
    mime_type: doc.document?.mime_type,
    extension: doc.document?.extension,
    uploaded_by: doc.document?.uploader?.name,
    created_at: doc.document?.created_at,
  }));
});

function handleDocumentUploaded(files: File[]) {
  emit('uploaded');
  // Reload courses data to get updated module documents
  router.reload({
    only: ['courses'],
  });
}

function handleDocumentDeleted(documentId: number) {
  emit('deleted');
  // Reload courses data to get updated module documents
  router.reload({
    only: ['courses'],
  });
}
</script>
