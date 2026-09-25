<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleQris(Request $request)
    {
        Log::info('QRIS Webhook Received:', $request->all());

        // Biasanya webhook memiliki parameter seperti amount, status, dan invoice identifier
        // Di sini saya berasumsi BuatQRIS atau Gateway lain mengirim: 
        // invoice_id (atau transaction_id/reference), status = paid, etc.
        
        // Simulasikan payload:
        // { "invoice_number": "INV-...", "status": "paid" } ATAU { "amount": 150000, "status": "PAID" }
        
        $invoiceNumber = $request->input('invoice_number');
        // Jika gateway tidak support meta-data invoice_number, kita gunakan total amount
        $amount = $request->input('amount') ?? $request->input('gross_amount');
        
        $status = strtolower($request->input('status') ?? 'paid');

        if ($status === 'paid' || $status === 'settlement' || $status === 'success') {
            
            // Cari invoice
            $invoice = null;
            if ($invoiceNumber) {
                $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();
            } elseif ($amount) {
                // Warning: ini rawan konflik jika ada beberapa invoice dengan nominal sama di hari yang sama
                // Tapi ini fallback sederhana
                $invoice = Invoice::where('payment_status', 'unpaid')
                                  ->where('grand_total', $amount)
                                  ->latest()
                                  ->first();
            }

            if ($invoice && $invoice->payment_status !== 'paid') {
                $invoice->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'qris',
                    'paid_at' => now(),
                ]);

                // Opsional: Kirim WA notifikasi ke owner/admin atau ke pelanggan bahwa lunas
                if ($request->wantsJson() || $request->isJson() || !$request->header('referer')) {
                    return response()->json(['message' => 'Invoice marked as paid'], 200);
                }
                
                return redirect()->back()->with('success', 'Simulasi Webhook Berhasil: Tagihan berhasil otomatis ditandai Lunas!');
            }

            if ($request->wantsJson() || $request->isJson() || !$request->header('referer')) {
                return response()->json(['message' => 'Invoice not found or already paid'], 404);
            }
            return redirect()->back()->with('error', 'Tagihan tidak ditemukan atau sudah lunas.');
        }

        if ($request->wantsJson() || $request->isJson() || !$request->header('referer')) {
            return response()->json(['message' => 'Ignored (status not paid)'], 200);
        }
        return redirect()->back()->with('error', 'Status bukan paid, webhook diabaikan.');
    }
}
