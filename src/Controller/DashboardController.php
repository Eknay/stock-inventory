<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Repository\DashboardRepository;
use App\Repository\StockMovementRepository;
use App\View;

final class DashboardController
{
    public function __construct(
        private DashboardRepository $dashboard,
        private StockMovementRepository $movements,
        private View $view
    ) {
    }

    public function index(Request $request, array $params = []): Response
    {
        return $this->view->render('dashboard', [
            'page' => 'dashboard',
            'summary' => $this->dashboard->summary(),
            'recentMovements' => $this->movements->recent(8),
            'categoryBreakdown' => $this->dashboard->categoryBreakdown(),
        ], 'Dashboard');
    }

    public function summary(Request $request, array $params = []): Response
    {
        return Response::json([
            'summary' => $this->dashboard->summary(),
            'recent_movements' => $this->movements->recent(8),
            'category_breakdown' => $this->dashboard->categoryBreakdown(),
        ]);
    }
}
