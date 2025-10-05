import { ref } from 'vue';

export type NotificationType = 'success' | 'error' | 'info' | 'warning';

export interface Notification {
  type: NotificationType;
  message: string;
}

export function useNotification() {
  const notification = ref<Notification | null>(null);
  let timeoutId: NodeJS.Timeout | null = null;

  const showNotification = (type: NotificationType, message: string, duration: number = 5000) => {
    notification.value = { type, message };
    
    if (timeoutId) {
      clearTimeout(timeoutId);
    }
    
    if (duration > 0) {
      timeoutId = setTimeout(() => {
        notification.value = null;
      }, duration);
    }
  };

  const success = (message: string, duration?: number) => {
    showNotification('success', message, duration);
  };

  const error = (message: string, duration?: number) => {
    showNotification('error', message, duration);
  };

  const info = (message: string, duration?: number) => {
    showNotification('info', message, duration);
  };

  const warning = (message: string, duration?: number) => {
    showNotification('warning', message, duration);
  };

  const clearNotification = () => {
    if (timeoutId) {
      clearTimeout(timeoutId);
    }
    notification.value = null;
  };

  return {
    notification,
    showNotification,
    success,
    error,
    info,
    warning,
    clearNotification
  };
}
