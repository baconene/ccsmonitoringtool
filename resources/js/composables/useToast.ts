import { ref } from 'vue';

export interface Toast {
  id: string;
  type: 'success' | 'error' | 'info' | 'warning';
  message: string;
  duration?: number;
}

const toasts = ref<Toast[]>([]);

export function useToast() {
  const add = (type: Toast['type'], message: string, duration = 3000) => {
    const id = Date.now().toString();
    const toast: Toast = { id, type, message, duration };
    
    toasts.value.push(toast);
    
    if (duration > 0) {
      setTimeout(() => {
        remove(id);
      }, duration);
    }
    
    return id;
  };

  const remove = (id: string) => {
    const index = toasts.value.findIndex(t => t.id === id);
    if (index > -1) {
      toasts.value.splice(index, 1);
    }
  };

  const success = (message: string, duration?: number) => add('success', message, duration);
  const error = (message: string, duration?: number) => add('error', message, duration);
  const info = (message: string, duration?: number) => add('info', message, duration);
  const warning = (message: string, duration?: number) => add('warning', message, duration);

  return {
    toasts,
    add,
    remove,
    success,
    error,
    info,
    warning
  };
}
