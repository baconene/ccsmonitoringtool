// Utility to handle question type variations and provide consistent mapping
export interface QuestionTypeConfig {
  value: string;
  label: string;
  description: string;
  hasOptions: boolean;
  color: string;
}

// Centralized question type definitions
export const QUESTION_TYPES: Record<string, QuestionTypeConfig> = {
  multiple_choice: {
    value: 'multiple_choice',
    label: 'Multiple Choice',
    description: 'Choose one option from multiple choices',
    hasOptions: true,
    color: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
  },
  true_false: {
    value: 'true_false', 
    label: 'True or False',
    description: 'Select true or false',
    hasOptions: true,
    color: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
  },
  short_answer: {
    value: 'short_answer',
    label: 'Short Answer',
    description: 'Provide a brief text answer',
    hasOptions: false,
    color: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'
  },
  enumeration: {
    value: 'enumeration',
    label: 'Enumeration',
    description: 'List multiple items or steps',
    hasOptions: false,
    color: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
  }
};

// Normalize question type - handles both underscore and hyphen formats
export const normalizeQuestionType = (type: string): string => {
  if (!type) return 'multiple_choice';
  
  // Convert hyphens to underscores and lowercase
  const normalized = type.toLowerCase().replace(/-/g, '_');
  
  // Return normalized type if it exists, otherwise default to multiple_choice
  return QUESTION_TYPES[normalized] ? normalized : 'multiple_choice';
};

// Get question type config safely
export const getQuestionTypeConfig = (type: string): QuestionTypeConfig => {
  const normalizedType = normalizeQuestionType(type);
  return QUESTION_TYPES[normalizedType] || QUESTION_TYPES.multiple_choice;
};

// Check if question type should show options
export const questionTypeHasOptions = (type: string): boolean => {
  return getQuestionTypeConfig(type).hasOptions;
};

// Get all question types for select options
export const getQuestionTypeOptions = () => {
  return Object.values(QUESTION_TYPES).map(config => ({
    value: config.value,
    label: config.label
  }));
};

// Get question type label
export const getQuestionTypeLabel = (type: string): string => {
  return getQuestionTypeConfig(type).label;
};

// Get question type color
export const getQuestionTypeColor = (type: string): string => {
  return getQuestionTypeConfig(type).color;
};

// Validate if answer should use selected_option_id or answer_text
export const shouldUseSelectedOption = (type: string): boolean => {
  return questionTypeHasOptions(type);
};