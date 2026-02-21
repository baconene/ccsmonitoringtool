<script setup lang="ts">
import { Mail, Phone, MapPin } from 'lucide-vue-next';
import EditableField from '@/components/Instructor/EditableField.vue';

interface Props {
  name: string;
  title: string | null;
  email: string;
  phone: string | null;
  officeLocation: string | null;
  employeeId: string | null;
  status: string | null;
}

defineProps<Props>();

const emit = defineEmits<{
  (e: 'update-field', field: string, value: string | number): void;
  (e: 'edit-start', field: string): void;
  (e: 'edit-end', field: string): void;
}>();

const getStatusBadgeColor = (status: string | null) => {
  if (!status) return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
  
  switch (status.toLowerCase()) {
    case 'active':
      return 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200';
    case 'inactive':
      return 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200';
    case 'on leave':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200';
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
  }
};
</script>

<template>
  <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-xl p-8 border border-purple-200 dark:border-purple-800">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
      <div class="flex items-start gap-6">
        <!-- Avatar -->
        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg flex-shrink-0">
          {{ name.charAt(0).toUpperCase() }}
        </div>
        
        <!-- Basic Info -->
        <div class="flex-1 min-w-0">
          <div class="mb-2">
            <EditableField
              label="Name"
              :value="name"
              field="name"
              type="text"
              @update="(field, value) => emit('update-field', field, value)"
              @edit-start="(field) => emit('edit-start', field)"
              @edit-end="(field) => emit('edit-end', field)"
              class="text-3xl font-bold"
            />
          </div>
          
          <div class="mb-3">
            <EditableField
              label="Title"
              :value="title"
              field="title"
              type="text"
              @update="(field, value) => emit('update-field', field, value)"
              @edit-start="(field) => emit('edit-start', field)"
              @edit-end="(field) => emit('edit-end', field)"
              class="text-lg"
            />
          </div>
          
          <div class="space-y-2">
            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
              <Mail class="w-4 h-4 flex-shrink-0" />
              <EditableField
                label=""
                :value="email"
                field="email"
                type="email"
                @update="(field, value) => emit('update-field', field, value)"
                @edit-start="(field) => emit('edit-start', field)"
                @edit-end="(field) => emit('edit-end', field)"
              />
            </div>
            
            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
              <Phone class="w-4 h-4 flex-shrink-0" />
              <EditableField
                label=""
                :value="phone"
                field="phone"
                type="tel"
                @update="(field, value) => emit('update-field', field, value)"
                @edit-start="(field) => emit('edit-start', field)"
                @edit-end="(field) => emit('edit-end', field)"
              />
            </div>
            
            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
              <MapPin class="w-4 h-4 flex-shrink-0" />
              <EditableField
                label=""
                :value="officeLocation"
                field="office_location"
                type="text"
                @update="(field, value) => emit('update-field', field, value)"
                @edit-start="(field) => emit('edit-start', field)"
                @edit-end="(field) => emit('edit-end', field)"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Status Badge -->
      <div class="flex flex-col items-end gap-2 flex-shrink-0">
        <span
          v-if="employeeId"
          class="px-4 py-2 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200 rounded-lg font-mono text-sm"
        >
          {{ employeeId }}
        </span>
        <span
          v-if="status"
          :class="getStatusBadgeColor(status)"
          class="px-4 py-2 rounded-lg text-sm font-semibold"
        >
          {{ status }}
        </span>
      </div>
    </div>
  </div>
</template>
