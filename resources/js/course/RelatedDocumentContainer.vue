<template>
  <div class="border border-gray-200 dark:border-gray-600 rounded p-4 w-full max-w-md bg-white dark:bg-gray-800">
    <!-- Header -->
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold text-gray-800 dark:text-gray-200">Related Document</h2>
      <button
        @click="addDocument"
        class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-bold text-lg transition-colors"
      >
        +
      </button>
    </div>

    <!-- Document List -->
    <div class="space-y-2">
      <RelatedDocumentItem
        v-for="(doc, index) in documents"
        :key="index"
        :doc="doc"
        @remove="removeDocument(index)"
      />

      <p v-if="documents.length === 0" class="text-sm text-gray-400 dark:text-gray-500 italic">
        No related documents yet
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from "vue";  
import RelatedDocumentItem from "./RelatedDocumentItem.vue";

const props = defineProps<{
  modelValue: { name: string; doc_type: string }[];
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: { name: string; doc_type: string }[]): void;
}>();

// local state
const documents = ref([...props.modelValue]);

// keep in sync if parent updates
watch(
  () => props.modelValue,
  (newVal) => {
    documents.value = [...newVal];
  }
);

function addDocument() {
  const newDoc = {
    name: `Document ${documents.value.length + 1}`,
    doc_type: "pdf",
  };
  documents.value.push(newDoc);
  emit("update:modelValue", documents.value);
}

function removeDocument(index: number) {
  documents.value.splice(index, 1);
  emit("update:modelValue", documents.value);
}
</script>
