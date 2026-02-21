import { ref } from 'vue';
import axios from 'axios';
import type { Role } from '@/types';

// Helper function to extract user-friendly error messages
const getErrorMessage = (err: any): string => {
  // Network error (no response from server)
  if (!err.response) {
    if (err.message === 'Network Error') {
      return 'Network error. Please check your connection and try again.';
    }
    if (err.code === 'ECONNABORTED') {
      return 'Request timeout. Server is taking too long to respond.';
    }
    return err.message || 'Network error occurred. Please check your connection.';
  }

  // Unauthorized
  if (err.response?.status === 401) {
    return 'Unauthorized. Please log in again.';
  }

  // Forbidden
  if (err.response?.status === 403) {
    return 'You do not have permission to access this resource.';
  }

  // Not found
  if (err.response?.status === 404) {
    return 'Resource not found.';
  }

  // Server error
  if (err.response?.status >= 500) {
    return 'Server error. Please try again later.';
  }

  // Custom error message from API
  if (err.response?.data?.message) {
    return err.response.data.message;
  }

  // Fallback
  return 'Failed to fetch roles';
};

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
      error.value = getErrorMessage(err);
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
