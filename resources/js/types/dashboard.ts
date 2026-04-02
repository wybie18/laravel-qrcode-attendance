export type TrendGranularity = 'day' | 'month';

export interface DashboardStatSummary {
    totalOffices: number;
    totalPersonnel: number;
    todaysPersonnelLogs: number;
    todaysTimeInCount: number;
    todaysTimeOutCount: number;
}

export interface DashboardTrendPoint {
    bucket: string;
    label: string;
    value: number;
}

export interface DashboardTrend {
    granularity: TrendGranularity;
    data: DashboardTrendPoint[];
}

export interface DashboardLatestPersonnel {
    id: number | null;
    first_name: string | null;
    middle_name: string | null;
    last_name: string | null;
    office_name: string | null;
    position_name: string | null;
}

export interface DashboardLatestPersonnelLog {
    id: number;
    log_date: string;
    time_in: string | null;
    time_out: string | null;
    personnel: DashboardLatestPersonnel;
}

export interface DashboardPageProps {
    stats: DashboardStatSummary;
    trend: DashboardTrend;
    latestPersonnelLogs: DashboardLatestPersonnelLog[];
}
