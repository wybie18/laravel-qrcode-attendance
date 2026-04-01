<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceLogRequest;
use App\Http\Requests\UpdateAttendanceLogRequest;
use App\Http\Resources\AttendanceLogResource;
use App\Http\Resources\PersonnelResource;
use App\Models\AttendanceLog;
use App\Models\Personnel;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceLogController extends Controller
{
    public function __construct(public AttendanceService $attendanceService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $attendanceLogs = AttendanceLog::query()
            ->with('personnel')
            ->orderByDesc('log_date')
            ->orderByDesc('id')
            ->paginate(15);

        $personnels = Personnel::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return Inertia::render('logs/Index', [
            'attendanceLogs' => AttendanceLogResource::collection($attendanceLogs),
            'personnels' => PersonnelResource::collection($personnels),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendanceLogRequest $request): RedirectResponse
    {
        $this->attendanceService->create($request->validated());

        return back()->with('success', 'Attendance log created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceLogRequest $request, AttendanceLog $attendanceLog): RedirectResponse
    {
        $this->attendanceService->update($attendanceLog, $request->validated());

        return back()->with('success', 'Attendance log updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceLog $attendanceLog): RedirectResponse
    {
        $this->attendanceService->delete($attendanceLog);

        return back()->with('success', 'Attendance log deleted successfully.');
    }
}
