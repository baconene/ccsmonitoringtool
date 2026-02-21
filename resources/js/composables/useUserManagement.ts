import { ref } from 'vue';
import axios from 'axios';
import type { User, NewUserData, UserUpdateData } from '@/types';

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

  // Validation errors
  if (err.response?.status === 422) {
    const errors = err.response.data?.errors;
    if (errors && typeof errors === 'object') {
      const firstError = Object.values(errors)[0];
      if (Array.isArray(firstError)) {
        return firstError[0] as string;
      }
    }
  }

  // Unauthorized
  if (err.response?.status === 401) {
    return 'Unauthorized. Please log in again.';
  }

  // Forbidden
  if (err.response?.status === 403) {
    return 'You do not have permission to perform this action.';
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
  return 'An unexpected error occurred.';
};

export function useUserManagement() {
  const users = ref<User[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const fetchUsers = async () => {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await axios.get('/api/users');
      users.value = response.data;
      return response.data;
    } catch (err: any) {
      error.value = getErrorMessage(err);
      console.error('Error fetching users:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const createUser = async (userData: NewUserData) => {
    loading.value = true;
    error.value = null;
    
    console.log('Creating user with data:', userData);
    
    try {
      const response = await axios.post('/api/users', userData);
      console.log('User created successfully:', response.data);
      await fetchUsers();
      return response.data;
    } catch (err: any) {
      error.value = getErrorMessage(err);
      console.error('Error creating user:', err.response?.data || err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const updateUser = async (userId: number, userData: UserUpdateData) => {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await axios.put(`/api/users/${userId}`, userData);
      await fetchUsers();
      return response.data;
    } catch (err: any) {
      error.value = getErrorMessage(err);
      console.error('Error updating user:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const deleteUser = async (userId: number) => {
    loading.value = true;
    error.value = null;
    
    try {
      await axios.delete(`/api/users/${userId}`);
      await fetchUsers();
    } catch (err: any) {
      error.value = getErrorMessage(err);
      console.error('Error deleting user:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    users,
    loading,
    error,
    fetchUsers,
    createUser,
    updateUser,
    deleteUser
  };
}
