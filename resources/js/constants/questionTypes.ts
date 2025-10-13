// Question Type Constants - Single Source of Truth
export const QUESTION_TYPES = {
  MULTIPLE_CHOICE: 'multiple_choice',
  TRUE_FALSE: 'true_false', 
  SHORT_ANSWER: 'short_answer',
  ENUMERATION: 'enumeration'
} as const;

export type QuestionType = typeof QUESTION_TYPES[keyof typeof QUESTION_TYPES];

export const QUESTION_TYPE_LABELS = {
  [QUESTION_TYPES.MULTIPLE_CHOICE]: 'Multiple Choice',
  [QUESTION_TYPES.TRUE_FALSE]: 'True or False',
  [QUESTION_TYPES.SHORT_ANSWER]: 'Short Answer',
  [QUESTION_TYPES.ENUMERATION]: 'Enumeration'
} as const;

export const QUESTION_TYPE_OPTIONS = [
  { value: QUESTION_TYPES.MULTIPLE_CHOICE, label: QUESTION_TYPE_LABELS[QUESTION_TYPES.MULTIPLE_CHOICE] },
  { value: QUESTION_TYPES.TRUE_FALSE, label: QUESTION_TYPE_LABELS[QUESTION_TYPES.TRUE_FALSE] },
  { value: QUESTION_TYPES.SHORT_ANSWER, label: QUESTION_TYPE_LABELS[QUESTION_TYPES.SHORT_ANSWER] },
  { value: QUESTION_TYPES.ENUMERATION, label: QUESTION_TYPE_LABELS[QUESTION_TYPES.ENUMERATION] }
] as const;

export const QUESTION_TYPE_COLORS = {
  [QUESTION_TYPES.MULTIPLE_CHOICE]: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
  [QUESTION_TYPES.TRUE_FALSE]: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
  [QUESTION_TYPES.SHORT_ANSWER]: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
  [QUESTION_TYPES.ENUMERATION]: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
} as const;

// Utility functions
export const isMultipleChoiceType = (type: string): boolean => {
  return type === QUESTION_TYPES.MULTIPLE_CHOICE || type === QUESTION_TYPES.TRUE_FALSE;
};

export const isTextAnswerType = (type: string): boolean => {
  return type === QUESTION_TYPES.SHORT_ANSWER || type === QUESTION_TYPES.ENUMERATION;
};

export const getQuestionTypeLabel = (type: string): string => {
  return QUESTION_TYPE_LABELS[type as QuestionType] || type;
};

export const getQuestionTypeColor = (type: string): string => {
  return QUESTION_TYPE_COLORS[type as QuestionType] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};