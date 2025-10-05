import { computed, unref, type ComputedRef, type Ref } from 'vue';
import type { Module } from '@/types';

export function useModuleType(moduleRef: Module | null | ComputedRef<Module | null> | Ref<Module | null>) {
  // Get normalized module type
  const moduleType = computed(() => {
    const module = unref(moduleRef);
    return module?.module_type || module?.moduleType || 'Mixed';
  });

  // Check if module allows lessons
  const allowsLessons = computed(() => {
    const type = moduleType.value;
    return type === 'Lessons' || type === 'Mixed';
  });

  // Check if module allows activities
  const allowsActivities = computed(() => {
    const type = moduleType.value;
    return ['Activities', 'Mixed', 'Quizzes', 'Assignments', 'Assessment'].includes(type);
  });

  // Check if module is activity-only
  const isActivityOnly = computed(() => {
    const type = moduleType.value;
    return ['Activities', 'Quizzes', 'Assignments', 'Assessment'].includes(type);
  });

  // Get module type badge color classes
  const getModuleTypeBadgeClass = computed(() => {
    const type = moduleType.value;
    const classes: Record<string, string> = {
      'Lessons': 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
      'Activities': 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
      'Mixed': 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
      'Quizzes': 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400',
      'Assignments': 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
      'Assessment': 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
    };
    return classes[type] || classes['Mixed'];
  });

  return {
    moduleType,
    allowsLessons,
    allowsActivities,
    isActivityOnly,
    getModuleTypeBadgeClass
  };
}
