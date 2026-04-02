<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceLogRequest;
use App\Http\Requests\UpdateAttendanceLogRequest;
use App\Http\Resources\AttendanceLogResource;
use App\Http\Resources\OfficeResource;
use App\Http\Resources\PersonnelResource;
use App\Models\AttendanceLog;
use App\Models\Office;
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
        $query = AttendanceLog::query()->with('personnel.office');

        // Filter by search (personnel name)
        if (request()->filled('search')) {
            $search = request('search');
            $query->whereHas('personnel', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Filter by office_id
        if (request()->filled('office_id')) {
            $query->whereHas('personnel', function ($q) {
                $q->where('office_id', request('office_id'));
            });
        }

        // Filter by date range
        if (request()->filled('date_from')) {
            $query->whereDate('log_date', '>=', request('date_from'));
        }

        if (request()->filled('date_to')) {
            $query->whereDate('log_date', '<=', request('date_to'));
        }

        $attendanceLogs = $query
            ->orderByDesc('log_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $personnels = Personnel::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $offices = Office::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('logs/Index', [
            'attendanceLogs' => AttendanceLogResource::collection($attendanceLogs),
            'personnels' => PersonnelResource::collection($personnels),
            'offices' => OfficeResource::collection($offices),
            'filters' => [
                'search' => request('search'),
                'office_id' => request('office_id'),
                'date_from' => request('date_from'),
                'date_to' => request('date_to'),
            ],
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
