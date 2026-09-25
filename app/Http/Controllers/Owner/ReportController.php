<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $stats = [
            'total_income' => \App\Models\Invoice::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('payment_status', 'paid')->sum('grand_total'),
            'qris_income' => \App\Models\Invoice::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('payment_status', 'paid')->where('payment_method', 'qris')->sum('grand_total'),
            'cash_income' => \App\Models\Invoice::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('payment_status', 'paid')->where('payment_method', 'cash')->sum('grand_total'),
            'total_services' => \App\Models\ServiceOrder::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('status', 'completed')->count(),
            'sparepart_usage' => \App\Models\InventoryTransaction::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('type', 'OUT')->sum('quantity'),
        ];
        if ($request->get('export') == 'excel') {
            $invoices = \App\Models\Invoice::with(['serviceOrder.vehicle.customer'])
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->orderBy('created_at', 'desc')
                ->get();
                
            $title = strtoupper(date('F Y', strtotime("$year-$month-01")));
            
            return response(view('exports.invoice_excel', compact('invoices', 'title')))
                ->header('Content-Type', 'application/vnd.ms-excel')
                ->header('Content-Disposition', 'attachment; filename="Laporan_Owner_'.$month.'_'.$year.'.xls"');
        }
        
        return view('owner.laporan', compact('stats', 'month', 'year'));
    }
}
