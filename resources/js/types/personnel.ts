import type { Office } from './office';
import type { PaginationMeta, ResourceCollection } from './pagination';
import type { Position } from './position';

export interface Personnel {
    id: number;
    first_name: string;
    middle_name: string | null;
    last_name: string;
    email: string;
    phone_number: string | null;
    qr_code: string | null;
    office_id: number;
    position_id: number;
    office: Office | null;
    position: Position | null;
}

export interface PaginatedPersonnel {
    data: Personnel[];
    meta: PaginationMeta;
}

export interface PersonnelFilters {
    search: string | null;
    office_id: number | null;
}

export interface PersonnelPageProps {
    personnels: PaginatedPersonnel;
    offices: ResourceCollection<Office>;
    positions: ResourceCollection<Position>;
    filters: PersonnelFilters;
}
