<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // TODO: Gather actual stats from Database
        $stats = [
            'transaksi_hari_ini' => 0,
            'pendapatan_hari_ini' => 0,
            'invoice_belum_dibayar' => 0,
            'invoice_sudah_dibayar' => 0,
        ];
        
        return view('kasir.dashboard', compact('stats'));
    }
}
