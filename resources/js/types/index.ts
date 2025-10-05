export interface User {
  id: number;
  name: string;
  email: string;
  role_id: number;
  role_name: string;
  role_display_name: string;
  created_at: string;
  updated_at: string;
}

export interface Role {
  id: number;
  name: string;
  display_name: string;
  description: string;
  is_active: boolean;
}

export interface NewUserData {
  name: string;
  email: string;
  password: string;
  role: string;
}

export interface UserUpdateData {
  name: string;
  email: string;
  password?: string;
  role: string;
}