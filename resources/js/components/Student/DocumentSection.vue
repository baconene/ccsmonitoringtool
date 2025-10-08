<template>
  <div v-if="documents.length > 0" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="p-6">
      <div class="flex items-center mb-6">
        <FileText class="h-6 w-6 text-amber-600 dark:text-amber-400 mr-3" />
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
          {{ title }} ({{ documents.length }})
        </h2>
      </div>

      <div class="space-y-3">
        <div 
          v-for="document in documents" 
          :key="document.id"
          class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
        >
          <div class="flex items-center flex-1">
            <div class="flex-shrink-0 mr-4">
              <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                <component :is="getDocumentIcon(document.doc_type)" class="h-5 w-5 text-amber-600 dark:text-amber-400" />
              </div>
            </div>
            <div class="flex-1">
              <h3 class="font-medium text-gray-900 dark:text-gray-100">
                {{ document.name }}
              </h3>
              <div class="flex items-center mt-1 text-sm text-gray-500 dark:text-gray-400">
                <span class="capitalize">{{ document.doc_type }}</span>
                <span class="mx-2">•</span>
                <span>{{ getFileExtension(document.file_path) }}</span>
              </div>
            </div>
          </div>

          <div class="flex-shrink-0 ml-4">
            <div class="flex gap-2">
              <button
                @click="viewDocument(document)"
                class="px-3 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors flex items-center"
              >
                <Eye class="h-4 w-4 mr-1" />
                View
              </button>
              <button
                @click="downloadDocument(document)"
                class="px-3 py-2 text-sm font-medium bg-gray-600 hover:bg-gray-700 text-white rounded-md transition-colors flex items-center"
              >
                <Download class="h-4 w-4 mr-1" />
                Download
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { 
  FileText, 
  Eye, 
  Download, 
  File, 
  FileImage, 
  FileVideo, 
  FileAudio,
  Presentation
} from 'lucide-vue-next';

interface Document {
  id: number;
  name: string;
  file_path: string;
  doc_type: string;
}

const props = defineProps<{
  documents: Document[];
  title: string;
}>();

const getDocumentIcon = (docType: string) => {
  switch (docType.toLowerCase()) {
    case 'pdf':
    case 'document':
      return FileText;
    case 'image':
      return FileImage;
    case 'video':
      return FileVideo;
    case 'audio':
      return FileAudio;
    case 'presentation':
      return Presentation;
    default:
      return File;
  }
};

const getFileExtension = (filePath: string) => {
  return filePath.split('.').pop()?.toUpperCase() || 'FILE';
};

const viewDocument = (document: Document) => {
  // Open document in new tab/window
  window.open(`/storage/${document.file_path}`, '_blank');
};

const downloadDocument = (document: Document) => {
  // Create download link
  const link = window.document.createElement('a');
  link.href = `/storage/${document.file_path}`;
  link.download = document.name;
  link.click();
};
</script>