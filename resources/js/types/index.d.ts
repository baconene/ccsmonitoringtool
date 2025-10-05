import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

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

export interface Role {
    id: number;
    name: string;
    display_name: string;
    description?: string;
    is_active: boolean;
}

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

export type BreadcrumbItemType = BreadcrumbItem;
