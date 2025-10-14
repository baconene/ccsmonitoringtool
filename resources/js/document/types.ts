// Document types for the upload system

export type ModelType = 'course' | 'activity' | 'lesson' | 'module' | 'report' | 'project' | 'assessment' | 'student' | 'instructor';

export type VisibilityType = 'public' | 'students' | 'instructors' | 'private';

export type DocumentCategory = 
  | 'syllabus'
  | 'lecture_notes'
  | 'reading_material'
  | 'assignment'
  | 'solution'
  | 'exam'
  | 'quiz'
  | 'transcript'
  | 'id_card'
  | 'certificate'
  | 'grade_report'
  | 'progress_report'
  | 'performance_report'
  | 'certification'
  | 'resume'
  | 'other';

export interface DocumentMetadata {
  description?: string;
  tags?: string[];
  category?: DocumentCategory;
  academic_year?: string;
  semester?: string;
  version?: number;
  [key: string]: any;
}

export interface Document {
  id: number;
  name: string;
  original_name: string;
  file_path: string;
  file_size: number;
  mime_type: string;
  extension: string;
  document_type: ModelType;
  uploaded_by: number;
  description?: string;
  metadata?: DocumentMetadata;
  created_at: string;
  updated_at: string;
  deleted_at?: string;
  
  // Accessors
  file_size_human?: string;
  file_url?: string;
  
  // Relationships
  uploader?: {
    id: number;
    name: string;
    email: string;
  };
}

export interface CourseDocument {
  id: number;
  document_id: number;
  course_id: number;
  visibility: VisibilityType;
  is_required: boolean;
  order: number;
  created_at: string;
  updated_at: string;
  
  // Relationships
  document?: Document;
  course?: {
    id: number;
    title: string;
    code: string;
  };
}

export interface ActivityDocument {
  id: number;
  document_id: number;
  activity_id: number;
  visibility: VisibilityType;
  is_required: boolean;
  order: number;
  created_at: string;
  updated_at: string;
  
  // Relationships
  document?: Document;
  activity?: {
    id: number;
    title: string;
    activity_type: string;
  };
}

export interface LessonDocument {
  id: number;
  document_id: number;
  lesson_id: number;
  visibility: VisibilityType;
  is_required: boolean;
  order: number;
  created_at: string;
  updated_at: string;
  
  // Relationships
  document?: Document;
  lesson?: {
    id: number;
    title: string;
  };
}

export interface ModuleDocument {
  id: number;
  document_id: number;
  module_id: number;
  visibility: VisibilityType;
  is_required: boolean;
  order: number;
  created_at: string;
  updated_at: string;
  
  // Relationships
  document?: Document;
  module?: {
    id: number;
    title: string;
  };
}

export interface ReportDocument {
  id: number;
  document_id: number;
  student_id?: number;
  course_id?: number;
  report_type: string;
  generated_by: number;
  generated_at: string;
  created_at: string;
  updated_at: string;
  
  // Relationships
  document?: Document;
  student?: {
    id: number;
    name: string;
  };
  course?: {
    id: number;
    title: string;
  };
}

export interface ProjectDocument {
  id: number;
  document_id: number;
  activity_id: number;
  student_id: number;
  submission_date: string;
  status: 'submitted' | 'graded' | 'returned' | 'resubmitted';
  feedback?: string;
  created_at: string;
  updated_at: string;
  
  // Relationships
  document?: Document;
  activity?: {
    id: number;
    title: string;
  };
  student?: {
    id: number;
    name: string;
  };
}

export interface AssessmentDocument {
  id: number;
  document_id: number;
  activity_id: number;
  student_id?: number;
  document_category: string;
  score?: number;
  created_at: string;
  updated_at: string;
  
  // Relationships
  document?: Document;
  activity?: {
    id: number;
    title: string;
  };
  student?: {
    id: number;
    name: string;
  };
}

export interface StudentDocument {
  id: number;
  document_id: number;
  student_id: number;
  document_category: string;
  academic_year?: string;
  verified: boolean;
  verified_by?: number;
  verified_at?: string;
  created_at: string;
  updated_at: string;
  
  // Relationships
  document?: Document;
  student?: {
    id: number;
    name: string;
  };
  verifier?: {
    id: number;
    name: string;
  };
}

export interface InstructorDocument {
  id: number;
  document_id: number;
  instructor_id: number;
  document_category: string;
  expiry_date?: string;
  verified: boolean;
  verified_by?: number;
  verified_at?: string;
  created_at: string;
  updated_at: string;
  
  // Relationships
  document?: Document;
  instructor?: {
    id: number;
    name: string;
  };
  verifier?: {
    id: number;
    name: string;
  };
}

// Upload request/response types
export interface UploadRequest {
  model_type: ModelType;
  foreign_key_id: number;
  foreign_key_name: string;
  visibility: VisibilityType;
  is_required: boolean;
  files: File[];
}

export interface UploadResponse {
  success: boolean;
  message: string;
  documents: Document[];
}

export interface UploadError {
  message: string;
  errors?: {
    [key: string]: string[];
  };
}

// Component props
export interface DocumentUploaderProps {
  modelType: ModelType;
  foreignKeyId: number;
  foreignKeyName?: string;
  uploadUrl?: string;
  multiple?: boolean;
  maxFileSize?: number;
  acceptedTypes?: string;
  autoUpload?: boolean;
  visibility?: VisibilityType;
  isRequired?: boolean;
}

// Component events
export interface DocumentUploaderEmits {
  (e: 'upload-success', files: File[]): void;
  (e: 'upload-error', error: string): void;
  (e: 'files-selected', files: File[]): void;
}
