<script setup lang="ts">
import { X } from 'lucide-vue-next';
import type { AcceptableValue } from 'reka-ui';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { Office } from '@/types/office';

interface Props {
    search: string;
    officeId: number | null;
    dateFrom: string;
    dateTo: string;
    offices: Office[];
    hasActiveFilters: boolean;
}

interface Emits {
    (e: 'update:search', value: string): void;
    (e: 'update:officeId', value: number | null): void;
    (e: 'update:dateFrom', value: string): void;
    (e: 'update:dateTo', value: string): void;
    (e: 'clear'): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();
const ALL_OFFICES_VALUE = 'all';

function updateSearch(event: Event): void {
    const target = event.target as HTMLInputElement;
    emit('update:search', target.value);
}

function updateOfficeId(value: AcceptableValue): void {
    if (typeof value !== 'string') {
        return;
    }

    if (value === ALL_OFFICES_VALUE) {
        emit('update:officeId', null);

        return;
    }

    const officeId = Number(value);

    emit('update:officeId', Number.isNaN(officeId) ? null : officeId);
}

function updateDateFrom(event: Event): void {
    const target = event.target as HTMLInputElement;
    emit('update:dateFrom', target.value);
}

function updateDateTo(event: Event): void {
    const target = event.target as HTMLInputElement;
    emit('update:dateTo', target.value);
}

function handleClear(): void {
    emit('clear');
}
</script>

<template>
    <div class="rounded-xl border border-sidebar-border/70 bg-background p-4">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold">Filters</h3>
                <Button
                    v-if="props.hasActiveFilters"
                    variant="ghost"
                    size="sm"
                    class="h-8 px-2"
                    @click="handleClear"
                >
                    <X class="mr-1 size-4" />
                    Clear All
                </Button>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="space-y-2">
                    <Label for="search" class="text-xs">Search Personnel</Label>
                    <Input
                        id="search"
                        type="text"
                        placeholder="Search by name..."
                        :value="props.search"
                        @input="updateSearch"
                    />
                </div>

                <div class="space-y-2">
                    <Label for="office" class="text-xs">Office</Label>
                    <Select
                        :model-value="props.officeId === null ? ALL_OFFICES_VALUE : props.officeId.toString()"
                        @update:model-value="updateOfficeId"
                    >
                        <SelectTrigger id="office">
                            <SelectValue placeholder="All Offices" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem :value="ALL_OFFICES_VALUE">All Offices</SelectItem>
                            <SelectItem
                                v-for="office in props.offices"
                                :key="office.id"
                                :value="office.id.toString()"
                            >
                                {{ office.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="space-y-2">
                    <Label for="date_from" class="text-xs">Date From</Label>
                    <Input
                        id="date_from"
                        type="date"
                        :value="props.dateFrom"
                        @input="updateDateFrom"
                    />
                </div>

                <div class="space-y-2">
                    <Label for="date_to" class="text-xs">Date To</Label>
                    <Input
                        id="date_to"
                        type="date"
                        :value="props.dateTo"
                        @input="updateDateTo"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
