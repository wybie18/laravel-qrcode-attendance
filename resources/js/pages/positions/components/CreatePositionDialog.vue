<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { store } from '@/actions/App/Http/Controllers/PositionController';
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

interface Props {
    open: boolean;
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
        <DialogTrigger as-child>
            <Button>Add Position</Button>
        </DialogTrigger>

        <DialogContent class="sm:max-w-xl">
            <Form
                v-bind="store.form()"
                reset-on-success
                :options="{ preserveScroll: true }"
                class="space-y-5"
                @success="() => updateOpen(false)"
                v-slot="{ errors, processing, reset, clearErrors }"
            >
                <DialogHeader>
                    <DialogTitle>Create Position</DialogTitle>
                    <DialogDescription>
                        Add a new position record.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-4">
                    <div class="grid gap-2">
                        <Label for="create-name">Position Name</Label>
                        <Input id="create-name" name="name" placeholder="Supervisor" />
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
                        {{ processing ? 'Saving...' : 'Create' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
