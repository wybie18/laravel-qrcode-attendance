import type { Personnel } from './types';

export function fullName(personnel: Personnel): string {
    return [personnel.first_name, personnel.middle_name, personnel.last_name]
        .filter(Boolean)
        .join(' ');
}

/**
 * Formats a date string (YYYY-MM-DD) to "Month DD, YYYY"
 * @param dateString - Date string in YYYY-MM-DD format (e.g., "2026-04-02")
 * @returns Formatted date string (e.g., "April 02, 2026")
 */
export function formatDate(dateString: string): string {
    const date = new Date(dateString + 'T00:00:00');
    const options: Intl.DateTimeFormatOptions = {
        year: 'numeric',
        month: 'long',
        day: '2-digit',
    };
    return date.toLocaleDateString('en-US', options);
}

/**
 * Formats a time string (HH:mm:ss) to 12-hour format with AM/PM
 * @param timeString - Time string in HH:mm:ss format (e.g., "14:00:00" or "08:00:00")
 * @returns Formatted time string (e.g., "2:00 PM" or "8:00 AM")
 */
export function formatTime(timeString: string | null): string {
    if (!timeString) return '-';

    const [hours, minutes] = timeString.split(':');
    const hour = parseInt(hours, 10);
    const minute = parseInt(minutes, 10);

    const period = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;

    return `${displayHour}:${minute.toString().padStart(2, '0')} ${period}`;
}
