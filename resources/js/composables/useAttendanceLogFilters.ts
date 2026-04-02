import { router } from '@inertiajs/vue3';
import { computed, shallowRef, watch } from 'vue';

interface AttendanceLogFilters {
    search: string;
    office_id: number | string | null;
    date_from: string;
    date_to: string;
}

interface UseAttendanceLogFiltersOptions {
    url: string;
    initialFilters?: Partial<AttendanceLogFilters>;
    only?: string[];
}

export function useAttendanceLogFilters(
    options: UseAttendanceLogFiltersOptions,
) {
    const { url, initialFilters = {}, only = ['attendanceLogs'] } = options;

    function normalizeOfficeId(value: unknown): number | null {
        if (value === null || value === undefined || value === '') {
            return null;
        }

        if (typeof value === 'number') {
            return Number.isNaN(value) ? null : value;
        }

        if (typeof value === 'string') {
            const parsedValue = Number(value);

            return Number.isNaN(parsedValue) ? null : parsedValue;
        }

        return null;
    }

    const search = shallowRef(initialFilters.search ?? '');
    const officeId = shallowRef<number | null>(
        normalizeOfficeId(initialFilters.office_id),
    );
    const dateFrom = shallowRef(initialFilters.date_from ?? '');
    const dateTo = shallowRef(initialFilters.date_to ?? '');

    const hasActiveFilters = computed(() => {
        return Boolean(
            search.value || officeId.value || dateFrom.value || dateTo.value,
        );
    });

    function applyFilters(): void {
        const params: Record<string, string | number> = {};

        if (search.value) {
            params.search = search.value;
        }

        if (officeId.value) {
            params.office_id = officeId.value;
        }

        if (dateFrom.value) {
            params.date_from = dateFrom.value;
        }

        if (dateTo.value) {
            params.date_to = dateTo.value;
        }

        router.visit(url, {
            data: params,
            only,
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    }

    function clearFilters(): void {
        search.value = '';
        officeId.value = null;
        dateFrom.value = '';
        dateTo.value = '';

        router.visit(url, {
            only,
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    }

    // Debounced filter application for search input
    let searchTimeout: ReturnType<typeof setTimeout> | null = null;

    watch(search, () => {
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 300);
    });

    // Apply filters immediately for select and date inputs
    watch([officeId, dateFrom, dateTo], () => {
        applyFilters();
    });

    return {
        search,
        officeId,
        dateFrom,
        dateTo,
        hasActiveFilters,
        applyFilters,
        clearFilters,
    };
}
