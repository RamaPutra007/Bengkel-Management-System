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
                
            $html = '<html><head><meta charset="utf-8"></head><body>';
            $html .= '<table border="1" style="border-collapse: collapse; font-family: sans-serif;">';
            $html .= '<tr><th colspan="7" style="background-color: #1e293b; color: #ffffff; font-size: 16px; font-weight: bold; text-align: center; height: 40px;">LAPORAN PENDAPATAN BENGKELPRO - ' . strtoupper(date('F Y', strtotime("$year-$month-01"))) . '</th></tr>';
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
                ->header('Content-Disposition', 'attachment; filename="Laporan_Owner_'.$month.'_'.$year.'.xls"');
        }
        
        return view('owner.laporan', compact('stats', 'month', 'year'));
    }
}
