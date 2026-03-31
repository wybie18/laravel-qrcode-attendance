<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { destroy } from '@/actions/App/Http/Controllers/OfficeController';
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
import type { Office } from '@/types/office';

interface Props {
    open: boolean;
    office: Office | null;
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
                v-if="props.office"
                v-bind="destroy.form({ office: props.office.id })"
                :options="{ preserveScroll: true }"
                @success="() => updateOpen(false)"
                class="space-y-6"
                v-slot="{ processing }"
            >
                <DialogHeader>
                    <DialogTitle>Delete Office</DialogTitle>
                    <DialogDescription>
                        This will permanently remove
                        <span class="font-medium">{{ props.office?.name }}</span>
                        from the office records.
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
