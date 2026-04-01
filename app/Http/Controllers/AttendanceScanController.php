<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceScanRequest;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;

class AttendanceScanController extends Controller
{
    public function __construct(public AttendanceService $attendanceService) {}

    public function store(StoreAttendanceScanRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $attendanceLog = $this->attendanceService->logByQrCode(
            $validated['qr_code'],
            $validated['action']
        );

        return response()->json([
            'message' => $validated['action'] === 'time_in'
                ? 'Time in logged successfully.'
                : 'Time out logged successfully.',
            'data' => $attendanceLog,
        ]);
    }
}
