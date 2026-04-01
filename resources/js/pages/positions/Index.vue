<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, shallowRef } from 'vue';
import { exportMethod, index } from '@/actions/App/Http/Controllers/PositionController';
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
import DeletePositionDialog from '@/pages/positions/components/DeletePositionDialog.vue';
import EditPositionDialog from '@/pages/positions/components/EditPositionDialog.vue';
import CreatePositionDialog from '@/pages/positions/components/CreatePositionDialog.vue';
import ImportPositionsDialog from '@/pages/positions/components/ImportPositionsDialog.vue';
import type { PositionPageProps, Position } from '@/types/position';
import { index as positionsIndex } from '@/routes/positions';

const props = defineProps<PositionPageProps>();

const createDialogOpen = shallowRef(false);
const importDialogOpen = shallowRef(false);
const editDialogOpen = shallowRef(false);
const deleteDialogOpen = shallowRef(false);

const selectedPosition = shallowRef<Position | null>(null);

const positionRows = computed(() => props.positions.data);

const currentPage = computed(() => props.positions.meta.current_page);
const lastPage = computed(() => props.positions.meta.last_page);
const totalItems = computed(() => props.positions.meta.total);
const perPage = computed(() => props.positions.meta.per_page);

function visitPage(page: number): void {
    if (page === currentPage.value) {
        return;
    }

    router.visit(index.url({ query: { page } }), {
        only: ['positions'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}

function openEditDialog(position: Position): void {
    selectedPosition.value = position;
    editDialogOpen.value = true;
}

function openDeleteDialog(position: Position): void {
    selectedPosition.value = position;
    deleteDialogOpen.value = true;
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Positions',
                href: positionsIndex(),
            },
        ],
    },
});

</script>

<template>
    <Head title="Positions" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Positions</h1>
                <p class="text-sm text-muted-foreground">
                    Manage position information.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <Button as-child variant="outline">
                    <a :href="exportMethod.url()">Export Excel</a>
                </Button>
                <ImportPositionsDialog v-model:open="importDialogOpen" />
                <CreatePositionDialog v-model:open="createDialogOpen" />
            </div>
        </div>

        <div class="rounded-xl border border-sidebar-border/70">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead class="text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <template v-if="positionRows.length">
                        <TableRow v-for="position in positionRows" :key="position.id">
                            <TableCell class="font-medium">{{ position.name }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Button variant="secondary" size="sm" @click="openEditDialog(position)">
                                        Edit
                                    </Button>
                                    <Button variant="destructive" size="sm" @click="openDeleteDialog(position)">
                                        Delete
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </template>

                    <TableEmpty v-else :colspan="2">
                        No positions found.
                    </TableEmpty>
                </TableBody>
            </Table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-muted-foreground">
                Showing {{ props.positions.meta.from ?? 0 }} to {{ props.positions.meta.to ?? 0 }} of
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

                        <template v-for="(item, itemIndex) in items" :key="`page-${itemIndex}`">
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

        <EditPositionDialog
            v-model:open="editDialogOpen"
            :position="selectedPosition"
        />

        <DeletePositionDialog
            v-model:open="deleteDialogOpen"
            :position="selectedPosition"
        />
    </div>
</template>
