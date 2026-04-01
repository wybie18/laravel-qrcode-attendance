<?php

namespace App\Imports;

use App\Models\Office;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OfficesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        $rows
            ->map(function (Collection $row): array {
                return [
                    'name' => trim((string) $row->get('name', '')),
                    'category' => trim((string) $row->get('category', '')),
                ];
            })
            ->filter(fn (array $row): bool => $row['name'] !== '' && $row['category'] !== '')
            ->unique(fn (array $row): string => $row['name'].'|'.$row['category'])
            ->each(function (array $row): void {
                Office::query()->firstOrCreate([
                    'name' => $row['name'],
                    'category' => $row['category'],
                ]);
            });
    }
}
