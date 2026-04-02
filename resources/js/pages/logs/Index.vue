<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, shallowRef } from 'vue';
import {
    exportMethod,
    index,
} from '@/actions/App/Http/Controllers/AttendanceLogController';
import { Button } from '@/components/ui/button';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationFirst,
    PaginationItem,
    PaginationLast,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import {
    Table,
    TableBody,
    TableCell,
    TableEmpty,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useAttendanceLogFilters } from '@/composables/useAttendanceLogFilters';
import AttendanceLogFilters from '@/pages/logs/components/AttendanceLogFilters.vue';
import CreateAttendanceLogDialog from '@/pages/logs/components/CreateAttendanceLogDialog.vue';
import DeleteAttendanceLogDialog from '@/pages/logs/components/DeleteAttendanceLogDialog.vue';
import EditAttendanceLogDialog from '@/pages/logs/components/EditAttendanceLogDialog.vue';
import { index as attendanceLogsIndex } from '@/routes/attendance-logs';
import type {
    AttendanceLog,
    AttendanceLogsPageProps,
} from '@/types/attendance-log';
import { fullName, formatDate, formatTime } from '@/utils';

const props = defineProps<AttendanceLogsPageProps>();

const createDialogOpen = shallowRef(false);
const editDialogOpen = shallowRef(false);
const deleteDialogOpen = shallowRef(false);

const selectedAttendanceLog = shallowRef<AttendanceLog | null>(null);

const attendanceLogRows = computed(() => props.attendanceLogs.data);
const personnels = computed(() => props.personnels.data);
const offices = computed(() => props.offices.data);

const currentPage = computed(() => props.attendanceLogs.meta.current_page);
const lastPage = computed(() => props.attendanceLogs.meta.last_page);
const totalItems = computed(() => props.attendanceLogs.meta.total);
const perPage = computed(() => props.attendanceLogs.meta.per_page);

const filters = useAttendanceLogFilters({
    url: index.url(),
    initialFilters: props.filters,
});

function visitPage(page: number): void {
    if (page === currentPage.value) {
        return;
    }

    router.visit(index.url({ query: { page } }), {
        only: ['attendanceLogs'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}

function openEditDialog(attendanceLog: AttendanceLog): void {
    selectedAttendanceLog.value = attendanceLog;
    editDialogOpen.value = true;
}

function openDeleteDialog(attendanceLog: AttendanceLog): void {
    selectedAttendanceLog.value = attendanceLog;
    deleteDialogOpen.value = true;
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Attendance Logs',
                href: attendanceLogsIndex(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Attendance Logs" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
    >
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Attendance Logs
                </h1>
                <p class="text-sm text-muted-foreground">
                    Manage attendance entries for all personnel.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <Button as-child variant="outline">
                    <a :href="exportMethod.url()">Export Excel</a>
                </Button>

                <CreateAttendanceLogDialog
                    v-model:open="createDialogOpen"
                    :personnels="personnels"
                />
            </div>
        </div>

        <AttendanceLogFilters
            v-model:search="filters.search.value"
            v-model:office-id="filters.officeId.value"
            v-model:date-from="filters.dateFrom.value"
            v-model:date-to="filters.dateTo.value"
            :offices="offices"
            :has-active-filters="filters.hasActiveFilters.value"
            @clear="filters.clearFilters"
        />

        <div class="rounded-xl border border-sidebar-border/70">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Personnel</TableHead>
                        <TableHead>Log Date</TableHead>
                        <TableHead>Time In</TableHead>
                        <TableHead>Time Out</TableHead>
                        <TableHead class="text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <template v-if="attendanceLogRows.length">
                        <TableRow
                            v-for="attendanceLog in attendanceLogRows"
                            :key="attendanceLog.id"
                        >
                            <TableCell class="font-medium">
                                {{
                                    attendanceLog.personnel
                                        ? fullName(attendanceLog.personnel)
                                        : 'Unknown personnel'
                                }}
                            </TableCell>
                            <TableCell>{{ formatDate(attendanceLog.log_date) }}</TableCell>
                            <TableCell>{{
                                formatTime(attendanceLog.time_in)
                            }}</TableCell>
                            <TableCell>{{
                                formatTime(attendanceLog.time_out)
                            }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="secondary"
                                        size="sm"
                                        @click="openEditDialog(attendanceLog)"
                                    >
                                        Edit
                                    </Button>
                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        @click="openDeleteDialog(attendanceLog)"
                                    >
                                        Delete
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </template>

                    <TableEmpty v-else :colspan="5">
                        No attendance logs found.
                    </TableEmpty>
                </TableBody>
            </Table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-muted-foreground">
                Showing {{ props.attendanceLogs.meta.from ?? 0 }} to
                {{ props.attendanceLogs.meta.to ?? 0 }} of
                {{ totalItems }} records.
            </p>

            <div>
                <Pagination
                    v-if="lastPage > 1"
                    v-slot="{ page }"
                    :page="currentPage"
                    :items-per-page="perPage"
                    :total="totalItems"
                    :sibling-count="1"
                    show-edges
                    @update:page="visitPage"
                >
                    <PaginationContent v-slot="{ items }">
                        <PaginationFirst />
                        <PaginationPrevious />

                        <template
                            v-for="(item, itemIndex) in items"
                            :key="`page-${itemIndex}`"
                        >
                            <PaginationItem
                                v-if="item.type === 'page'"
                                :value="item.value"
                                :is-active="item.value === page"
                            >
                                {{ item.value }}
                            </PaginationItem>

                            <PaginationEllipsis v-else :index="itemIndex" />
                        </template>

                        <PaginationNext />
                        <PaginationLast />
                    </PaginationContent>
                </Pagination>
            </div>
        </div>

        <EditAttendanceLogDialog
            v-model:open="editDialogOpen"
            :attendance-log="selectedAttendanceLog"
            :personnels="personnels"
        />

        <DeleteAttendanceLogDialog
            v-model:open="deleteDialogOpen"
            :attendance-log="selectedAttendanceLog"
        />
    </div>
</template>
