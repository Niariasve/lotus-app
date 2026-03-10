import type { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}

export function trimDecimal(value: number) {
    if (value === null || value === undefined) return null;
    return Number(value);
}

export function formatDate(
    value: string | Date | null | undefined,
    locale = 'en-US',
    fallback = '—'
): string {
    if (!value) return fallback;

    const parsed = value instanceof Date ? value : new Date(`${value}T00:00:00`);

    if (Number.isNaN(parsed.getTime())) return fallback;

    return parsed.toLocaleDateString(locale, {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
    });
}

export function formatDateTime(
    value: string | Date | null | undefined,
    options?: Intl.DateTimeFormatOptions,
    locale = 'en-US',
    fallback = '—'
): string {
    if (!value) return fallback;

    const parsed = value instanceof Date ? value : new Date(value);

    if (Number.isNaN(parsed.getTime())) return fallback;

    return parsed.toLocaleString(locale, {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        ...options,
    });
}