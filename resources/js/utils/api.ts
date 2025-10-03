import axios from 'axios';

// Define types for API responses
export interface Course {
  id: number;
  title: string;
  description: string;
  instructor_id: number;
  created_at: string;
  updated_at: string;
  students?: Student[];
}

export interface Student {
  id: number;
  name: string;
  email: string;
  courseId?: number;
  courseTitle?: string;
}

export interface Schedule {
  id: number;
  courseId: number;
  courseTitle: string;
  date: string;
  time: string;
  type: string;
}

export interface Instructor {
  id: number;
  name: string;
  email: string;
}

export interface DashboardStats {
  totalCourses: number;
  totalStudents: number;
  upcomingClasses: number;
  totalAssignments: number;
}

// API base configuration
const API_BASE_URL = '/api';

// Configure axios defaults
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Content-Type'] = 'application/json';

// Add CSRF token if available
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (csrfToken) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

/**
 * Courses API calls
 */
export const coursesApi = {
  /**
   * Get all courses for the authenticated instructor
   */
  async getCourses(): Promise<Course[]> {
    try {
      const response = await axios.get(`${API_BASE_URL}/courses`);
      return response.data;
    } catch (error) {
      console.error('Error fetching courses:', error);
      throw new Error('Failed to fetch courses');
    }
  },

  /**
   * Get a specific course by ID
   */
  async getCourse(courseId: number): Promise<Course> {
    try {
      const response = await axios.get(`${API_BASE_URL}/courses/${courseId}`);
      return response.data;
    } catch (error) {
      console.error(`Error fetching course ${courseId}:`, error);
      throw new Error(`Failed to fetch course ${courseId}`);
    }
  },

  /**
   * Create a new course
   */
  async createCourse(courseData: Partial<Course>): Promise<Course> {
    try {
      const response = await axios.post(`${API_BASE_URL}/courses`, courseData);
      return response.data;
    } catch (error) {
      console.error('Error creating course:', error);
      throw new Error('Failed to create course');
    }
  },

  /**
   * Update an existing course
   */
  async updateCourse(courseId: number, courseData: Partial<Course>): Promise<Course> {
    try {
      const response = await axios.put(`${API_BASE_URL}/courses/${courseId}`, courseData);
      return response.data;
    } catch (error) {
      console.error(`Error updating course ${courseId}:`, error);
      throw new Error(`Failed to update course ${courseId}`);
    }
  },

  /**
   * Delete a course
   */
  async deleteCourse(courseId: number): Promise<void> {
    try {
      await axios.delete(`${API_BASE_URL}/courses/${courseId}`);
    } catch (error) {
      console.error(`Error deleting course ${courseId}:`, error);
      throw new Error(`Failed to delete course ${courseId}`);
    }
  },

  /**
   * Get students enrolled in a specific course
   */
  async getCourseStudents(courseId: number): Promise<Student[]> {
    try {
      const response = await axios.get(`${API_BASE_URL}/courses/${courseId}/students`);
      return response.data;
    } catch (error) {
      console.error(`Error fetching students for course ${courseId}:`, error);
      throw new Error(`Failed to fetch students for course ${courseId}`);
    }
  }
};

/**
 * Students API calls
 */
export const studentsApi = {
  /**
   * Get all students
   */
  async getStudents(): Promise<Student[]> {
    try {
      const response = await axios.get(`${API_BASE_URL}/students`);
      return response.data;
    } catch (error) {
      console.error('Error fetching students:', error);
      throw new Error('Failed to fetch students');
    }
  },

  /**
   * Get a specific student by ID
   */
  async getStudent(studentId: number): Promise<Student> {
    try {
      const response = await axios.get(`${API_BASE_URL}/students/${studentId}`);
      return response.data;
    } catch (error) {
      console.error(`Error fetching student ${studentId}:`, error);
      throw new Error(`Failed to fetch student ${studentId}`);
    }
  }
};

/**
 * Schedule API calls
 */
export const scheduleApi = {
  /**
   * Get schedule for the authenticated instructor
   */
  async getSchedule(): Promise<Schedule[]> {
    try {
      const response = await axios.get(`${API_BASE_URL}/schedule`);
      return response.data;
    } catch (error) {
      console.error('Error fetching schedule:', error);
      throw new Error('Failed to fetch schedule');
    }
  },

  /**
   * Get upcoming schedule items
   */
  async getUpcomingSchedule(limit: number = 5): Promise<Schedule[]> {
    try {
      const response = await axios.get(`${API_BASE_URL}/schedule/upcoming?limit=${limit}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching upcoming schedule:', error);
      throw new Error('Failed to fetch upcoming schedule');
    }
  }
};

/**
 * Dashboard API calls
 */
export const dashboardApi = {
  /**
   * Get dashboard statistics
   */
  async getDashboardStats(): Promise<DashboardStats> {
    try {
      const response = await axios.get(`${API_BASE_URL}/dashboard/stats`);
      return response.data;
    } catch (error) {
      console.error('Error fetching dashboard stats:', error);
      throw new Error('Failed to fetch dashboard statistics');
    }
  },

  /**
   * Get instructor profile
   */
  async getInstructorProfile(): Promise<Instructor> {
    try {
      const response = await axios.get(`${API_BASE_URL}/instructor/profile`);
      return response.data;
    } catch (error) {
      console.error('Error fetching instructor profile:', error);
      throw new Error('Failed to fetch instructor profile');
    }
  }
};

/**
 * Generic error handler for API calls
 */
export const handleApiError = (error: any): string => {
  if (error.response) {
    // Server responded with error status
    const status = error.response.status;
    const message = error.response.data?.message || 'An error occurred';
    
    switch (status) {
      case 401:
        return 'Unauthorized. Please log in again.';
      case 403:
        return 'Access forbidden. You do not have permission to perform this action.';
      case 404:
        return 'Resource not found.';
      case 422:
        return message || 'Invalid data provided.';
      case 500:
        return 'Server error. Please try again later.';
      default:
        return message;
    }
  } else if (error.request) {
    // Network error
    return 'Network error. Please check your connection.';
  } else {
    // Other error
    return error.message || 'An unexpected error occurred.';
  }
};

/**
 * Utility function to retry API calls
 */
export const retryApiCall = async <T>(
  apiCall: () => Promise<T>,
  maxRetries: number = 3,
  delay: number = 1000
): Promise<T> => {
  let lastError: any;
  
  for (let i = 0; i < maxRetries; i++) {
    try {
      return await apiCall();
    } catch (error) {
      lastError = error;
      if (i < maxRetries - 1) {
        await new Promise(resolve => setTimeout(resolve, delay * (i + 1)));
      }
    }
  }
  
  throw lastError;
};