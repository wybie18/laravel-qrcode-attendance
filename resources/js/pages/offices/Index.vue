<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, shallowRef } from 'vue';
import { exportMethod, index } from '@/actions/App/Http/Controllers/OfficeController';
import CreateOfficeDialog from '@/pages/offices/components/CreateOfficeDialog.vue';
import DeleteOfficeDialog from '@/pages/offices/components/DeleteOfficeDialog.vue';
import EditOfficeDialog from '@/pages/offices/components/EditOfficeDialog.vue';
import ImportOfficesDialog from '@/pages/offices/components/ImportOfficesDialog.vue';
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
import type { OfficePageProps, Office } from '@/types/office';
import { index as officesIndex } from '@/routes/offices';

const props = defineProps<OfficePageProps>();

const createDialogOpen = shallowRef(false);
const importDialogOpen = shallowRef(false);
const editDialogOpen = shallowRef(false);
const deleteDialogOpen = shallowRef(false);

const selectedOffice = shallowRef<Office | null>(null);

const officesRows = computed(() => props.offices.data);

const currentPage = computed(() => props.offices.meta.current_page);
const lastPage = computed(() => props.offices.meta.last_page);
const totalItems = computed(() => props.offices.meta.total);
const perPage = computed(() => props.offices.meta.per_page);

function visitPage(page: number): void {
    if (page === currentPage.value) {
        return;
    }

    router.visit(index.url({ query: { page } }), {
        only: ['offices'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}

function openEditDialog(office: Office): void {
    selectedOffice.value = office;
    editDialogOpen.value = true;
}

function openDeleteDialog(office: Office): void {
    selectedOffice.value = office;
    deleteDialogOpen.value = true;
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Offices',
                href: officesIndex(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Offices" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Offices</h1>
                <p class="text-sm text-muted-foreground">
                    Manage office information.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <Button as-child variant="outline">
                    <a :href="exportMethod.url()">Export Excel</a>
                </Button>
                <ImportOfficesDialog v-model:open="importDialogOpen" />
                <CreateOfficeDialog
                    v-model:open="createDialogOpen"
                />
            </div>
        </div>

        <div class="rounded-xl border border-sidebar-border/70">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead>Category</TableHead>
                        <TableHead class="text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <template v-if="officesRows.length">
                        <TableRow v-for="office in officesRows" :key="office.id">
                            <TableCell class="font-medium">{{ office.name }}</TableCell>
                            <TableCell>{{ office.category }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Button variant="secondary" size="sm" @click="openEditDialog(office)">
                                        Edit
                                    </Button>
                                    <Button variant="destructive" size="sm" @click="openDeleteDialog(office)">
                                        Delete
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </template>

                    <TableEmpty v-else :colspan="7">
                        No offices found.
                    </TableEmpty>
                </TableBody>
            </Table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-muted-foreground">
                Showing {{ props.offices.meta.from ?? 0 }} to {{ props.offices.meta.to ?? 0 }} of
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

        <EditOfficeDialog
            v-model:open="editDialogOpen"
            :office="selectedOffice"
        />

        <DeleteOfficeDialog
            v-model:open="deleteDialogOpen"
            :office="selectedOffice"
        />
    </div>
</template>
