<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\KiPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    protected $kiPayService;

    public function __construct(KiPayService $kiPayService)
    {
        $this->kiPayService = $kiPayService;
    }

    /**
     * Endpoint route internal yang melindungi kredensial:
     * Mengambil gambar QR (500x500 image/png) dari KiPay menggunakan API Key di backend,
     * lalu meneruskan response image/png ke browser tanpa mengekspos API key.
     *
     * URL: /api/checkout/qr/{trx_id}
     */
    public function getQrImage(string $trxId)
    {
        $result = $this->kiPayService->getQrImage($trxId);

        if ($result['success']) {
            return response($result['body'])
                ->header('Content-Type', $result['content_type'])
                ->header('Cache-Control', 'public, max-age=600');
        }

        // Fallback jika API sedang gangguan: cari payload dari database
        $invoice = Invoice::where('kipay_trx_id', $trxId)->first();
        if ($invoice && !empty($invoice->kipay_qr_payload)) {
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($invoice->kipay_qr_payload);
            try {
                $fallbackRes = Http::timeout(5)->get($qrUrl);
                if ($fallbackRes->successful()) {
                    return response($fallbackRes->body())
                        ->header('Content-Type', 'image/png')
                        ->header('Cache-Control', 'public, max-age=600');
                }
            } catch (\Exception $e) {
                // fall through
            }
        }

        abort(404, 'Gambar QRIS tidak ditemukan.');
    }

    /**
     * Halaman Publik Checkout Pelanggan:
     * Menampilkan rincian tagihan, QRIS dinamis KiPay, batas waktu pembayaran,
     * dan otomatis refresh status pembayaran ketika pelanggan selesai scan.
     *
     * URL: /checkout/{invoice}
     */
    public function show(string $invoiceNumber)
    {
        $invoice = Invoice::with(['serviceOrder.customer', 'serviceOrder.vehicle', 'serviceOrder.items'])
            ->where('invoice_number', $invoiceNumber)
            ->orWhere('id', is_numeric($invoiceNumber) ? (int)$invoiceNumber : 0)
            ->firstOrFail();

        return view('checkout', compact('invoice'));
    }

    /**
     * Endpoint JSON untuk Polling Status Pembayaran di Halaman Checkout Pelanggan.
     *
     * URL: /checkout/{invoice}/status
     */
    public function checkStatus(string $invoiceNumber)
    {
        $invoice = Invoice::where('invoice_number', $invoiceNumber)
            ->orWhere('id', is_numeric($invoiceNumber) ? (int)$invoiceNumber : 0)
            ->firstOrFail();

        return response()->json([
            'invoice_number' => $invoice->invoice_number,
            'payment_status' => $invoice->payment_status,
            'payment_method' => $invoice->payment_method,
            'provider' => $invoice->kipay_provider,
            'paid_at' => $invoice->paid_at ? $invoice->paid_at->format('d/m/Y H:i') : null,
            'is_paid' => $invoice->payment_status === 'paid',
        ]);
    }
}

