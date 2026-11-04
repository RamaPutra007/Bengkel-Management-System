<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\Sparepart;
use App\Models\Mechanic;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    /**
     * Get overview statistics shared between Admin and Owner
     *
     * @return array
     */
    public function getOverviewStats()
    {
        return Cache::remember('dashboard.overview_stats', 30, function () {
            $lowStockCount = Sparepart::whereColumn('stock', '<=', 'reorder_level')->count();

            return [
                'total_customers' => Customer::count(),
                'total_vehicles' => Vehicle::count(),
                'total_mechanics' => Mechanic::count(),
                'total_spare_parts' => Sparepart::count(),
                'stok_menipis' => $lowStockCount,
                'low_stock' => $lowStockCount,
            ];
        });
    }
}
