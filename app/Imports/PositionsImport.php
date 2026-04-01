<?php

namespace App\Imports;

use App\Models\Position;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PositionsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        $timestamp = now();

        $payload = $rows
            ->pluck('name')
            ->filter(fn (mixed $name): bool => is_string($name) && trim($name) !== '')
            ->map(fn (string $name): array => [
                'name' => trim($name),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ])
            ->unique('name')
            ->values()
            ->all();

        if ($payload === []) {
            return;
        }

        Position::query()->upsert($payload, ['name'], ['updated_at']);
    }
}
