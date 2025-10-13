<script setup lang="ts">
import { GraduationCap, Award } from 'lucide-vue-next';
import EditableField from '@/components/Instructor/EditableField.vue';
import { computed } from 'vue';

interface Props {
  educationLevel: string | null;
  certifications: any;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'update-field', field: string, value: string | number): void;
}>();

const certificationsArray = computed(() => {
  if (!props.certifications) return [];
  
  if (Array.isArray(props.certifications)) {
    return props.certifications;
  }
  
  if (typeof props.certifications === 'object') {
    return Object.values(props.certifications);
  }
  
  return [];
});
</script>

<template>
  <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-xl p-8 border border-purple-200 dark:border-purple-800">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-3">
      <div class="p-2 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-lg">
        <GraduationCap class="w-6 h-6 text-white" />
      </div>
      Education & Certifications
    </h2>

    <div class="space-y-6">
      <!-- Education Level -->
      <div class="space-y-2">
        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
          <GraduationCap class="w-4 h-4" />
          <span class="text-sm font-medium">Education Level</span>
        </div>
        <EditableField
          label=""
          :value="educationLevel"
          field="education_level"
          type="text"
          @update="emit('update-field', $event, $event)"
        />
      </div>

      <!-- Certifications -->
      <div class="space-y-3">
        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
          <Award class="w-4 h-4" />
          <span class="text-sm font-medium">Certifications</span>
        </div>
        
        <div v-if="certificationsArray.length > 0" class="space-y-2">
          <div
            v-for="(cert, index) in certificationsArray"
            :key="index"
            class="px-4 py-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800"
          >
            <p class="text-gray-900 dark:text-gray-100">{{ cert }}</p>
          </div>
        </div>
        
        <div v-else class="px-4 py-3 bg-gray-50 dark:bg-gray-900/20 rounded-lg border border-gray-200 dark:border-gray-700">
          <p class="text-gray-500 dark:text-gray-400 text-sm">No certifications listed</p>
        </div>
      </div>
    </div>
  </div>
</template>
