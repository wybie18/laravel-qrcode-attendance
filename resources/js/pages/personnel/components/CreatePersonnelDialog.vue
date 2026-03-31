<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { shallowRef } from 'vue';
import { store } from '@/actions/App/Http/Controllers/PersonnelController';
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
    DialogTrigger,
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
import type { Position } from '@/types/position';

interface Props {
    open: boolean;
    offices: Office[];
    positions: Position[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const officeId = shallowRef('');
const positionId = shallowRef('');

function updateOpen(value: boolean): void {
    emit('update:open', value);
}
</script>

<template>
    <Dialog :open="props.open" @update:open="updateOpen">
        <DialogTrigger as-child>
            <Button>Add Personnel</Button>
        </DialogTrigger>

        <DialogContent class="sm:max-w-xl">
            <Form
                v-bind="store.form()"
                reset-on-success
                :options="{ preserveScroll: true }"
                class="space-y-5"
                @success="
                    () => {
                        updateOpen(false);
                        officeId = '';
                        positionId = '';
                    }
                "
                v-slot="{ errors, processing, reset, clearErrors }"
            >
                <DialogHeader>
                    <DialogTitle>Create Personnel</DialogTitle>
                    <DialogDescription>
                        Fill out the profile details to add a new personnel record.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="create-first-name">First Name</Label>
                        <Input id="create-first-name" name="first_name" placeholder="Juan" />
                        <InputError :message="errors.first_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-middle-name">Middle Name</Label>
                        <Input id="create-middle-name" name="middle_name" placeholder="Santos" />
                        <InputError :message="errors.middle_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-last-name">Last Name</Label>
                        <Input id="create-last-name" name="last_name" placeholder="Reyes" />
                        <InputError :message="errors.last_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-email">Email</Label>
                        <Input
                            id="create-email"
                            name="email"
                            type="email"
                            placeholder="personnel@company.com"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-phone">Phone Number</Label>
                        <Input id="create-phone" name="phone_number" placeholder="09123456789" />
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
                                    officeId = '';
                                    positionId = '';
                                }
                            "
                        >
                            Cancel
                        </Button>
                    </DialogClose>
                    <Button type="submit" :disabled="processing">
                        {{ processing ? 'Saving...' : 'Create' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
