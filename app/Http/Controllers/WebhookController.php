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

        // 1. Verifikasi Signature (HMAC) dari Payment Gateway
        $secret = env('WEBHOOK_SECRET');
        
        // Untuk simulasi dari UI web (tidak ada header signature), kita izinkan jika secret belum diset di .env
        // Namun di production, blok ini wajib aktif.
        if ($secret) {
            $signature = $request->header('X-Signature');
            $expectedSignature = hash_hmac('sha256', $request->getContent(), $secret);

            if (!hash_equals((string) $expectedSignature, (string) $signature)) {
                Log::warning('QRIS Webhook: Invalid Signature.', ['ip' => $request->ip()]);
                return response()->json(['message' => 'Invalid signature'], 403);
            }
        } else {
            Log::warning('QRIS Webhook: WEBHOOK_SECRET tidak diset di .env. Endpoint ini tidak aman!');
        }

        $invoiceNumber = $request->input('invoice_number');
        $status = strtolower($request->input('status') ?? 'paid');

        if (!$invoiceNumber) {
            if ($request->wantsJson() || $request->isJson() || !$request->header('referer')) {
                return response()->json(['message' => 'invoice_number is required'], 400);
            }
            return redirect()->back()->with('error', 'Parameter invoice_number diperlukan.');
        }

        if ($status === 'paid' || $status === 'settlement' || $status === 'success') {
            
            // 2. Cari invoice HANYA berdasarkan invoice_number yang spesifik, jangan gunakan nominal amount (rawan konflik)
            $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();

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
