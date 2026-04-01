<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, shallowRef } from 'vue';
import { exportMethod, index } from '@/actions/App/Http/Controllers/PersonnelController';
import CreatePersonnelDialog from '@/pages/personnel/components/CreatePersonnelDialog.vue';
import DeletePersonnelDialog from '@/pages/personnel/components/DeletePersonnelDialog.vue';
import EditPersonnelDialog from '@/pages/personnel/components/EditPersonnelDialog.vue';
import ImportPersonnelsDialog from '@/pages/personnel/components/ImportPersonnelsDialog.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
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
import { Spinner } from '@/components/ui/spinner';
import type { PersonnelPageProps, Personnel } from '@/types/personnel';
import { fullName } from '@/utils';
import { index as personnelsIndex } from '@/routes/personnels';
import QrcodeVue from 'qrcode.vue';
import axios from 'axios';

const props = defineProps<PersonnelPageProps>();

const createDialogOpen = shallowRef(false);
const importDialogOpen = shallowRef(false);
const showDialogOpen = shallowRef(false);
const editDialogOpen = shallowRef(false);
const deleteDialogOpen = shallowRef(false);
const isDownloadingQr = shallowRef(false);

const selectedPersonnel = shallowRef<Personnel | null>(null);

const personnelRows = computed(() => props.personnels.data);
const offices = computed(() => props.offices.data);
const positions = computed(() => props.positions.data);

const currentPage = computed(() => props.personnels.meta.current_page);
const lastPage = computed(() => props.personnels.meta.last_page);
const totalItems = computed(() => props.personnels.meta.total);
const perPage = computed(() => props.personnels.meta.per_page);

function visitPage(page: number): void {
    if (page === currentPage.value) {
        return;
    }

    router.visit(index.url({ query: { page } }), {
        only: ['personnels'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}

function openShowDialog(personnel: Personnel): void {
    selectedPersonnel.value = personnel;
    showDialogOpen.value = true;
}

function openEditDialog(personnel: Personnel): void {
    selectedPersonnel.value = personnel;
    editDialogOpen.value = true;
}

function openDeleteDialog(personnel: Personnel): void {
    selectedPersonnel.value = personnel;
    deleteDialogOpen.value = true;
}

async function downloadQrCode(): Promise<void> {
    if (!selectedPersonnel.value || !selectedPersonnel.value.qr_code) {
        return;
    }

    isDownloadingQr.value = true;

    try {
        const response = await axios.post('/qr-code/download', { 
            qr_data: selectedPersonnel.value.qr_code,
            filename: `qr-${selectedPersonnel.value.last_name}-${selectedPersonnel.value.id}`,
        }, {
            responseType: 'blob',
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));

        const link = document.createElement('a');
        link.href = url;

        const contentDisposition = response.headers['content-disposition'];
        let fileName = 'downloaded-qr.png';
        if (contentDisposition) {
            const fileNameMatch = contentDisposition.match(/filename="?([^"]+)"?/);
            if (fileNameMatch && fileNameMatch.length === 2) {
                fileName = fileNameMatch[1];
            }
        }
        
        link.setAttribute('download', fileName);
        document.body.appendChild(link);
        link.click();
        
        // 4. Cleanup
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error downloading QR code:', error);
    } finally {
        isDownloadingQr.value = false;
    }
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Personnel',
                href: personnelsIndex(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Personnel" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Personnel</h1>
                <p class="text-sm text-muted-foreground">
                    Manage personnel profiles, assignments, and QR details.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <Button as-child variant="outline">
                    <a :href="exportMethod.url()">Export Excel</a>
                </Button>
                <ImportPersonnelsDialog v-model:open="importDialogOpen" />
                <CreatePersonnelDialog
                    v-model:open="createDialogOpen"
                    :offices="offices"
                    :positions="positions"
                />
            </div>
        </div>

        <div class="rounded-xl border border-sidebar-border/70">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Phone</TableHead>
                        <TableHead>Office</TableHead>
                        <TableHead>Position</TableHead>
                        <TableHead class="text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <template v-if="personnelRows.length">
                        <TableRow v-for="personnel in personnelRows" :key="personnel.id">
                            <TableCell class="font-medium">{{ fullName(personnel) }}</TableCell>
                            <TableCell>{{ personnel.email }}</TableCell>
                            <TableCell>{{ personnel.phone_number ?? '-' }}</TableCell>
                            <TableCell>{{ personnel.office?.name ?? '-' }}</TableCell>
                            <TableCell>{{ personnel.position?.name ?? '-' }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Button variant="outline" size="sm" @click="openShowDialog(personnel)">
                                        Show
                                    </Button>
                                    <Button variant="secondary" size="sm" @click="openEditDialog(personnel)">
                                        Edit
                                    </Button>
                                    <Button variant="destructive" size="sm" @click="openDeleteDialog(personnel)">
                                        Delete
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </template>

                    <TableEmpty v-else :colspan="7">
                        No personnel records yet.
                    </TableEmpty>
                </TableBody>
            </Table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-muted-foreground">
                Showing {{ props.personnels.meta.from ?? 0 }} to {{ props.personnels.meta.to ?? 0 }} of
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

        <Dialog v-model:open="showDialogOpen">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>Personnel Details</DialogTitle>
                    <DialogDescription>
                        Review the selected personnel profile details.
                    </DialogDescription>
                </DialogHeader>

                <dl v-if="selectedPersonnel" class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                    <!-- 2 Column span and center -->
                     
                     <div class="col-span-1 sm:col-span-2 mx-auto text-center">
                        <span class="text-muted-foreground">QR Code</span>
                        <div class="mt-2">
                            <QrcodeVue
                                :value="selectedPersonnel.qr_code ?? ''"
                                :size="200"
                                level="H"
                                include-margin
                            />
                        </div>

                        <Button
                            class="mt-3"
                            size="sm"
                            variant="outline"
                            :disabled="!selectedPersonnel.qr_code || isDownloadingQr"
                            @click="downloadQrCode"
                        >
                            <Spinner v-if="isDownloadingQr" class="mr-2" />
                            {{ isDownloadingQr ? 'Downloading...' : 'Download QR' }}
                        </Button>

                     </div>
                    <div class="col-span-2 text-center">
                        <dt class="text-muted-foreground">Full Name</dt>
                        <dd class="font-medium">{{ fullName(selectedPersonnel) }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Email</dt>
                        <dd class="font-medium">{{ selectedPersonnel.email }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Phone</dt>
                        <dd class="font-medium">{{ selectedPersonnel.phone_number ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Office</dt>
                        <dd class="font-medium">{{ selectedPersonnel.office?.name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Position</dt>
                        <dd class="font-medium">{{ selectedPersonnel.position?.name ?? '-' }}</dd>
                    </div>
                </dl>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="secondary">Close</Button>
                    </DialogClose>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <EditPersonnelDialog
            v-model:open="editDialogOpen"
            :personnel="selectedPersonnel"
            :offices="offices"
            :positions="positions"
        />

        <DeletePersonnelDialog
            v-model:open="deleteDialogOpen"
            :personnel="selectedPersonnel"
        />
    </div>
</template>
