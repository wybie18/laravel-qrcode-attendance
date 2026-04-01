import type { PaginationMeta, ResourceCollection } from './pagination';
import type { Personnel } from './personnel';

export interface AttendanceLog {
    id: number;
    personnel: Personnel | null;
    log_date: string;
    time_in: string | null;
    time_out: string | null;
}

export interface PaginatedAttendanceLogs {
    data: AttendanceLog[];
    meta: PaginationMeta;
}

export interface AttendanceLogsPageProps {
    attendanceLogs: PaginatedAttendanceLogs;
    personnels: ResourceCollection<Personnel>;
}
