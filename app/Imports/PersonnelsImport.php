<?php

namespace App\Imports;

use App\Models\Office;
use App\Models\Personnel;
use App\Models\Position;
use App\Services\PersonnelOnboardingEmailService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PersonnelsImport implements ToCollection, WithHeadingRow
{
    public function __construct(
        private readonly PersonnelOnboardingEmailService $personnelOnboardingEmailService,
    ) {}

    public function collection(Collection $rows): void
    {
        $rows
            ->map(function (Collection $row): array {
                return [
                    'first_name' => trim((string) $row->get('first_name', '')),
                    'middle_name' => trim((string) $row->get('middle_name', '')),
                    'last_name' => trim((string) $row->get('last_name', '')),
                    'email' => trim((string) $row->get('email', '')),
                    'phone_number' => trim((string) $row->get('phone_number', '')),
                    'office' => trim((string) $row->get('office', '')),
                    'office_category' => trim((string) $row->get('office_category', '')),
                    'position' => trim((string) $row->get('position', '')),
                ];
            })
            ->filter(function (array $row): bool {
                return $row['first_name'] !== ''
                    && $row['last_name'] !== ''
                    && $row['email'] !== ''
                    && $row['office'] !== ''
                    && $row['position'] !== '';
            })
            ->unique(fn (array $row): string => strtolower($row['email']))
            ->each(function (array $row): void {
                $office = Office::query()->where('name', $row['office'])->first();

                if (! $office) {
                    if ($row['office_category'] === '') {
                        return;
                    }

                    $office = Office::query()->create([
                        'name' => $row['office'],
                        'category' => $row['office_category'],
                    ]);
                }

                $position = Position::query()->firstOrCreate([
                    'name' => $row['position'],
                ]);

                $existingPersonnel = Personnel::query()
                    ->where('email', $row['email'])
                    ->first();

                $personnel = Personnel::query()->updateOrCreate(
                    ['email' => $row['email']],
                    [
                        'first_name' => $row['first_name'],
                        'middle_name' => $row['middle_name'] !== '' ? $row['middle_name'] : null,
                        'last_name' => $row['last_name'],
                        'phone_number' => $row['phone_number'] !== '' ? $row['phone_number'] : null,
                        'qr_code' => $existingPersonnel?->qr_code ?: (string) Str::uuid(),
                        'office_id' => $office->id,
                        'position_id' => $position->id,
                    ]
                );

                if ($personnel->wasRecentlyCreated) {
                    $this->personnelOnboardingEmailService->sendWelcomeEmail($personnel);
                }
            });
    }
}
