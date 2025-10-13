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
  grade_level_id?: number | null;
  section?: string;
}

export interface UserUpdateData {
  name: string;
  email: string;
  password?: string;
  role: string;
  grade_level?: string;
  grade_level_id?: number | null;
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

export type Activity ={
    id: number;
    title: string;
    description: string;
    activity_type: number | ActivityType; // Can be ID or full object (Laravel snake_case)
    activity_type_id: number; // Same as activity_type
    created_by: number; // User ID of the creator
    created_at: string;
    updated_at: string;
    due_date?: string; // New due date field
    activityType?: ActivityType; // Camelcase version (may not always be present)
    creator: User;
    question_count?: number;
    total_points?: number;
    has_due_date?: boolean;
    used_in_modules?: Array<{
        id: number;
        title: string;
    }>;
    quiz?: Quiz;
    assignment?: any;
    pivot?: any; // For pivot data when loaded through relationships
}

export type ActivityType = {
    id: number;
    name: string; // e.g., "Assignment", "Quiz"
    description: string;
}

export type Quiz = {
    id: number;
    created_by: number; // User ID of the creator
    title: string;
    description: string;  
}
export type Question = {
    id: number;
    quiz_id: number; // Associated Quiz ID
    question_text: string;
    options: Array<QuestionOption>;//can be null
    question_type: string; // e.g., "multiple_choice", "true_false", "enumeration", "short_answer" (database format)
    points: number; 
    correct_answer?: string; // For text-based questions
    created_at: string;
    updated_at: string;
}
export type QuestionType = {
    id: number;
    type: string; // e.g., "multiple-choice", "true-false","enumeration", "short-answer"
    description: string;
}
export type QuestionOption = {
    id: number;
    question_id: number; // Associated Question ID
    option_text: string;
    is_correct: boolean;
}
export type Assignment = {
    id: number;
    created_by: number; // User ID of the creator
    title: string;
    description: string;
    document: number; //the document id stored in the document table 
    created_at: string;
    updated_at: string;
}
export type AssignmentType = {
    id: number;
    type: string; // e.g., "homework", "project"
    
}

export type AssignmentDocument = {
    id: number;
    assignment_id: number; // Associated Assignment ID  
}
export type AssignmentSubmission = {
    id: number;
    assignment_id: number; // Associated Assignment ID
    student_id: number; // User ID of the student
    submitted_at: string;
    grade?: number; // Nullable, as it may not be graded yet
    feedback?: string; // Nullable, as feedback may not be provided yet
}
export type AssignmentSubmissionFile = {
    id: number;
    submission_id: number; // Associated Submission ID
}

export type QuizAttempt = {
    id: number;
    quiz_id: number; // Associated Quiz ID  
    student_id: number; // User ID of the student
    score?: number; // Nullable, as it may not be graded yet
    attempted_at: string;
}
export type QuizAttemptAnswer = {
    id: number;
    attempt_id: number; // Associated Attempt ID  
    question_id: number; // Associated Question ID
    answer_text: string;
    is_correct?: boolean; // Nullable, as it may not be graded yet
}
export type QuizAttemptFile = {
    id: number;
    answer_id: number; // Associated Answer ID 
    document_id: number; //the document id stored in the document table 
} 

export type StudentQuizProgress = { 
  id: number;
  student_id: number; // User ID of the student
  quiz_id: number; // Associated Quiz ID
  activity_id: number; // Associated Activity ID
  quiz: Quiz; // Full Quiz object with questions
  answers: Array<StudentQuizAnswer>; // Array of answers provided by the student
  started_at: string;
  last_accessed_at: string;
  is_completed: boolean;
  is_submitted: boolean;
  completed_questions: number;
  total_questions: number;
  score?: number; // Calculated score based on correct answers
  percentage_score?: number; // Score as percentage
  time_spent?: number; // Time spent in minutes
  created_at: string;
  updated_at: string;
} 

export type StudentQuizAnswer = {
  id: number;
  student_id: number; // User ID of the student
  quiz_progress_id: number; // Associated Progress ID
  question_id: number; // Associated Question ID
  question: Question; // Full Question object for validation
  selected_option_id?: number; // For multiple choice questions
  selected_option?: QuestionOption; // The selected option object (from Laravel relationship)
  selectedOption?: QuestionOption; // Camelcase alias for compatibility
  answer_text?: string; // For text-based answers
  is_correct: boolean; // Determined by comparing with question.correct_answer
  points_earned: number; // Points earned for this answer
  answered_at: string;
  created_at: string;
  updated_at: string;
}

export type Module = {
    id: number;
    title?: string;
    course_id: number; // Associated Course ID
    description: string;
    sequence?: number;
    completion_percentage?: number;
    created_by: number; // User ID of the creator 
    created_at: string;
    updated_at: string;
    module_percentage?: number; // Optional percentage weight of the module
    module_type?: string; // Database field: "Lessons", "Activities", "Mixed","Quizzes","Assignments","Assessment"
    moduleType?: string; // Alias for module_type (for compatibility)
    lessons?: Array<{
        id: number;
        title: string;
        description: string;
        documents: Array<{
            id: number;
            name: string;
            file_path: string;
            doc_type: string;
        }>;
    }>; // Optional array of lessons within the module
    activities?: Array<Activity>; // Optional array of full Activity objects
}

export type ModuleLesson = {
    id: number;
    module_id: number; // Associated Module ID
    title: string;
    content: string;
    order: number; // Order of the lesson within the module sequence
    created_at: string;
    updated_at: string;
}

export type ModuleActivity = {
    id: number;
    module_id: number; // Associated Module ID  
    activity_id: number; // Associated Activity ID
    module_course_id: number; // Associated Module Course ID
    order: number; // Order of the activity within the module sequence
}

export type ModuleLessonActivity = {
    id: number;
    module_lesson_id: number; // Associated Module Lesson ID  
    activity_id: number; // Associated Activity ID
    order: number; // Order of the activity within the lesson sequence
}

export type BreadcrumbItemType = BreadcrumbItem;
