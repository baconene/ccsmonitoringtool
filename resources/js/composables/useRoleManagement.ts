import { ref } from 'vue';
import axios from 'axios';
import type { Role } from '@/types';

export function useRoleManagement() {
  const roles = ref<Role[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const fetchRoles = async () => {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await axios.get('/api/roles');
      roles.value = response.data;
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch roles';
      console.error('Error fetching roles:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const getRoleBadgeColor = (roleName: string) => {
    switch (roleName) {
      case 'admin': return 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200';
      case 'instructor': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200';
      case 'student': return 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200';
      default: return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';
    }
  };

  return {
    roles,
    loading,
    error,
    fetchRoles,
    getRoleBadgeColor
  };
}
