<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { update } from '@/actions/App/Http/Controllers/PositionController';
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
        <DialogContent class="sm:max-w-xl">
            <Form
                v-if="props.position"
                :key="`edit-${props.position.id}`"
                v-bind="update.form({ position: props.position.id })"
                :options="{ preserveScroll: true }"
                class="space-y-5"
                @success="() => updateOpen(false)"
                v-slot="{ errors, processing, reset, clearErrors }"
            >
                <DialogHeader>
                    <DialogTitle>Edit Position</DialogTitle>
                    <DialogDescription>
                        Update this position record and save your changes.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-4">
                    <div class="grid gap-2">
                        <Label for="edit-name">Position Name</Label>
                        <Input id="edit-name" name="name" :default-value="props.position.name" />
                        <InputError :message="errors.name" />
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
