<script setup lang="ts">
import { Toaster } from '@/components/ui/sonner';
</script>

<template>
    <main class="relative h-screen w-full overflow-hidden bg-neutral-950 text-white">
        <slot />
    </main>
    <Toaster position="top-right" />
</template>
