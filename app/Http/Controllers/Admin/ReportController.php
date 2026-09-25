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
                
            $html = '<html><head><meta charset="utf-8"></head><body>';
            $html .= '<table border="1" style="border-collapse: collapse; font-family: sans-serif;">';
            $html .= '<tr><th colspan="7" style="background-color: #1e293b; color: #ffffff; font-size: 16px; font-weight: bold; text-align: center; height: 40px;">LAPORAN PENDAPATAN BENGKELPRO - ' . strtoupper($startDate->format('F Y')) . '</th></tr>';
            $html .= '<tr style="background-color: #FF6B00; color: #ffffff; text-align: center; font-weight: bold;">';
            $html .= '<th style="width: 120px;">Tanggal</th>';
            $html .= '<th style="width: 150px;">No. Invoice</th>';
            $html .= '<th style="width: 200px;">Pelanggan</th>';
            $html .= '<th style="width: 150px;">Plat Nomor</th>';
            $html .= '<th style="width: 120px;">Metode Bayar</th>';
            $html .= '<th style="width: 120px;">Status</th>';
            $html .= '<th style="width: 150px;">Total (Rp)</th>';
            $html .= '</tr>';
            
            $totalPendapatan = 0;
            foreach($invoices as $inv) {
                if ($inv->payment_status === 'paid') {
                    $totalPendapatan += $inv->grand_total;
                }
                
                $html .= '<tr style="text-align: center;">';
                $html .= '<td>' . $inv->created_at->format('Y-m-d') . '</td>';
                $html .= '<td>' . $inv->invoice_number . '</td>';
                $html .= '<td style="text-align: left;">' . ($inv->serviceOrder->vehicle->customer->name ?? '-') . '</td>';
                $html .= '<td>' . ($inv->serviceOrder->vehicle->license_plate ?? '-') . '</td>';
                $html .= '<td>' . strtoupper($inv->payment_method) . '</td>';
                
                $statusColor = $inv->payment_status === 'paid' ? '#16a34a' : '#dc2626';
                $html .= '<td style="color: '.$statusColor.'; font-weight: bold;">' . strtoupper($inv->payment_status) . '</td>';
                
                $html .= '<td style="text-align: right;">' . number_format($inv->grand_total, 0, ',', '.') . '</td>';
                $html .= '</tr>';
            }
            
            $html .= '<tr>';
            $html .= '<th colspan="6" style="text-align: right; font-weight: bold; font-size: 14px; background-color: #f1f5f9; height: 30px;">TOTAL PENDAPATAN (LUNAS)</th>';
            $html .= '<th style="text-align: right; font-weight: bold; font-size: 14px; background-color: #f1f5f9; color: #16a34a;">Rp ' . number_format($totalPendapatan, 0, ',', '.') . '</th>';
            $html .= '</tr>';
            
            $html .= '</table></body></html>';
            
            return response($html)
                ->header('Content-Type', 'application/vnd.ms-excel')
                ->header('Content-Disposition', 'attachment; filename="Laporan_Bengkel_'.$month.'_'.$year.'.xls"');
        }

        return view('admin.report.index', compact(
            'month', 'year', 'totalRevenue', 'totalServiceOrders', 'completedServiceOrders', 'totalSparepartOut', 'weeklyRevenue', 'qrisIncome', 'cashIncome'
        ));
    }
}
