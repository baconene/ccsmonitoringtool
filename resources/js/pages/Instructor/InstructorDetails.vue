<script setup lang="ts">
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types/index';
import { ArrowLeft, Save } from 'lucide-vue-next';
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

// Track unsaved changes
const unsavedChanges = ref<Record<string, any>>({});
const hasUnsavedChanges = computed(() => Object.keys(unsavedChanges.value).length > 0);
const isSaving = ref(false);

// Breadcrumb items
const breadcrumbItems: BreadcrumbItem[] = [
  { title: 'Home', href: '/' },
  { title: 'Role Management', href: '/role-management' },
  { title: props.instructor.name, href: `/instructor/${props.instructor.id}` }
];

// Handle field updates
const handleFieldUpdate = (field: string, value: any) => {
  unsavedChanges.value[field] = value;
};

// Save changes
const saveChanges = async () => {
  if (!hasUnsavedChanges.value) return;
  
  isSaving.value = true;
  
  try {
    router.put(`/api/instructor/${props.instructor.id}`, unsavedChanges.value, {
      preserveScroll: true,
      onSuccess: () => {
        unsavedChanges.value = {};
      },
      onError: (errors) => {
        console.error('Save failed:', errors);
        alert('Failed to save changes. Please try again.');
      },
      onFinish: () => {
        isSaving.value = false;
      }
    });
  } catch (error) {
    console.error('Save error:', error);
    isSaving.value = false;
  }
};

// Warn before leaving with unsaved changes
const handleBeforeUnload = (e: BeforeUnloadEvent) => {
  if (hasUnsavedChanges.value) {
    e.preventDefault();
    e.returnValue = '';
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
        
        <!-- Header with back button and save button -->
        <div class="flex justify-between items-center mb-6">
          <Link
            href="/role-management"
            class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-200 transition-colors group"
          >
            <ArrowLeft class="w-5 h-5 group-hover:-translate-x-1 transition-transform" />
            <span class="font-medium">Back to Role Management</span>
          </Link>

          <button
            v-if="hasUnsavedChanges"
            @click="saveChanges"
            :disabled="isSaving"
            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <Save class="w-5 h-5" />
            <span>{{ isSaving ? 'Saving...' : 'Save Changes' }}</span>
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
            />

            <!-- Education & Certifications -->
            <EducationCertificationsCard
              :education-level="instructor.education_level"
              :certifications="instructor.certifications"
              @update-field="handleFieldUpdate"
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
