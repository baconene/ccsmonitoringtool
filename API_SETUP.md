# Learning Management System API Setup

This document describes the API utils setup for handling data calls from the backend.

## Overview

The API utils file (`resources/js/utils/api.ts`) provides a centralized way to handle all API calls to the Laravel backend. This ensures consistent error handling, request configuration, and data typing across the application.

## Features

- **Type Safety**: Full TypeScript support with defined interfaces
- **Error Handling**: Centralized error handling with user-friendly messages
- **Retry Logic**: Automatic retry functionality for failed requests
- **CSRF Protection**: Automatic CSRF token handling
- **Authentication**: Sanctum-based API authentication

## API Endpoints

### Courses API (`/api/courses`)
- `GET /api/courses` - Get all courses for authenticated instructor
- `POST /api/courses` - Create a new course
- `GET /api/courses/{id}` - Get specific course
- `PUT /api/courses/{id}` - Update course
- `DELETE /api/courses/{id}` - Delete course
- `GET /api/courses/{id}/students` - Get students enrolled in course

### Dashboard API (`/api/dashboard`)
- `GET /api/dashboard/stats` - Get dashboard statistics
- `GET /api/instructor/profile` - Get instructor profile

### Students API (`/api/students`)
- `GET /api/students` - Get all students for instructor's courses
- `GET /api/students/{id}` - Get specific student

### Schedule API (`/api/schedule`)
- `GET /api/schedule` - Get full schedule
- `GET /api/schedule/upcoming?limit=5` - Get upcoming schedule items

## Usage Examples

### Using the API Utils in Components

```typescript
import { coursesApi, dashboardApi, handleApiError } from '@/utils/api';

// Fetch courses
const fetchCourses = async () => {
  try {
    const courses = await coursesApi.getCourses();
    console.log(courses);
  } catch (error) {
    console.error(handleApiError(error));
  }
};

// Get dashboard stats
const fetchStats = async () => {
  try {
    const stats = await dashboardApi.getDashboardStats();
    console.log(stats);
  } catch (error) {
    console.error(handleApiError(error));
  }
};
```

### Component Integration

The `InstructorDashboard.vue` component has been updated to:
- Fetch real data from the backend API on component mount
- Fall back to sample data if API calls fail
- Display loading and error states
- Provide a refresh function to reload data

The `Dashboard.vue` component acts as a container that:
- Manages the overall dashboard state
- Provides fallback data
- Handles API failures gracefully

## Database Setup

The following migrations have been added:
- `add_instructor_id_and_title_to_courses_table` - Adds instructor relationship and title field
- `create_course_user_table` - Creates pivot table for student enrollments
- Sanctum migrations for API authentication

Sample data is provided through the `CourseEnrollmentSeeder`.

## Authentication

The API uses Laravel Sanctum for authentication. Make sure to:
1. Include CSRF token in requests (handled automatically)
2. Authenticate users before accessing protected endpoints
3. Use the `auth:sanctum` middleware on protected routes

## Error Handling

The API utils provide comprehensive error handling:
- Network errors
- Authentication errors (401)
- Authorization errors (403)
- Validation errors (422)
- Server errors (500)

## Development vs Production

In development, the components fall back to sample data if API calls fail. In production, you should ensure all API endpoints are properly implemented and accessible.

## Next Steps

1. Implement real Schedule model and API endpoints
2. Add Assignments model and API endpoints
3. Implement proper user roles (instructor vs student)
4. Add real-time updates using WebSockets or polling
5. Add caching for frequently accessed data