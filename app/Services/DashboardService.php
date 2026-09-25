<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\Sparepart;
use App\Models\Mechanic;

class DashboardService
{
    /**
     * Get overview statistics shared between Admin and Owner
     *
     * @return array
     */
    public function getOverviewStats()
    {
        return [
            'total_customers' => Customer::count(),
            'total_vehicles' => Vehicle::count(),
            'total_mechanics' => Mechanic::count(),
            'total_spare_parts' => Sparepart::count(),
            'stok_menipis' => Sparepart::whereColumn('stock', '<=', 'reorder_level')->count(),
            'low_stock' => Sparepart::whereColumn('stock', '<=', 'reorder_level')->count(),
        ];
    }
}
