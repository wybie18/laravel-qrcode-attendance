<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { importMethod, template } from '@/actions/App/Http/Controllers/PersonnelController';
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
            <Button variant="outline">Import Excel</Button>
        </DialogTrigger>

        <DialogContent class="sm:max-w-lg">
            <Form
                v-bind="importMethod.form()"
                reset-on-success
                :options="{ preserveScroll: true }"
                class="space-y-5"
                @success="() => updateOpen(false)"
                v-slot="{ errors, processing, reset, clearErrors }"
            >
                <DialogHeader>
                    <DialogTitle>Import Personnel</DialogTitle>
                    <DialogDescription>
                        Upload an Excel file and import personnel records in bulk.
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4">
                    <div class="rounded-md border p-3 text-sm">
                        <p class="text-muted-foreground">
                            Download the template first and fill
                            <span class="font-medium">first_name</span>,
                            <span class="font-medium">last_name</span>,
                            <span class="font-medium">email</span>,
                            <span class="font-medium">office</span>,
                            <span class="font-medium">office_category</span>, and
                            <span class="font-medium">position</span> columns.
                        </p>
                        <Button as-child variant="link" class="h-auto p-0">
                            <a :href="template.url()">Download template</a>
                        </Button>
                    </div>

                    <div class="grid gap-2">
                        <Label for="personnels-file">Excel File</Label>
                        <Input
                            id="personnels-file"
                            type="file"
                            name="file"
                            accept=".xlsx,.xls,.csv"
                            required
                        />
                        <InputError :message="errors.file" />
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
                        {{ processing ? 'Importing...' : 'Import' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
