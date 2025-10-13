<script setup lang="ts">
import { ref, computed } from 'vue';
import { Pencil, Check, X } from 'lucide-vue-next';

interface Props {
  label: string;
  value: string | number | null;
  field: string;
  type?: 'text' | 'email' | 'tel' | 'number' | 'date' | 'textarea';
  icon?: any;
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text'
});

const emit = defineEmits<{
  (e: 'update', field: string, value: string | number): void;
}>();

const isEditing = ref(false);
const editValue = ref<string | number>('');

const displayValue = computed(() => {
  if (props.value === null || props.value === undefined || props.value === '') {
    return 'N/A';
  }
  return props.value;
});

const startEditing = () => {
  editValue.value = props.value || '';
  isEditing.value = true;
};

const saveEdit = () => {
  if (editValue.value !== props.value) {
    emit('update', props.field, editValue.value);
  }
  isEditing.value = false;
};

const cancelEdit = () => {
  isEditing.value = false;
  editValue.value = '';
};
</script>

<template>
  <div>
    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 flex items-center gap-2">
      <component v-if="icon" :is="icon" class="w-4 h-4" />
      {{ label }}
    </label>
    
    <div v-if="!isEditing" class="group relative">
      <p 
        @click="startEditing"
        class="text-gray-900 dark:text-white font-medium cursor-pointer hover:text-purple-600 dark:hover:text-purple-400 transition-colors pr-8"
        :class="{ 'text-gray-400 dark:text-gray-600 italic': displayValue === 'N/A' }"
      >
        {{ displayValue }}
      </p>
      <button
        @click="startEditing"
        class="absolute right-0 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity text-gray-400 hover:text-purple-600 dark:hover:text-purple-400"
        title="Edit"
      >
        <Pencil class="w-4 h-4" />
      </button>
    </div>

    <div v-else class="flex items-center gap-2">
      <textarea
        v-if="type === 'textarea'"
        v-model="editValue"
        @keydown.esc="cancelEdit"
        class="flex-1 px-3 py-2 border border-purple-300 dark:border-purple-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors resize-none"
        rows="3"
        autofocus
      />
      <input
        v-else
        v-model="editValue"
        :type="type"
        @keydown.enter="saveEdit"
        @keydown.esc="cancelEdit"
        class="flex-1 px-3 py-2 border border-purple-300 dark:border-purple-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
        autofocus
      />
      <button
        @click="saveEdit"
        class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors"
        title="Save"
      >
        <Check class="w-4 h-4" />
      </button>
      <button
        @click="cancelEdit"
        class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
        title="Cancel"
      >
        <X class="w-4 h-4" />
      </button>
    </div>
  </div>
</template>
