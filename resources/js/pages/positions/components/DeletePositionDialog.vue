<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { destroy } from '@/actions/App/Http/Controllers/PositionController';
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
import type { Position } from '@/types/position';

interface Props {
    open: boolean;
    position: Position | null;
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
                v-if="props.position"
                v-bind="destroy.form({ position: props.position.id })"
                :options="{ preserveScroll: true }"
                @success="() => updateOpen(false)"
                class="space-y-6"
                v-slot="{ processing }"
            >
                <DialogHeader>
                    <DialogTitle>Delete Position</DialogTitle>
                    <DialogDescription>
                        This will permanently remove
                        <span class="font-medium">{{ props.position?.name }}</span>
                        from the position records.
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
