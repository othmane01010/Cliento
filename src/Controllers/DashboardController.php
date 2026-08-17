<?php

namespace App\Controllers;

use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;

class DashboardController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        Subscription::updateExpiredStatuses();

      
        $totalClients          = Client::count();
        $totalPlans            = Plan::count();
        $activeSubscriptions   = Subscription::countActive();
        $totalRevenue          = Subscription::getTotalRevenue();
        $expiringSubscriptions = Subscription::getExpiringSoon(3);
        $recentSubscriptions   = array_slice(Subscription::getAllWithDetails(), 0, 5);

        $this->render('dashboard/index', [
            'totalClients'          => $totalClients,
            'totalPlans'            => $totalPlans,
            'activeSubscriptions'   => $activeSubscriptions,
            'expiringCount'         => count($expiringSubscriptions),
            'expiringSubscriptions' => $expiringSubscriptions,
            'totalRevenue'          => $totalRevenue,
            'recentSubscriptions'   => $recentSubscriptions,
            'adminName'             => $_SESSION['admin_name'] ?? 'Admin',
        ]);
    }
}