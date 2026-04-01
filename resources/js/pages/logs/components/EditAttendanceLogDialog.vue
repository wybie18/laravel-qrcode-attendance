<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { shallowRef, watch } from 'vue';
import { update } from '@/actions/App/Http/Controllers/AttendanceLogController';
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
import type { AttendanceLog } from '@/types/attendance-log';
import type { Personnel } from '@/types/personnel';
import { fullName } from '@/utils';

interface Props {
    open: boolean;
    attendanceLog: AttendanceLog | null;
    personnels: Personnel[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const personnelId = shallowRef('');

watch(
    () => props.attendanceLog,
    (attendanceLog) => {
        personnelId.value = attendanceLog?.personnel?.id ? String(attendanceLog.personnel.id) : '';
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
                v-if="props.attendanceLog"
                :key="`edit-${props.attendanceLog.id}`"
                v-bind="update.form({ attendance_log: props.attendanceLog.id })"
                :options="{ preserveScroll: true }"
                class="space-y-5"
                @success="() => updateOpen(false)"
                v-slot="{ errors, processing, reset, clearErrors }"
            >
                <DialogHeader>
                    <DialogTitle>Edit Attendance Log</DialogTitle>
                    <DialogDescription>
                        Update this attendance log entry and save your changes.
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
                        <Label for="edit-log-date">Log Date</Label>
                        <Input
                            id="edit-log-date"
                            type="date"
                            name="log_date"
                            :default-value="props.attendanceLog.log_date"
                            required
                        />
                        <InputError :message="errors.log_date" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-time-in">Time In</Label>
                        <Input
                            id="edit-time-in"
                            type="time"
                            step="1"
                            name="time_in"
                            :default-value="props.attendanceLog.time_in ?? ''"
                        />
                        <InputError :message="errors.time_in" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-time-out">Time Out</Label>
                        <Input
                            id="edit-time-out"
                            type="time"
                            step="1"
                            name="time_out"
                            :default-value="props.attendanceLog.time_out ?? ''"
                        />
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
                                    personnelId = props.attendanceLog?.personnel?.id
                                        ? String(props.attendanceLog.personnel.id)
                                        : '';
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
