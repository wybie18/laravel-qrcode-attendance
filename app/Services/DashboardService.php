<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\Office;
use App\Models\Personnel;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * @return array{
     *     stats: array{
     *         totalOffices: int,
     *         totalPersonnel: int,
     *         todaysPersonnelLogs: int,
     *         todaysTimeInCount: int,
     *         todaysTimeOutCount: int
     *     },
     *     trend: array{
     *         granularity: 'day'|'month',
     *         data: array<int, array{bucket: string, label: string, value: int}>
     *     },
     *     latestPersonnelLogs: array<int, array{
     *         id: int,
     *         log_date: string,
     *         time_in: string|null,
     *         time_out: string|null,
     *         personnel: array{
     *             id: int|null,
     *             first_name: string|null,
     *             middle_name: string|null,
     *             last_name: string|null,
     *             office_name: string|null,
     *             position_name: string|null
     *         }
     *     }>
     * }
     */
    public function getDashboardData(string $granularity = 'day'): array
    {
        $today = CarbonImmutable::today();
        $selectedGranularity = $granularity === 'month' ? 'month' : 'day';

        return [
            'stats' => $this->getStats($today),
            'trend' => [
                'granularity' => $selectedGranularity,
                'data' => $this->getTrendData($selectedGranularity, $today),
            ],
            'latestPersonnelLogs' => $this->getLatestPersonnelLogsToday($today),
        ];
    }

    /**
     * @return array{
     *     totalOffices: int,
     *     totalPersonnel: int,
     *     todaysPersonnelLogs: int,
     *     todaysTimeInCount: int,
     *     todaysTimeOutCount: int
     * }
     */
    private function getStats(CarbonImmutable $today): array
    {
        $todayDate = $today->toDateString();

        return [
            'totalOffices' => Office::query()->count(),
            'totalPersonnel' => Personnel::query()->count(),
            'todaysPersonnelLogs' => AttendanceLog::query()
                ->whereDate('log_date', $todayDate)
                ->count('personnel_id'),
            'todaysTimeInCount' => AttendanceLog::query()
                ->whereDate('log_date', $todayDate)
                ->whereNotNull('time_in')
                ->count(),
            'todaysTimeOutCount' => AttendanceLog::query()
                ->whereDate('log_date', $todayDate)
                ->whereNotNull('time_out')
                ->count(),
        ];
    }

    /**
     * @return array<int, array{bucket: string, label: string, value: int}>
     */
    private function getTrendData(string $granularity, CarbonImmutable $today): array
    {
        if ($granularity === 'month') {
            return $this->getMonthlyTrendData($today);
        }

        return $this->getDailyTrendData($today);
    }

    /**
     * @return array<int, array{bucket: string, label: string, value: int}>
     */
    private function getDailyTrendData(CarbonImmutable $today): array
    {
        $startDate = $today->subDays(13);

        $rows = AttendanceLog::query()
            ->selectRaw('DATE(log_date) as bucket, COUNT(DISTINCT personnel_id) as total')
            ->whereDate('log_date', '>=', $startDate->toDateString())
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        $totalsByBucket = $this->mapTotalsByBucket($rows);
        $data = [];

        for ($day = $startDate; $day->lessThanOrEqualTo($today); $day = $day->addDay()) {
            $bucket = $day->toDateString();
            $data[] = [
                'bucket' => $bucket,
                'label' => $day->format('M d'),
                'value' => $totalsByBucket[$bucket] ?? 0,
            ];
        }

        return $data;
    }

    /**
     * @return array<int, array{bucket: string, label: string, value: int}>
     */
    private function getMonthlyTrendData(CarbonImmutable $today): array
    {
        $startMonth = $today->startOfMonth()->subMonths(11);

        $rows = AttendanceLog::query()
            ->selectRaw("DATE_FORMAT(log_date, '%Y-%m') as bucket, COUNT(DISTINCT personnel_id) as total")
            ->whereDate('log_date', '>=', $startMonth->toDateString())
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        $totalsByBucket = $this->mapTotalsByBucket($rows);
        $data = [];

        for ($month = $startMonth; $month->lessThanOrEqualTo($today); $month = $month->addMonth()) {
            $bucket = $month->format('Y-m');
            $data[] = [
                'bucket' => $bucket,
                'label' => $month->format('M Y'),
                'value' => $totalsByBucket[$bucket] ?? 0,
            ];
        }

        return $data;
    }

    /**
     * @param  Collection<int, object{bucket: string, total: int|string}>  $rows
     * @return array<string, int>
     */
    private function mapTotalsByBucket(Collection $rows): array
    {
        return $rows
            ->mapWithKeys(fn (object $row): array => [(string) $row->bucket => (int) $row->total])
            ->all();
    }

    /**
     * @return array<int, array{
     *     id: int,
     *     log_date: string,
     *     time_in: string|null,
     *     time_out: string|null,
     *     personnel: array{
     *         id: int|null,
     *         first_name: string|null,
     *         middle_name: string|null,
     *         last_name: string|null,
     *         office_name: string|null,
     *         position_name: string|null
     *     }
     * }>
     */
    private function getLatestPersonnelLogsToday(CarbonImmutable $today): array
    {
        $todayDate = $today->toDateString();

        $latestLogIdsByPersonnel = AttendanceLog::query()
            ->selectRaw('MAX(id) as id')
            ->whereDate('log_date', $todayDate)
            ->groupBy('personnel_id');

        return AttendanceLog::query()
            ->with(['personnel.office', 'personnel.position'])
            ->whereIn('id', $latestLogIdsByPersonnel)
            ->orderByDesc(DB::raw('COALESCE(time_out, time_in)'))
            ->orderByDesc('id')
            ->limit(12)
            ->get()
            ->map(function (AttendanceLog $attendanceLog): array {
                $personnel = $attendanceLog->personnel;

                return [
                    'id' => $attendanceLog->id,
                    'log_date' => $attendanceLog->log_date->toDateString(),
                    'time_in' => $attendanceLog->time_in?->toTimeString(),
                    'time_out' => $attendanceLog->time_out?->toTimeString(),
                    'personnel' => [
                        'id' => $personnel?->id,
                        'first_name' => $personnel?->first_name,
                        'middle_name' => $personnel?->middle_name,
                        'last_name' => $personnel?->last_name,
                        'office_name' => $personnel?->office?->name,
                        'position_name' => $personnel?->position?->name,
                    ],
                ];
            })
            ->values()
            ->all();
    }
}
