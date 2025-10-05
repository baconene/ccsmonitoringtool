import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';
export interface User {
 
      id: number;
    name: string;
    email: string;
    avatar?: string;
    role?: string | Role; // Support both string and Role object
    role_id?: number;
    role_name?: string; // Computed attribute from backend
    role_display_name?: string; // Computed attribute from backend
    grade_level?: string; // For students
    section?: string; // For students
    email_verified_at: string | null;
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
  grade_level?: string;
  section?: string;
}

export interface UserUpdateData {
  name: string;
  email: string;
  password?: string;
  role: string;
  grade_level?: string;
  section?: string;
}
//


export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

 

 

export type BreadcrumbItemType = BreadcrumbItem;
