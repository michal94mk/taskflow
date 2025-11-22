<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    public function index()
    {
        $user = auth()->user();

        return Inertia::render('Dashboard', [
            'kpi' => $this->dashboardService->getKpiData($user),
            'recentTasks' => $this->dashboardService->getRecentTasks($user),
            'projects' => $this->dashboardService->getRecentProjects($user),
            'charts' => $this->dashboardService->getChartData($user),
        ]);
    }
}
