<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types/index';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';
import HeaderCard from '@/components/Instructor/HeaderCard.vue';
import ProfessionalInfoCard from '@/components/Instructor/ProfessionalInfoCard.vue';
import EducationCertificationsCard from '@/components/Instructor/EducationCertificationsCard.vue';
import CoursesCard from '@/components/Instructor/CoursesCard.vue';
import QuickStatsCard from '@/components/Instructor/QuickStatsCard.vue';
import ContactCard from '@/components/Instructor/ContactCard.vue';

interface Instructor {
  id: number;
  name: string;
  email: string;
  employee_id: string | null;
  title: string | null;
  department: string | null;
  specialization: string | null;
  bio: string | null;
  office_location: string | null;
  phone: string | null;
  office_hours: string | null;
  hire_date: string | null;
  employment_type: string | null;
  status: string | null;
  salary: number | null;
  education_level: string | null;
  certifications: any;
  years_experience: number | null;
}

interface Course {
  id: number;
  title: string;
  description: string | null;
  students_count: number;
}

interface Stats {
  total_courses_as_instructor: number;
  total_students_enrolled: number;
  total_courses_created: number;
}

const props = defineProps<{
  instructor: Instructor;
  courses: Course[];
  stats: Stats;
}>();

const { success, error, warning } = useToast();

// Track active fields and unsaved changes
const activeFields = ref<Set<string>>(new Set());
const unsavedChanges = ref<Record<string, any>>({});
const hasUnsavedChanges = computed(() => Object.keys(unsavedChanges.value).length > 0);
const unsavedFieldCount = computed(() => Object.keys(unsavedChanges.value).length);
// Show Save All button when editing 2+ fields simultaneously
const showSaveAll = computed(() => {
  console.log('Checking showSaveAll: activeFields.size =', activeFields.value.size);
  return activeFields.value.size >= 2;
});

// Valid enum values
const employmentTypeOptions = ['full-time', 'part-time', 'adjunct', 'visiting'];
const statusOptions = ['active', 'inactive', 'on-leave', 'retired'];

// Breadcrumb items
const breadcrumbItems: BreadcrumbItem[] = [
  { title: 'Home', href: '/' },
  { title: 'Role Management', href: '/role-management' },
  { title: props.instructor.name, href: `/instructor/${props.instructor.id}` }
];

// Track field edit state
const handleEditStart = (field: string) => {
  activeFields.value.add(field);
};

const handleEditEnd = (field: string) => {
  activeFields.value.delete(field);
  // Don't remove from unsavedChanges yet - users might want to save all later
};

// Handle field updates - save immediately with fetch
const handleFieldUpdate = async (field: string, value: any) => {
  // Validate enum fields
  if (field === 'employment_type' && !employmentTypeOptions.includes(String(value))) {
    error(`Invalid employment type. Must be one of: ${employmentTypeOptions.join(', ')}`);
    return;
  }
  if (field === 'status' && !statusOptions.includes(String(value))) {
    error(`Invalid status. Must be one of: ${statusOptions.join(', ')}`);
    return;
  }
  
  // Track this field as having pending changes
  unsavedChanges.value[field] = value;
  console.log('Added to unsavedChanges:', field, '=', value, 'Total unsaved:', unsavedFieldCount.value);
  
  try {
    // Get CSRF token from meta tag
    const token = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content;
    
    const response = await fetch(`/api/instructor/${props.instructor.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(token && { 'X-CSRF-Token': token })
      },
      body: JSON.stringify({ [field]: value })
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Failed to save changes');
    }

    const data = await response.json();
    // Remove from unsaved changes since it's been successfully saved
    delete unsavedChanges.value[field];
    console.log('Removed from unsavedChanges:', field, 'Total unsaved:', unsavedFieldCount.value);
    success(data.message || 'Changes saved successfully');
  } catch (err) {
    console.error('Save error:', err);
    error(err instanceof Error ? err.message : 'Failed to save changes. Please try again.');
  }
};

// Save all unsaved changes at once
const saveAllChanges = async () => {
  console.log('Save All Changes clicked');
  console.log('Active fields:', Array.from(activeFields.value));
  console.log('Unsaved changes:', unsavedChanges.value);
  
  if (activeFields.value.size < 2) {
    warning('Need at least 2 fields being edited to use Save All');
    return;
  }

  // Only save the current active/editing fields
  const fieldsToSave: Record<string, any> = {};
  for (const field of activeFields.value) {
    if (unsavedChanges.value[field] !== undefined) {
      fieldsToSave[field] = unsavedChanges.value[field];
    }
  }
  
  if (Object.keys(fieldsToSave).length === 0) {
    warning('No fields to save');
    return;
  }
  
  console.log('Fields to save:', fieldsToSave);
  
  // Validate all enum fields
  for (const [field, value] of Object.entries(fieldsToSave)) {
    console.log(`Validating field: ${field} = ${value}`);
    if (field === 'employment_type' && !employmentTypeOptions.includes(String(value))) {
      error(`Invalid employment type. Must be one of: ${employmentTypeOptions.join(', ')}`);
      return;
    }
    if (field === 'status' && !statusOptions.includes(String(value))) {
      error(`Invalid status. Must be one of: ${statusOptions.join(', ')}`);
      return;
    }
  }
  
  try {
    const token = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content;
    const changeCount = Object.keys(fieldsToSave).length;
    
    console.log('Sending save request with data:', fieldsToSave);
    
    const response = await fetch(`/api/instructor/${props.instructor.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(token && { 'X-CSRF-Token': token })
      },
      body: JSON.stringify(fieldsToSave)
    });

    console.log('Response status:', response.status);
    
    if (!response.ok) {
      const errorData = await response.json();
      console.error('Server error:', errorData);
      throw new Error(errorData.message || 'Failed to save changes');
    }

    const data = await response.json();
    console.log('Save successful:', data);
    
    // Remove saved fields from unsavedChanges
    for (const field of Object.keys(fieldsToSave)) {
      delete unsavedChanges.value[field];
    }
    
    success(`${changeCount} ${changeCount === 1 ? 'change' : 'changes'} saved successfully`);
  } catch (err) {
    console.error('Save error:', err);
    error(err instanceof Error ? err.message : 'Failed to save changes. Please try again.');
  }
};

