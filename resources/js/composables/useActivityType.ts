import type { Activity } from '@/types';

export function useActivityType() {
  // Get activity type name with fallback
  // Handle both camelCase (activityType) and snake_case (activity_type) from Laravel
  const getActivityTypeName = (activity: Activity | any) => {
    const activityType = activity.activityType || activity.activity_type;
    return activityType?.name || 'Unknown';
  };

  // Get activity type badge color classes
  const getActivityTypeBadgeClass = (typeName?: string) => {
    if (!typeName) return 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
    
    const name = typeName.toLowerCase();
    if (name.includes('quiz')) {
      return 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400';
    } else if (name.includes('assignment')) {
      return 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400';
    } else if (name.includes('exercise')) {
      return 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400';
    } else if (name.includes('assessment') || name.includes('exam') || name.includes('test')) {
      return 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400';
    }
    return 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
  };

  // Get activity stats display
  const getActivityStats = (activity: Activity) => {
    const stats: string[] = [];
    
    if (activity.question_count) {
      stats.push(`${activity.question_count} question${activity.question_count !== 1 ? 's' : ''}`);
    }
    
    if (activity.total_points) {
      stats.push(`${activity.total_points} points`);
    }
    
    if (activity.has_due_date) {
      stats.push('Has due date');
    }
    
    return stats;
  };

  return {
    getActivityTypeName,
    getActivityTypeBadgeClass,
    getActivityStats
  };
}
