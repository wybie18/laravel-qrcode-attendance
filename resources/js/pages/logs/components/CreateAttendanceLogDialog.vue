<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { shallowRef } from 'vue';
import { store } from '@/actions/App/Http/Controllers/AttendanceLogController';
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
import type { Personnel } from '@/types/personnel';
import { fullName } from '@/utils';

interface Props {
    open: boolean;
    personnels: Personnel[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const personnelId = shallowRef('');

function updateOpen(value: boolean): void {
    emit('update:open', value);
}
</script>

<template>
    <Dialog :open="props.open" @update:open="updateOpen">
        <DialogTrigger as-child>
            <Button>Add Attendance Log</Button>
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
                        personnelId = '';
                    }
                "
                v-slot="{ errors, processing, reset, clearErrors }"
            >
                <DialogHeader>
                    <DialogTitle>Create Attendance Log</DialogTitle>
                    <DialogDescription>
                        Add a new attendance log entry.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2 sm:col-span-2">
                        <Label>Personnel</Label>
                        <Select v-model="personnelId">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select personnel" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="personnel in props.personnels"
                                    :key="personnel.id"
                                    :value="String(personnel.id)"
                                >
                                    {{ fullName(personnel) }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <input type="hidden" name="personnel_id" :value="personnelId" />
                        <InputError :message="errors.personnel_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-log-date">Log Date</Label>
                        <Input id="create-log-date" type="date" name="log_date" required />
                        <InputError :message="errors.log_date" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-time-in">Time In</Label>
                        <Input id="create-time-in" type="time" step="1" name="time_in" />
                        <InputError :message="errors.time_in" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-time-out">Time Out</Label>
                        <Input id="create-time-out" type="time" step="1" name="time_out" />
                        <InputError :message="errors.time_out" />
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
                                    personnelId = '';
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
