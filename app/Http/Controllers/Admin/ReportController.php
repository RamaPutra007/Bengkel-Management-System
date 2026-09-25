<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\ServiceOrder;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Keuangan: Total Pendapatan dari Invoice yang sudah lunas
        $totalRevenue = Invoice::where('payment_status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('grand_total');

        // Operasional: Total Service Order
        $totalServiceOrders = ServiceOrder::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedServiceOrders = ServiceOrder::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Stok: Sparepart Keluar
        $totalSparepartOut = InventoryTransaction::where('type', 'out')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('quantity');

        // Breakdown Pembayaran
        $qrisIncome = Invoice::where('payment_status', 'paid')
            ->where('payment_method', 'qris')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('grand_total');

        $cashIncome = Invoice::where('payment_status', 'paid')
            ->where('payment_method', 'cash')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('grand_total');

        // Pendapatan Mingguan (Chart Data placeholder)
        $weeklyRevenue = Invoice::where('payment_status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(grand_total) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($request->get('export') == 'excel') {
            $invoices = Invoice::with(['serviceOrder.vehicle.customer'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();
                
            $title = strtoupper($startDate->format('F Y'));
            
            return response(view('exports.invoice_excel', compact('invoices', 'title')))
                ->header('Content-Type', 'application/vnd.ms-excel')
                ->header('Content-Disposition', 'attachment; filename="Laporan_Bengkel_'.$month.'_'.$year.'.xls"');
        }

        return view('admin.report.index', compact(
            'month', 'year', 'totalRevenue', 'totalServiceOrders', 'completedServiceOrders', 'totalSparepartOut', 'weeklyRevenue', 'qrisIncome', 'cashIncome'
        ));
    }
}
