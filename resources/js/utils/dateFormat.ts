/**
 * Date and time formatting utilities for the scheduling system
 */

/**
 * Format a date string or Date object to a readable date format
 * @param date - Date string or Date object
 * @param options - Intl.DateTimeFormatOptions
 * @returns Formatted date string
 */
export function formatDate(
  date: string | Date,
  options: Intl.DateTimeFormatOptions = {
    weekday: 'long',
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  }
): string {
  const dateObj = typeof date === 'string' ? new Date(date) : date;
  return dateObj.toLocaleDateString('en-US', options);
}

/**
 * Format a date string or Date object to a readable time format
 * @param date - Date string or Date object
 * @param options - Intl.DateTimeFormatOptions
 * @returns Formatted time string
 */
export function formatTime(
  date: string | Date,
  options: Intl.DateTimeFormatOptions = {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  }
): string {
  const dateObj = typeof date === 'string' ? new Date(date) : date;
  return dateObj.toLocaleTimeString('en-US', options);
}

/**
 * Get relative date string (Today, Tomorrow, or full date)
 * @param date - Date string or Date object
 * @returns Relative date string
 */
export function getRelativeDate(date: string | Date): string {
  const dateObj = typeof date === 'string' ? new Date(date) : date;
  const today = new Date();
  const tomorrow = new Date(today);
  tomorrow.setDate(tomorrow.getDate() + 1);
  
  if (dateObj.toDateString() === today.toDateString()) {
    return 'Today';
  }
  
  if (dateObj.toDateString() === tomorrow.toDateString()) {
    return 'Tomorrow';
  }
  
  return formatDate(dateObj);
}

/**
 * Format duration in minutes to human-readable string
 * @param minutes - Duration in minutes
 * @returns Formatted duration string
 */
export function formatDuration(minutes: number): string {
  if (minutes < 60) {
    return `${minutes} min`;
  }
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  return mins > 0 ? `${hours}h ${mins}m` : `${hours}h`;
}

/**
 * Format a date range
 * @param start - Start date string or Date object
 * @param end - End date string or Date object
 * @returns Formatted date range string
 */
export function formatDateRange(start: string | Date, end: string | Date): string {
  const startObj = typeof start === 'string' ? new Date(start) : start;
  const endObj = typeof end === 'string' ? new Date(end) : end;
  
  // Same day
  if (startObj.toDateString() === endObj.toDateString()) {
    return `${formatDate(startObj)} • ${formatTime(startObj)} - ${formatTime(endObj)}`;
  }
  
  // Different days
  return `${formatDate(startObj)} ${formatTime(startObj)} - ${formatDate(endObj)} ${formatTime(endObj)}`;
}

/**
 * Check if a date is in the past
 * @param date - Date string or Date object
 * @returns True if date is in the past
 */
export function isPast(date: string | Date): boolean {
  const dateObj = typeof date === 'string' ? new Date(date) : date;
  return dateObj < new Date();
}

/**
 * Check if a date is today
 * @param date - Date string or Date object
 * @returns True if date is today
 */
export function isToday(date: string | Date): boolean {
  const dateObj = typeof date === 'string' ? new Date(date) : date;
  const today = new Date();
  return dateObj.toDateString() === today.toDateString();
}

/**
 * Check if a date is tomorrow
 * @param date - Date string or Date object
 * @returns True if date is tomorrow
 */
export function isTomorrow(date: string | Date): boolean {
  const dateObj = typeof date === 'string' ? new Date(date) : date;
  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  return dateObj.toDateString() === tomorrow.toDateString();
}
