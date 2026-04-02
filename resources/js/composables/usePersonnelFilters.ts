import { router } from '@inertiajs/vue3';
import { computed, shallowRef, watch } from 'vue';

interface PersonnelFilters {
    search: string;
    office_id: number | string | null;
}

interface UsePersonnelFiltersOptions {
    url: string;
    initialFilters?: Partial<PersonnelFilters>;
    only?: string[];
}

export function usePersonnelFilters(options: UsePersonnelFiltersOptions) {
    const { url, initialFilters = {}, only = ['personnels'] } = options;

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

    const hasActiveFilters = computed(() => {
        return Boolean(search.value || officeId.value);
    });

    function applyFilters(): void {
        const params: Record<string, string | number> = {};

        if (search.value) {
            params.search = search.value;
        }

        if (officeId.value) {
            params.office_id = officeId.value;
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

    // Apply filters immediately for office select
    watch(officeId, () => {
        applyFilters();
    });

    return {
        search,
        officeId,
        hasActiveFilters,
        applyFilters,
        clearFilters,
    };
}
