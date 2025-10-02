<template>
  <div v-if="visible" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
    <div class="bg-white rounded-lg p-6 w-1/3 max-w-md">
      <h3 class="text-lg font-bold mb-4">{{ title }}</h3>
      <p class="mb-4">{{ message }}</p>

      <!-- Error Message -->
      <p v-if="error" class="text-red-600 text-sm mb-2">{{ error }}</p>

      <div class="flex justify-end gap-2">
        <button
          @click="closeModal"
          class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400"
          :disabled="loading"
        >
          Cancel
        </button>
        <button
          @click="confirmRemove"
          class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
          :disabled="loading"
        >
          <span v-if="loading">Removing...</span>
          <span v-else>Remove</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
  visible: boolean;
  moduleId?: number;
  title?: string;
  message?: string;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'removed'): void;
}>();

const loading = ref(false);
const error = ref<string | null>(null);

// Reset error when modal opens
watch(() => props.visible, (val) => {
  if (val) error.value = null;
});

function closeModal() {
  if (!loading.value) {
    emit('close');
  }
}

async function confirmRemove() {
  if (!props.moduleId) {
    error.value = 'Module ID is missing.';
    return;
  }

  loading.value = true;
  error.value = null;

  try {
    await router.delete(`/modules/${props.moduleId}`, {
  onSuccess: () => {
    emit('removed');  // notify parent to reload modules
    closeModal();
  },
  onError: (err: any) => {
    error.value = err?.message || 'Failed to remove module.';
  },
});
  } catch (err: any) {
    error.value = err?.message || 'An unexpected error occurred.';
  } finally {
    loading.value = false;
  }
}
</script>
