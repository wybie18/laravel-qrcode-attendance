import type { PaginationMeta } from "./pagination";

export interface Office {
    id: number;
    name: string;
    category: string;
}

export interface PaginatedOffices {
    data: Office[];
    meta: PaginationMeta;
}

export interface OfficePageProps {
    offices: PaginatedOffices;
}