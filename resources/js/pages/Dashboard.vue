<script setup lang="ts">
import { Head, router, usePoll } from '@inertiajs/vue3';
import { shallowRef, watch } from 'vue';
import type { AcceptableValue } from 'reka-ui';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableEmpty,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { dashboard } from '@/routes';
import type {
    DashboardLatestPersonnel,
    DashboardPageProps,
    TrendGranularity,
} from '@/types/dashboard';

import type { ChartConfig } from '@/components/ui/chart';
import { VisAxis, VisGroupedBar, VisXYContainer } from '@unovis/vue';
import {
    ChartContainer,
    ChartCrosshair,
    ChartTooltip,
    ChartTooltipContent,
    componentToString,
} from '@/components/ui/chart';

const props = defineProps<DashboardPageProps>();

const selectedTrend = shallowRef<TrendGranularity>(props.trend.granularity);

const chartConfig = {
    value: {
        label: 'Logs',
        color: 'var(--chart-2)',
    },
} satisfies ChartConfig;

usePoll(10000, {
    only: ['stats', 'trend', 'latestPersonnelLogs'],
});

watch(
    () => props.trend.granularity,
    (granularity) => {
        selectedTrend.value = granularity;
    },
);

function updateTrend(value: AcceptableValue): void {
    if (typeof value !== 'string') {
        return;
    }

    if (value !== 'day' && value !== 'month') {
        return;
    }

    if (value === props.trend.granularity) {
        return;
    }

    router.visit(dashboard.url({ query: { trend: value } }), {
        only: ['stats', 'trend', 'latestPersonnelLogs'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}

function personName(personnel: DashboardLatestPersonnel): string {
    if (!personnel.first_name || !personnel.last_name) {
        return 'Unknown personnel';
    }

    return [personnel.first_name, personnel.middle_name, personnel.last_name]
        .filter(Boolean)
        .join(' ');
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl bg-slate-50/60 p-4 dark:bg-transparent">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">Operations Dashboard</h1>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Live attendance overview for offices and personnel activity.
                </p>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <Card class="border-slate-200/80 shadow-sm dark:border-slate-800">
                <CardHeader class="p-4">
                    <CardDescription>Total Offices</CardDescription>
                    <CardTitle class="font-mono text-2xl text-blue-700 dark:text-blue-300">{{ props.stats.totalOffices }}</CardTitle>
                </CardHeader>
            </Card>

            <Card class="border-slate-200/80 shadow-sm dark:border-slate-800">
                <CardHeader class="p-4">
                    <CardDescription>Total Personnel</CardDescription>
                    <CardTitle class="font-mono text-2xl text-blue-700 dark:text-blue-300">{{ props.stats.totalPersonnel }}</CardTitle>
                </CardHeader>
            </Card>

            <Card class="border-slate-200/80 shadow-sm dark:border-slate-800">
                <CardHeader class="p-4">
                    <CardDescription>Today's Logs</CardDescription>
                    <CardTitle class="font-mono text-2xl text-blue-700 dark:text-blue-300">{{ props.stats.todaysPersonnelLogs }}</CardTitle>
                </CardHeader>
            </Card>

            <Card class="border-slate-200/80 shadow-sm dark:border-slate-800">
                <CardHeader class="p-4">
                    <CardDescription>Today's Time In</CardDescription>
                    <CardTitle class="font-mono text-2xl text-emerald-700 dark:text-emerald-300">{{ props.stats.todaysTimeInCount }}</CardTitle>
                </CardHeader>
            </Card>

            <Card class="border-slate-200/80 shadow-sm dark:border-slate-800">
                <CardHeader class="p-4">
                    <CardDescription>Today's Time Out</CardDescription>
                    <CardTitle class="font-mono text-2xl text-amber-700 dark:text-amber-300">{{ props.stats.todaysTimeOutCount }}</CardTitle>
                </CardHeader>
            </Card>
        </div>

        <div class="grid gap-4 lg:grid-cols-5">
            <Card class="border-slate-200/80 shadow-sm dark:border-slate-800 lg:col-span-5">
                <CardHeader class="flex flex-row items-start justify-between gap-3 pb-3">
                    <div>
                        <CardTitle>Attendance Trend</CardTitle>
                        <CardDescription>Distinct personnel logs over time</CardDescription>
                    </div>

                    <Select :model-value="selectedTrend" @update:model-value="updateTrend">
                        <SelectTrigger class="w-36">
                            <SelectValue placeholder="Select range" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="day">Per Day</SelectItem>
                            <SelectItem value="month">Per Month</SelectItem>
                        </SelectContent>
                    </Select>
                </CardHeader>

                <CardContent>
                    <ChartContainer :config="chartConfig" class="min-h-48 h-48 w-full">
                        <VisXYContainer :data="props.trend.data">
                            <VisGroupedBar
                                :x="(d: any) => new Date(d.bucket).getTime()"
                                :y="[(d: any) => d.value]"
                                :color="[chartConfig.value.color]"
                            />
                            <VisAxis
                                type="x"
                                :x="(d: any) => new Date(d.bucket).getTime()"
                                :tick-format="(d: number) => new Date(d).toLocaleDateString('en-US', props.trend.granularity === 'day' ? { day: 'numeric', month: 'short' } : { month: 'short', year: 'numeric' })"
                                :tick-values="props.trend.data.map(d => new Date(d.bucket).getTime())"
                                :tick-line="false"
                                :domain-line="false"
                                :grid-line="false"
                            />
                            <VisAxis
                                type="y"
                                :tick-line="false"
                                :domain-line="false"
                                :grid-line="true"
                                :tick-format="(tick: number) => tick % 1 === 0 ? String(tick) : ''"
                            />
                            <ChartTooltip />
                            <ChartCrosshair
                                :template="componentToString(chartConfig, ChartTooltipContent, {
                                    labelFormatter(d) {
                                        return new Date(d as number).toLocaleDateString('en-US', props.trend.granularity === 'day' ? { day: 'numeric', month: 'short', year: 'numeric' } : { month: 'long', year: 'numeric' });
                                    },
                                })"
                                :color="[chartConfig.value.color]"
                            />
                        </VisXYContainer>
                    </ChartContainer>
                </CardContent>
            </Card>
        </div>

        <div class="grid gap-4">
            <Card class="border-slate-200/80 shadow-sm dark:border-slate-800">
            <CardHeader>
                <CardTitle>Latest Personnel Logs Today</CardTitle>
                <CardDescription>Most recent unique personnel attendance entries for today.</CardDescription>
            </CardHeader>

            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Personnel</TableHead>
                            <TableHead>Office</TableHead>
                            <TableHead>Position</TableHead>
                            <TableHead>Time In</TableHead>
                            <TableHead>Time Out</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="log in props.latestPersonnelLogs" :key="log.id">
                            <TableCell class="font-medium">
                                {{ personName(log.personnel) }}
                            </TableCell>
                            <TableCell>{{ log.personnel.office_name ?? '-' }}</TableCell>
                            <TableCell>{{ log.personnel.position_name ?? '-' }}</TableCell>
                            <TableCell>{{ log.time_in ?? '-' }}</TableCell>
                            <TableCell>{{ log.time_out ?? '-' }}</TableCell>
                        </TableRow>

                        <TableEmpty v-if="!props.latestPersonnelLogs.length" :colspan="5">
                            No personnel logs found for today.
                        </TableEmpty>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
        </div>
    </div>
</template>
