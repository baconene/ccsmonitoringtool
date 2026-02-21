<script setup lang="ts">
import { Briefcase, Building, Calendar, Clock, Award } from 'lucide-vue-next';
import EditableField from '@/components/Instructor/EditableField.vue';
import { computed } from 'vue';

interface Props {
  department: string | null;
  specialization: string | null;
  employmentType: string | null;
  hireDate: string | null;
  yearsOfExperience: number | null;
  officeHours: string | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'update-field', field: string, value: string | number): void;
  (e: 'edit-start', field: string): void;
  (e: 'edit-end', field: string): void;
}>();

const formattedHireDate = computed(() => {
  if (!props.hireDate) return null;
  return new Date(props.hireDate).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
});
</script>

<template>
  <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-xl p-8 border border-purple-200 dark:border-purple-800">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-3">
      <div class="p-2 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-lg">
        <Briefcase class="w-6 h-6 text-white" />
      </div>
      Professional Information
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Department -->
      <div class="space-y-2">
        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
          <Building class="w-4 h-4" />
          <span class="text-sm font-medium">Department</span>
        </div>
        <EditableField
          label=""
          :value="department"
          field="department"
          type="text"
          @update="(field, value) => emit('update-field', field, value)"
          @edit-start="(field) => emit('edit-start', field)"
          @edit-end="(field) => emit('edit-end', field)"
        />
      </div>

      <!-- Specialization -->
      <div class="space-y-2">
        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
          <Award class="w-4 h-4" />
          <span class="text-sm font-medium">Specialization</span>
        </div>
        <EditableField
          label=""
          :value="specialization"
          field="specialization"
          type="text"
          @update="(field, value) => emit('update-field', field, value)"
          @edit-start="(field) => emit('edit-start', field)"
          @edit-end="(field) => emit('edit-end', field)"
        />
      </div>

      <!-- Employment Type -->
      <div class="space-y-2">
        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
          <Briefcase class="w-4 h-4" />
          <span class="text-sm font-medium">Employment Type</span>
        </div>
        <EditableField
          label=""
          :value="employmentType"
          field="employment_type"
          type="text"
          @update="(field, value) => emit('update-field', field, value)"
          @edit-start="(field) => emit('edit-start', field)"
          @edit-end="(field) => emit('edit-end', field)"
        />
      </div>

      <!-- Hire Date -->
      <div class="space-y-2">
        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
          <Calendar class="w-4 h-4" />
          <span class="text-sm font-medium">Hire Date</span>
        </div>
        <EditableField
          label=""
          :value="hireDate"
          field="hire_date"
          type="date"
          @update="(field, value) => emit('update-field', field, value)"
          @edit-start="(field) => emit('edit-start', field)"
          @edit-end="(field) => emit('edit-end', field)"
        />
        <p v-if="formattedHireDate" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
          {{ formattedHireDate }}
        </p>
      </div>

      <!-- Years of Experience -->
      <div class="space-y-2">
        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
          <Award class="w-4 h-4" />
          <span class="text-sm font-medium">Years of Experience</span>
        </div>
        <EditableField
          label=""
          :value="yearsOfExperience"
          field="years_experience"
          type="number"
          @update="(field, value) => emit('update-field', field, value)"
          @edit-start="(field) => emit('edit-start', field)"
          @edit-end="(field) => emit('edit-end', field)"
        />
      </div>

      <!-- Office Hours -->
      <div class="space-y-2">
        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
          <Clock class="w-4 h-4" />
          <span class="text-sm font-medium">Office Hours</span>
        </div>
        <EditableField
          label=""
          :value="officeHours"
          field="office_hours"
          type="textarea"
          @update="(field, value) => emit('update-field', field, value)"
          @edit-start="(field) => emit('edit-start', field)"
          @edit-end="(field) => emit('edit-end', field)"
        />
      </div>
    </div>
  </div>
</template>
