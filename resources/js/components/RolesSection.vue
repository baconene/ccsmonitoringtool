<script setup lang="ts">
import type { Role } from '@/types';

defineProps<{
  roles: Role[];
  getRoleBadgeColor: (roleName: string) => string;
}>();
</script>

<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">System Roles</h2>
    
    <div class="space-y-4">
      <div
        v-for="role in roles"
        :key="role.id"
        class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:shadow-sm transition-shadow"
      >
        <div class="flex items-center justify-between mb-2">
          <div class="flex items-center space-x-3">
            <span :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getRoleBadgeColor(role.name)}`">
              {{ role.display_name }}
            </span>
            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ role.name }}</span>
          </div>
          <span :class="`inline-flex items-center px-2 py-1 text-xs font-medium rounded ${role.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'}`">
            {{ role.is_active ? 'Active' : 'Inactive' }}
          </span>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-300">{{ role.description }}</p>
      </div>
    </div>
  </div>
</template>
