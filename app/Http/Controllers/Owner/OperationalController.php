<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OperationalController extends Controller
{
    public function index()
    {
        // Get active service orders
        $activeOrders = \App\Models\ServiceOrder::with(['vehicle.customer', 'mechanic'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->get();
            
        // Get pending bookings
        $bookings = \App\Models\Booking::with(['customer', 'vehicle'])
            ->where('status', 'PENDING')
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->get();

        return view('owner.operasional', compact('activeOrders', 'bookings'));
    }
}