// Warn before leaving with unsaved changes
const handleBeforeUnload = (e: BeforeUnloadEvent) => {
  if (hasUnsavedChanges.value) {
    e.preventDefault();
    e.returnValue = '';
    warning('You have unsaved changes. Do you want to leave without saving?');
  }
};

onMounted(() => {
  window.addEventListener('beforeunload', handleBeforeUnload);
});

onBeforeUnmount(() => {
  window.removeEventListener('beforeunload', handleBeforeUnload);
});
</script>

<template>
  <Head :title="`${instructor.name} - Instructor Details`" />

  <AppLayout :breadcrumbs="breadcrumbItems">
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-purple-50 to-indigo-50 dark:from-gray-900 dark:via-purple-950 dark:to-indigo-950 py-8 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <!-- Header with back button and save all button -->
        <div class="flex justify-between items-center mb-6">
          <Link
            href="/role-management"
            class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-200 transition-colors group"
          >
            <ArrowLeft class="w-5 h-5 group-hover:-translate-x-1 transition-transform" />
            <span class="font-medium">Back to Role Management</span>
          </Link>

          <button
            v-if="showSaveAll"
            @click="saveAllChanges"
            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl"
          >
            <Save class="w-5 h-5" />
            <span>Save All Changes ({{ activeFields.size }})</span>
          </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Header Card -->
            <HeaderCard
              :name="instructor.name"
              :title="instructor.title"
              :email="instructor.email"
              :phone="instructor.phone"
              :office-location="instructor.office_location"
              :employee-id="instructor.employee_id"
              :status="instructor.status"
              @update-field="handleFieldUpdate"
              @edit-start="handleEditStart"
              @edit-end="handleEditEnd"
            />
            
            <!-- Professional Information -->
            <ProfessionalInfoCard
              :department="instructor.department"
              :specialization="instructor.specialization"
              :employment-type="instructor.employment_type"
              :hire-date="instructor.hire_date"
              :years-of-experience="instructor.years_experience"
              :office-hours="instructor.office_hours"
              @update-field="handleFieldUpdate"
              @edit-start="handleEditStart"
              @edit-end="handleEditEnd"
            />

            <!-- Education & Certifications -->
            <EducationCertificationsCard
              :education-level="instructor.education_level"
              :certifications="instructor.certifications"
              @update-field="handleFieldUpdate"
              @edit-start="handleEditStart"
              @edit-end="handleEditEnd"
            />

            <!-- Courses Taught -->
            <CoursesCard :courses="courses" />
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Quick Stats -->
            <QuickStatsCard :courses="courses" :stats="stats" />

            <!-- Contact Card -->
            <ContactCard
              :email="instructor.email"
              :phone="instructor.phone"
              :office-location="instructor.office_location"
              :office-hours="instructor.office_hours"
            />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
