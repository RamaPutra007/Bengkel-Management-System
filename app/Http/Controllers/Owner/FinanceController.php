<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index()
    {
        $recentInvoices = \App\Models\Invoice::with(['serviceOrder.vehicle.customer'])
            ->latest()
            ->take(10)
            ->get();
            
        $totalRevenue = \App\Models\Invoice::where('payment_status', 'paid')->sum('grand_total');
        
        return view('owner.keuangan', compact('recentInvoices', 'totalRevenue'));
    }
}
