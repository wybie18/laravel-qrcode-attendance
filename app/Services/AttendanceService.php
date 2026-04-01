<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\Personnel;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    /**
     * @param  array{
     *     personnel_id: int,
     *     log_date: string,
     *     time_in?: string|null,
     *     time_out?: string|null
     * }  $validated
     */
    public function create(array $validated): AttendanceLog
    {
        return DB::transaction(function () use ($validated): AttendanceLog {
            return AttendanceLog::query()
                ->create($validated)
                ->load('personnel');
        });
    }

    /**
     * @param  array{
     *     personnel_id: int,
     *     log_date: string,
     *     time_in?: string|null,
     *     time_out?: string|null
     * }  $validated
     */
    public function update(AttendanceLog $attendanceLog, array $validated): AttendanceLog
    {
        return DB::transaction(function () use ($attendanceLog, $validated): AttendanceLog {
            $attendanceLog->update($validated);

            return $attendanceLog->fresh()->load('personnel');
        });
    }

    public function delete(AttendanceLog $attendanceLog): void
    {
        $attendanceLog->delete();
    }

    public function logByQrCode(string $qrCode, string $action, ?CarbonImmutable $loggedAt = null): AttendanceLog
    {
        $loggedAt ??= CarbonImmutable::now();

        $personnel = Personnel::query()->where('qr_code', $qrCode)->first();

        if (! $personnel) {
            throw ValidationException::withMessages([
                'qr_code' => ['Personnel with this QR code was not found.'],
            ]);
        }

        if ($action === 'time_in') {
            return AttendanceLog::query()
                ->create([
                    'personnel_id' => $personnel->id,
                    'log_date' => $loggedAt->toDateString(),
                    'time_in' => $loggedAt->format('H:i:s'),
                    'time_out' => null,
                ])
                ->load('personnel');
        }

        if ($action === 'time_out') {
            $openAttendanceLog = AttendanceLog::query()
                ->where('personnel_id', $personnel->id)
                ->whereDate('log_date', $loggedAt->toDateString())
                ->whereNotNull('time_in')
                ->whereNull('time_out')
                ->orderByDesc('time_in')
                ->orderByDesc('id')
                ->first();

            if (! $openAttendanceLog) {
                throw ValidationException::withMessages([
                    'action' => ['No open time-in record found for this personnel today.'],
                ]);
            }

            $openAttendanceLog->update([
                'time_out' => $loggedAt->format('H:i:s'),
            ]);

            return $openAttendanceLog->fresh()->load('personnel');
        }

        throw ValidationException::withMessages([
            'action' => ['Invalid action. Use time_in or time_out.'],
        ]);
    }
}
