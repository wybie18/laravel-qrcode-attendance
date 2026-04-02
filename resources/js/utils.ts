import type { Personnel } from "./types";

export function fullName(personnel: Personnel): string {
    return [personnel.first_name, personnel.middle_name, personnel.last_name]
        .filter(Boolean)
        .join(' ');
}