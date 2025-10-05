import { ref } from 'vue';
import axios from 'axios';
import type { User, NewUserData, UserUpdateData } from '@/types';

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
      error.value = err.response?.data?.message || 'Failed to fetch users';
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
      error.value = err.response?.data?.message || 
                    err.response?.data?.errors?.password?.[0] || 
                    'Failed to create user';
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
      error.value = err.response?.data?.message || 'Failed to update user';
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
      error.value = err.response?.data?.message || 'Failed to delete user';
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
