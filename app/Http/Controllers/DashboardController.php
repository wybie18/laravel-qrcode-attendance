<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(public DashboardService $dashboardService) {}

    public function index(Request $request): Response
    {
        $validated = $request->validate([
            'trend' => ['nullable', 'in:day,month'],
        ]);

        $dashboardData = $this->dashboardService->getDashboardData(
            $validated['trend'] ?? 'day',
        );

        return Inertia::render('Dashboard', $dashboardData);
    }
}
