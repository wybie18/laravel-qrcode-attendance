<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { shallowRef, watch } from 'vue';
import { update } from '@/actions/App/Http/Controllers/PersonnelController';
import InputError from '@/components/InputError.vue';
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
import type { Personnel } from '@/types/personnel';
import type { Position } from '@/types/position';

interface Props {
    open: boolean;
    personnel: Personnel | null;
    offices: Office [];
    positions: Position[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const officeId = shallowRef('');
const positionId = shallowRef('');

watch(
    () => props.personnel,
    (personnel) => {
        officeId.value = personnel ? String(personnel.office_id) : '';
        positionId.value = personnel ? String(personnel.position_id) : '';
    },
    { immediate: true },
);

function updateOpen(value: boolean): void {
    emit('update:open', value);
}
</script>

<template>
    <Dialog :open="props.open" @update:open="updateOpen">
        <DialogContent class="sm:max-w-xl">
            <Form
                v-if="props.personnel"
                :key="`edit-${props.personnel.id}`"
                v-bind="update.form({ personnel: props.personnel.id })"
                :options="{ preserveScroll: true }"
                class="space-y-5"
                @success="() => updateOpen(false)"
                v-slot="{ errors, processing, reset, clearErrors }"
            >
                <DialogHeader>
                    <DialogTitle>Edit Personnel</DialogTitle>
                    <DialogDescription>
                        Update this personnel record and save your changes.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="edit-first-name">First Name</Label>
                        <Input id="edit-first-name" name="first_name" :default-value="props.personnel.first_name" />
                        <InputError :message="errors.first_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-middle-name">Middle Name</Label>
                        <Input
                            id="edit-middle-name"
                            name="middle_name"
                            :default-value="props.personnel.middle_name ?? ''"
                        />
                        <InputError :message="errors.middle_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-last-name">Last Name</Label>
                        <Input id="edit-last-name" name="last_name" :default-value="props.personnel.last_name" />
                        <InputError :message="errors.last_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-email">Email</Label>
                        <Input
                            id="edit-email"
                            name="email"
                            type="email"
                            :default-value="props.personnel.email"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-phone">Phone Number</Label>
                        <Input
                            id="edit-phone"
                            name="phone_number"
                            :default-value="props.personnel.phone_number ?? ''"
                        />
                        <InputError :message="errors.phone_number" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Office</Label>
                        <Select v-model="officeId">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select office" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="office in props.offices"
                                    :key="office.id"
                                    :value="String(office.id)"
                                >
                                    {{ office.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <input type="hidden" name="office_id" :value="officeId" />
                        <InputError :message="errors.office_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Position</Label>
                        <Select v-model="positionId">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select position" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="position in props.positions"
                                    :key="position.id"
                                    :value="String(position.id)"
                                >
                                    {{ position.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <input type="hidden" name="position_id" :value="positionId" />
                        <InputError :message="errors.position_id" />
                    </div>
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button
                            type="button"
                            variant="secondary"
                            @click="
                                () => {
                                    clearErrors();
                                    reset();
                                    officeId = props.personnel ? String(props.personnel.office_id) : '';
                                    positionId = props.personnel ? String(props.personnel.position_id) : '';
                                }
                            "
                        >
                            Cancel
                        </Button>
                    </DialogClose>
                    <Button type="submit" :disabled="processing">
                        {{ processing ? 'Saving...' : 'Update' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
