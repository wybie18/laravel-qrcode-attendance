import { PaginationMeta } from "./pagination";

export interface Position {
    id: number;
    name: string;
}

export interface PaginatedPositions {
    data: Position[];
    meta: PaginationMeta;
}

export interface PositionPageProps {
    positions: PaginatedPositions;
}