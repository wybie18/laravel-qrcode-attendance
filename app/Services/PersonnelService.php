<?php

namespace App\Services;

use App\Models\Personnel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PersonnelService
{
    /**
     * @param  array{
     *     first_name: string,
     *     middle_name?: string|null,
     *     last_name: string,
     *     email: string,
     *     phone_number?: string|null,
     *     office_id: int,
     *     position_id: int
     * }  $validated
     */
    public function store(array $validated): Personnel
    {
        $validated['qr_code'] = $this->generateQrCode($validated);

        return DB::transaction(function () use ($validated): Personnel {
            return Personnel::query()
                ->create($validated)
                ->load(['office', 'position']);
        });
    }

    /**
     * @param  array{
     *     first_name: string,
     *     middle_name?: string|null,
     *     last_name: string,
     *     email: string,
     *     phone_number?: string|null,
     *     office_id: int,
     *     position_id: int
     * }  $validated
     */
    public function update(Personnel $personnel, array $validated): Personnel
    {
        $validated['qr_code'] = $this->generateQrCode($validated);
        
        return DB::transaction(function () use ($personnel, $validated): Personnel {
            $personnel->update($validated);

            return $personnel->fresh()->load(['office', 'position']);
        });
    }

    public function generateQrCode(array $data): string
    {
        $qrIdentifier = (string) Str::uuid();

        return $qrIdentifier;
    }
}
