<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ScanLine } from 'lucide-vue-next';
import { computed, onMounted, ref, shallowRef } from 'vue';
import { scan } from '@/routes/attendance';
import { Button } from '@/components/ui/button';
import { QrcodeStream } from 'vue-qrcode-reader';
import { toast } from 'vue-sonner';

type AttendanceAction = 'time_in' | 'time_out';

type AttendanceScanResponse = {
    message: string;
    data: {
        id: number;
        log_date: string;
        time_in: string | null;
        time_out: string | null;
    };
};

type DetectedQrCode = {
    rawValue?: string;
};
const isCameraActive = shallowRef(false);
const isHydrated = shallowRef(false);
const selectedAction = shallowRef<AttendanceAction>('time_in');
const lastScanAt = shallowRef('');
const lastAutoSubmittedCode = shallowRef('');
const lastAutoSubmittedAt = shallowRef(0);

const hasCameraSupport = computed(
    () => typeof navigator !== 'undefined' && !!navigator.mediaDevices?.getUserMedia,
);

function startCamera(): void {
    isCameraActive.value = true;
}

function stopCamera(): void {
    isCameraActive.value = false;
}

const isPaused = ref(false);

async function handleDetect(detectedCodes: DetectedQrCode[]): Promise<void> {
    const detectedQrCode = detectedCodes.find((result) => !!result.rawValue)?.rawValue?.trim();

    if (!detectedQrCode) return;

    isPaused.value = true;

    const now = Date.now();
    const isDuplicateBurst =
        detectedQrCode === lastAutoSubmittedCode.value
        && now - lastAutoSubmittedAt.value < 2000;

    if (isDuplicateBurst) return;

    lastScanAt.value = new Date().toLocaleTimeString();

    submitAttendance(detectedQrCode, selectedAction.value);

    setTimeout(() => {
        isPaused.value = false;
    }, 500);
}

function handleError(e: Error): void {
    if(e.name == 'NotAllowedError') {
        toast.error('Camera access denied. Please allow camera permissions to use the scanner.');
    } else if (e.name === 'NotFoundError') {
        toast.error('No camera found. Please connect a camera to use the scanner.');
    } else if (e.name === 'NotReadableError') {
        toast.error('Camera is already in use by another application. Please close other applications using the camera and try again.');
    } else if (e.name === 'OverconstrainedError') {
        toast.error('No suitable camera found. Your device may not support the required camera features for scanning.');
    } else {
        toast.error(`Camera error: ${e.message}`);
    }
}

async function submitAttendance(qrCode: string, action: AttendanceAction): Promise<void> {
    if (!qrCode.trim()) {
        toast.error('No QR code detected yet. Align the code inside the scanner frame.');

        return;
    }

    try {
        const response = await axios.post<AttendanceScanResponse>(scan.url(), {
            qr_code: qrCode.trim(),
            action,
        });

        toast.success(response.data.message);
        lastAutoSubmittedCode.value = qrCode.trim();
        lastAutoSubmittedAt.value = Date.now();
    } catch {
        toast.error('Failed to log attendance. Check the QR code and selected action.');
    }
}

onMounted(() => {
    isHydrated.value = true;
    void startCamera();
});
</script>

<template>
    <Head title="Attendance Scanner" />

    <div class="relative h-screen w-full overflow-hidden bg-neutral-950 text-white">
        <QrcodeStream
            v-if="isCameraActive"
            class="absolute inset-0 h-full w-full"
            :constraints="{ facingMode: 'environment' }"
            :paused="isPaused"
            @detect="handleDetect"
            @error="handleError"
        />

        <div
            v-else
            class="absolute inset-0 z-10 flex items-center justify-center bg-neutral-950 px-4 text-center text-sm text-neutral-300"
        >
            Camera is stopped. Tap Start Camera to scan.
        </div>

        <div class="absolute inset-0 bg-linear-to-b from-black/65 via-black/30 to-black/80" />

        <div class="pointer-events-none absolute left-1/2 top-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2 sm:h-72 sm:w-72">
            <span class="absolute left-0 top-0 h-10 w-10 rounded-tl-lg border-l-3 border-t-3 border-blue-300/90" />
            <span class="absolute right-0 top-0 h-10 w-10 rounded-tr-lg border-r-3 border-t-3 border-blue-300/90" />
            <span class="absolute bottom-0 left-0 h-10 w-10 rounded-bl-lg border-b-3 border-l-3 border-blue-300/90" />
            <span class="absolute bottom-0 right-0 h-10 w-10 rounded-br-lg border-b-3 border-r-3 border-blue-300/90" />

            <div class="absolute left-1/2 top-1/2 flex -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-white/25 bg-black/45 p-3 backdrop-blur-sm">
                <ScanLine class="h-8 w-8 text-white/90" />
            </div>
        </div>

                <header class="absolute left-4 right-4 top-4 z-10 flex items-start justify-between gap-3 sm:left-6 sm:right-6 sm:top-6">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Scanner</h1>
            </div>

            <Button
                v-if="!isCameraActive"
                type="button"
                class="min-h-10 border border-white/35 bg-white/15 text-white hover:bg-white/25"
                :disabled="!isHydrated || !hasCameraSupport"
                @click="startCamera"
            >
                Start
            </Button>
            <Button
                v-else
                type="button"
                class="min-h-10 border border-white/35 bg-black/35 text-white hover:bg-white/15"
                @click="stopCamera"
            >
                Stop
            </Button>
        </header>

        <section class="absolute bottom-4 left-4 right-4 z-10 space-y-3 rounded-2xl border border-white/15 bg-black/40 p-3 backdrop-blur-md sm:bottom-6 sm:left-6 sm:right-6 sm:p-4">
            <div class="grid grid-cols-2 gap-2">
                <Button
                    type="button"
                    class="min-h-11"
                    :variant="selectedAction === 'time_in' ? 'default' : 'outline'"
                    :class="selectedAction === 'time_in' ? 'bg-blue-300 text-black hover:bg-blue-200' : 'border border-white/40 bg-black/20 text-white hover:bg-white/10 hover:text-white/90'"
                    @click="selectedAction = 'time_in'"
                >
                Time In
                </Button>
                <Button
                    type="button"
                    class="min-h-11"
                    :variant="selectedAction === 'time_out' ? 'default' : 'outline'"
                    :class="selectedAction === 'time_out' ? 'bg-blue-300 text-black hover:bg-blue-200' : 'border border-white/40 bg-black/20 text-white hover:bg-white/10 hover:text-white/90'"
                    @click="selectedAction = 'time_out'"
                >
                    Time Out
                </Button>
            </div>
        </section>
    </div>
</template>
