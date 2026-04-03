<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { CircleAlert } from 'lucide-vue-next';
import EmailController from '@/actions/App/Http/Controllers/Settings/EmailController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { edit } from '@/routes/email';

type Props = {
    smtp_host: string | null;
    smtp_port: string | number | null;
    smtp_username: string | null;
    smtp_password: string | null;
    smtp_encryption: 'ssl' | 'tls' | null;
    personnel_onboarding_cc_address: string | null;
    personnel_onboarding_cc_name: string | null;
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Email settings',
                href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Email settings" />

    <h1 class="sr-only">Email settings</h1>

    <div class="space-y-6">
        <Heading
            variant="small"
            title="SMTP settings"
            description="Configure SMTP credentials for system emails"
        />

        <Form
            v-bind="EmailController.update.form()"
            class="space-y-6"
            v-slot="{ errors, processing, recentlySuccessful }"
        >
            <div class="grid gap-2">
                <Label for="smtp_host">SMTP Host</Label>
                <Input
                    id="smtp_host"
                    name="smtp_host"
                    :default-value="props.smtp_host ?? 'smtp.gmail.com'"
                    placeholder="smtp.gmail.com"
                    required
                />
                <InputError :message="errors.smtp_host" />
            </div>

            <div class="grid gap-2">
                <Label for="smtp_port">SMTP Port</Label>
                <Input
                    id="smtp_port"
                    type="number"
                    name="smtp_port"
                    :default-value="String(props.smtp_port ?? 465)"
                    placeholder="465"
                    required
                />
                <InputError :message="errors.smtp_port" />
            </div>

            <div class="grid gap-2">
                <Label for="smtp_username">SMTP Username</Label>
                <Input
                    id="smtp_username"
                    name="smtp_username"
                    :default-value="props.smtp_username ?? ''"
                    placeholder="no-reply@example.com"
                    required
                />
                <InputError :message="errors.smtp_username" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center gap-2">
                    <Label for="smtp_password">SMTP Password</Label>
                    <TooltipProvider :delay-duration="0">
                        <Tooltip>
                            <TooltipTrigger as-child>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="icon"
                                    class="h-6 w-6 text-amber-600 hover:text-amber-700"
                                >
                                    <span class="sr-only">SMTP password warning</span>
                                    <CircleAlert class="h-4 w-4" />
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent class="max-w-xs text-sm">
                                Use your email provider's <strong>App Password</strong>, not your regular login password.
                                For Gmail, enable 2-Step Verification first, then generate an App Password.
                            </TooltipContent>
                        </Tooltip>
                    </TooltipProvider>
                </div>
                <Input
                    id="smtp_password"
                    type="password"
                    name="smtp_password"
                    :default-value="props.smtp_password ?? ''"
                    placeholder="SMTP password"
                    required
                />
                <InputError :message="errors.smtp_password" />
            </div>

            <div class="grid gap-2">
                <Label for="smtp_encryption">Encryption</Label>
                <Select
                    name="smtp_encryption"
                    :default-value="props.smtp_encryption ?? 'ssl'"
                >
                    <SelectTrigger id="smtp_encryption">
                        <SelectValue placeholder="Select encryption" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="tls">TLS</SelectItem>
                        <SelectItem value="ssl">SSL</SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.smtp_encryption" />
            </div>

            <div class="grid gap-2">
                <Label for="personnel_onboarding_cc_address">CC Address</Label>
                <Input
                    id="personnel_onboarding_cc_address"
                    type="email"
                    name="personnel_onboarding_cc_address"
                    :default-value="props.personnel_onboarding_cc_address ?? 'admin.r8.ormoccity@deped.gov.ph'"
                    placeholder="admin.r8.ormoccity@deped.gov.ph"
                    required
                />
                <InputError :message="errors.personnel_onboarding_cc_address" />
            </div>

            <div class="grid gap-2">
                <Label for="personnel_onboarding_cc_name">CC Name</Label>
                <Input
                    id="personnel_onboarding_cc_name"
                    name="personnel_onboarding_cc_name"
                    :default-value="props.personnel_onboarding_cc_name ?? 'SDO Ormoc Admin Team'"
                    placeholder="SDO Ormoc Admin Team"
                    required
                />
                <InputError :message="errors.personnel_onboarding_cc_name" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save SMTP settings</Button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-show="recentlySuccessful" class="text-sm text-neutral-600">
                        Saved.
                    </p>
                </Transition>
            </div>
        </Form>
    </div>
</template>
