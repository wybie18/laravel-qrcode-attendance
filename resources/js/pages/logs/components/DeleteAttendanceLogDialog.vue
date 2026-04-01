<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { destroy } from '@/actions/App/Http/Controllers/AttendanceLogController';
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
import type { AttendanceLog } from '@/types/attendance-log';
import { fullName } from '@/utils';

interface Props {
    open: boolean;
    attendanceLog: AttendanceLog | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

function updateOpen(value: boolean): void {
    emit('update:open', value);
}
</script>

<template>
    <Dialog :open="props.open" @update:open="updateOpen">
        <DialogContent class="sm:max-w-lg">
            <Form
                v-if="props.attendanceLog"
                v-bind="destroy.form({ attendance_log: props.attendanceLog.id })"
                :options="{ preserveScroll: true }"
                @success="() => updateOpen(false)"
                class="space-y-6"
                v-slot="{ processing }"
            >
                <DialogHeader>
                    <DialogTitle>Delete Attendance Log</DialogTitle>
                    <DialogDescription>
                        This will permanently remove attendance entry for
                        <span class="font-medium">{{ props.attendanceLog.personnel ? fullName(props.attendanceLog.personnel) : 'Unknown personnel' }}</span>
                        on
                        <span class="font-medium">{{ props.attendanceLog.log_date }}</span>.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button type="button" variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Button type="submit" variant="destructive" :disabled="processing">
                        {{ processing ? 'Deleting...' : 'Delete' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
