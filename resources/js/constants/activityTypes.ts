import { ref, computed } from 'vue';

// Backend Activity Type Interface
interface BackendActivityType {
  id: number;
  name: string;
  description: string;
  created_at: string;
  updated_at: string;
}

// Reactive store for activity types
const activityTypesData = ref<BackendActivityType[]>([]);
const isLoaded = ref(false);

// Fetch activity types from backend
export const fetchActivityTypes = async (): Promise<BackendActivityType[]> => {
  if (isLoaded.value) {
    return activityTypesData.value;
  }

  try {
    const response = await fetch('/api/activity-types');
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    activityTypesData.value = data;
    isLoaded.value = true;
    return data;
  } catch (error) {
    console.error('Failed to fetch activity types:', error);
    // Fallback to hard-coded values if API fails
    const fallbackTypes: BackendActivityType[] = [
      { id: 1, name: 'quiz', description: 'Interactive quiz with multiple choice questions', created_at: '', updated_at: '' },
      { id: 2, name: 'assignment', description: 'Written assignment or homework', created_at: '', updated_at: '' },
      { id: 3, name: 'project', description: 'Long-term project work', created_at: '', updated_at: '' },
      { id: 4, name: 'assessment', description: 'Formal assessment or exam', created_at: '', updated_at: '' },
      { id: 5, name: 'discussion', description: 'Discussion forum or debate', created_at: '', updated_at: '' }
    ];
    activityTypesData.value = fallbackTypes;
    isLoaded.value = true;
    return fallbackTypes;
  }
};

// Dynamic computed properties based on backend data
export const ACTIVITY_TYPES = computed(() => {
  const types: Record<string, string> = {};
  activityTypesData.value.forEach(type => {
    types[type.name.toUpperCase()] = type.name;
  });
  return types;
});

export const ACTIVITY_TYPE_LABELS = computed(() => {
  const labels: Record<string, string> = {};
  activityTypesData.value.forEach(type => {
    labels[type.name] = type.name.charAt(0).toUpperCase() + type.name.slice(1);
  });
  return labels;
});

export const ACTIVITY_TYPE_DESCRIPTIONS = computed(() => {
  const descriptions: Record<string, string> = {};
  activityTypesData.value.forEach(type => {
    descriptions[type.name] = type.description;
  });
  return descriptions;
});

// Get all activity type names as an array
export const getAllActivityTypeNames = computed(() => 
  activityTypesData.value.map(type => type.name)
);

// Get activity type by name
export const getActivityTypeByName = (name: string): BackendActivityType | undefined => {
  return activityTypesData.value.find(type => type.name === name);
};

// Activity types that have management interfaces (configurable)
const MANAGEABLE_TYPES = ['quiz', 'assignment'];
export const MANAGEABLE_ACTIVITY_TYPES = computed(() => 
  activityTypesData.value.filter(type => MANAGEABLE_TYPES.includes(type.name))
);

// Activity types that students can take/interact with (configurable)  
const INTERACTIVE_TYPES = ['quiz', 'assignment', 'assessment'];
export const INTERACTIVE_ACTIVITY_TYPES = computed(() =>
  activityTypesData.value.filter(type => INTERACTIVE_TYPES.includes(type.name))
);

// Utility functions
export const getActivityTypeLabel = (type: string): string => {
  return ACTIVITY_TYPE_LABELS.value[type] || type.charAt(0).toUpperCase() + type.slice(1);
};

export const getActivityTypeDescription = (type: string): string => {  
  return ACTIVITY_TYPE_DESCRIPTIONS.value[type] || '';
};

export const isManageableActivityType = (type: string): boolean => {
  return MANAGEABLE_ACTIVITY_TYPES.value.some(activityType => activityType.name === type);
};

export const isInteractiveActivityType = (type: string): boolean => {
  return INTERACTIVE_ACTIVITY_TYPES.value.some(activityType => activityType.name === type);
};

// Initialize data on module load
fetchActivityTypes();

// Export the reactive data for direct access if needed
export const activityTypesStore = {
  data: activityTypesData,
  isLoaded,
  refetch: fetchActivityTypes
};