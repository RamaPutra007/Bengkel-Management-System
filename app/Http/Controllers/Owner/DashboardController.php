<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = \Carbon\Carbon::today();
        $yesterday = \Carbon\Carbon::yesterday();
        $startOfWeek = \Carbon\Carbon::now()->startOfWeek();
        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();

        $revenueToday = \App\Models\Invoice::where('payment_status', 'paid')->whereDate('paid_at', $today)->sum('grand_total');
        $revenueYesterday = \App\Models\Invoice::where('payment_status', 'paid')->whereDate('paid_at', $yesterday)->sum('grand_total');
        
        $revenueGrowth = 0;
        if ($revenueYesterday > 0) {
            $revenueGrowth = (($revenueToday - $revenueYesterday) / $revenueYesterday) * 100;
        } elseif ($revenueToday > 0) {
            $revenueGrowth = 100;
        }

        $stats = [
            'revenue_today' => $revenueToday,
            'revenue_growth' => round($revenueGrowth, 1),
            'revenue_week' => \App\Models\Invoice::where('payment_status', 'paid')->where('paid_at', '>=', $startOfWeek)->sum('grand_total'),
            'revenue_month' => \App\Models\Invoice::where('payment_status', 'paid')->where('paid_at', '>=', $startOfMonth)->sum('grand_total'),
            'total_service_orders' => \App\Models\ServiceOrder::where('created_at', '>=', $startOfMonth)->count(),
            'service_completed' => \App\Models\ServiceOrder::where('status', 'completed')->where('created_at', '>=', $startOfMonth)->count(),
            'service_in_progress' => \App\Models\ServiceOrder::where('status', 'in_progress')->count(),
            'total_customers' => \App\Models\Customer::count(),
            'total_vehicles' => \App\Models\Vehicle::count(),
            'total_spare_parts' => \App\Models\Sparepart::count(),
            'low_stock' => \App\Models\Sparepart::whereColumn('stock', '<=', 'reorder_level')->count(),
        ];
        $pending_bookings = \App\Models\Booking::with(['customer', 'vehicle'])->where('status', 'Menunggu')->get();
        
        return view('owner.dashboard', compact('stats', 'pending_bookings'));
    }
}
