<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        // Get spareparts, specially low stock
        $lowStockSpareparts = \App\Models\Sparepart::whereColumn('stock', '<=', 'reorder_level')->get();
        $recentTransactions = \App\Models\InventoryTransaction::with(['sparepart', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('owner.inventaris', compact('lowStockSpareparts', 'recentTransactions'));
    }
}
