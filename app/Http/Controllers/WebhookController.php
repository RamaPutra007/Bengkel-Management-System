<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Services\KiPayService;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $kiPayService;
    protected $fonnteService;

    public function __construct(KiPayService $kiPayService, FonnteService $fonnteService)
    {
        $this->kiPayService = $kiPayService;
        $this->fonnteService = $fonnteService;
    }

    /**
     * Webhook Handler khusus untuk KiPay (https://kipay.id/)
     */
    public function handleKiPay(Request $request)
    {
        Log::info('KiPay Webhook Received:', [
            'headers' => [
                'event' => $request->header('X-Webhook-Event'),
                'delivery' => $request->header('X-Webhook-Delivery'),
                'timestamp' => $request->header('X-Webhook-Timestamp'),
                'signature' => $request->header('X-Webhook-Signature'),
            ],
            'payload' => $request->all(),
        ]);

        $rawBody = $request->getContent();
        $signature = $request->header('X-Webhook-Signature') ?? $request->header('X-Signature');
        $timestamp = $request->header('X-Webhook-Timestamp');
        $deliveryId = $request->header('X-Webhook-Delivery');
        $event = $request->header('X-Webhook-Event') ?? $request->input('event');

        // 1. Verifikasi Signature HMAC jika secret sudah diatur
        $isValid = $this->kiPayService->verifyWebhookSignature($rawBody, $signature, $timestamp, $deliveryId);
        if (!$isValid) {
            Log::warning('KiPay Webhook: Invalid signature', ['ip' => $request->ip()]);
            return response()->json(['message' => 'Invalid webhook signature'], 401);
        }

        // 2. Handle Test Event
        if ($event === 'webhook.test') {
            Log::info('KiPay Webhook: Test connection received successfully.');
            return response()->json([
                'success' => true,
                'message' => 'KiPay webhook test connection successful',
            ], 200);
        }

        // 3. Handle Transaksi
        $transaction = $request->input('transaction') ?? $request->all();
        $trxId = $transaction['trx_id'] ?? null;
        $status = strtolower($transaction['status'] ?? '');
        $note = $transaction['note'] ?? $request->input('invoice_number');
        $provider = $transaction['provider'] ?? 'qris';
        $feeAmount = $transaction['fee_amount'] ?? null;
        $netAmount = $transaction['net_amount'] ?? null;
        $matchedAt = $transaction['matched_at'] ?? null;

        // Cari invoice berdasarkan trx_id KiPay atau note (nomor invoice)
        $invoice = null;
        if ($trxId) {
            $invoice = Invoice::where('kipay_trx_id', $trxId)->first();
        }
        if (!$invoice && $note) {
            $invoice = Invoice::where('invoice_number', $note)->first();
        }

        if (!$invoice) {
            Log::warning('KiPay Webhook: Invoice tidak ditemukan.', ['trx_id' => $trxId, 'note' => $note]);
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        // Simpan / update trx_id jika belum tersimpan
        if ($trxId && empty($invoice->kipay_trx_id)) {
            $invoice->kipay_trx_id = $trxId;
        }

        if ($event === 'transaction.paid' || $status === 'paid' || $status === 'settlement' || $status === 'success') {
            // Idempotent: jika sudah lunas, langsung return 200 OK
            if ($invoice->payment_status === 'paid') {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice already paid',
                    'invoice_number' => $invoice->invoice_number,
                ], 200);
            }

            // Langkah 4 KiPay: Verifikasi status server-to-server secara langsung sebelum memenuhi order
            if ($trxId && $this->kiPayService->isConfigured()) {
                $verifyRes = $this->kiPayService->getTransaction($trxId);
                if ($verifyRes['success'] && isset($verifyRes['data'])) {
                    $verifiedData = $verifyRes['data'];
                    $vStatus = strtolower($verifiedData['status'] ?? '');
                    if ($vStatus !== 'paid' && $vStatus !== 'settlement' && $vStatus !== 'success') {
                        Log::warning("KiPay Webhook: Verifikasi status server-to-server gagal. Status di KiPay: {$vStatus}");
                        return response()->json([
                            'message' => 'Transaction status not verified as paid on KiPay server',
                            'kipay_status' => $vStatus
                        ], 400);
                    }
                    // Gunakan data terverifikasi resmi dari API KiPay
                    $provider = $verifiedData['provider'] ?? $provider;
                    $matchedAt = $verifiedData['matched_at'] ?? $matchedAt;
                    $feeAmount = $verifiedData['fee_amount'] ?? $feeAmount;
                    $netAmount = $verifiedData['net_amount'] ?? $netAmount;
                }
            }

            // Langkah 03 KiPay: Bandingkan nominal final dengan order
            $receivedAmount = $transaction['requested_amount'] ?? $transaction['amount'] ?? 0;
            if ($receivedAmount > 0 && $receivedAmount < $invoice->grand_total) {
                Log::warning("KiPay Webhook: Nominal pembayaran ({$receivedAmount}) kurang dari tagihan ({$invoice->grand_total}).");
                return response()->json(['message' => 'Paid amount does not match invoice grand total'], 400);
            }

            // Update status lunas (Langkah 04: Penuhi order satu kali)
            $invoice->update([
                'payment_status' => 'paid',
                'payment_method' => 'qris',
                'paid_at' => $matchedAt ? Carbon::parse($matchedAt) : now(),
                'kipay_provider' => $provider,
                'kipay_fee' => $feeAmount ?? $invoice->kipay_fee,
                'kipay_net_amount' => $netAmount,
                'kipay_delivery_id' => $deliveryId,
            ]);

            Log::info("KiPay Webhook: Invoice #{$invoice->invoice_number} berhasil ditandai LUNAS via {$provider} (Net: Rp " . number_format($netAmount ?: $invoice->grand_total, 0, ',', '.') . ").");

            // Kirim notifikasi WhatsApp ke pelanggan bahwa pembayaran diterima
            $this->sendPaymentConfirmationWhatsApp($invoice);

            if ($request->wantsJson() || $request->isJson() || !$request->header('referer')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice successfully marked as paid',
                    'invoice_number' => $invoice->invoice_number,
                ], 200);
            }

            return redirect()->back()->with('success', "Pembayaran QRIS KiPay Berhasil! Invoice #{$invoice->invoice_number} lunas.");
        }

        if ($event === 'transaction.expired' || $status === 'expired') {
            Log::info("KiPay Webhook: Transaksi {$trxId} kadaluarsa untuk Invoice #{$invoice->invoice_number}.");
            if ($invoice->payment_status !== 'paid') {
                $invoice->update([
                    'kipay_expired_at' => now(),
                    'kipay_delivery_id' => $deliveryId,
                ]);
            }
            return response()->json(['message' => 'Transaction expired noted'], 200);
        }

        return response()->json(['message' => 'Webhook received and acknowledged'], 200);
    }

    /**
     * Backward-compatible handler untuk route /api/webhook/qris
     */
    public function handleQris(Request $request)
    {
        return $this->handleKiPay($request);
    }

    /**
     * Kirim pesan konfirmasi pembayaran lunas ke nomor WA pelanggan jika Fonnte aktif.
     */
    protected function sendPaymentConfirmationWhatsApp(Invoice $invoice): void
    {
        try {
            $customer = $invoice->serviceOrder->customer ?? null;
            if (!$customer || !$customer->phone) {
                return;
            }

            if (!$this->fonnteService->isEnabled()) {
                return;
            }

            $message = "Halo *{$customer->name}*,\n\n"
                     . "Terima kasih! Pembayaran QRIS Anda untuk Invoice *#{$invoice->invoice_number}* sebesar *Rp " . number_format($invoice->grand_total, 0, ',', '.') . "* telah *BERHASIL DITERIMA* lunas.\n\n"
                     . "Metode: *QRIS (" . strtoupper($invoice->kipay_provider ?? 'KiPay') . ")*\n"
                     . "Waktu: " . ($invoice->paid_at ? $invoice->paid_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i')) . " WIB\n\n"
                     . "Kendaraan Anda siap diproses lebih lanjut oleh tim kami di *" . config('app.name', 'BengkelPro') . "*.\n\n"
                     . "Salam hangat,\n*" . config('app.name', 'BengkelPro') . "*";

            $this->fonnteService->sendMessage($customer->phone, $message);
        } catch (\Exception $e) {
            Log::warning('Gagal mengirim WhatsApp konfirmasi pembayaran lunas: ' . $e->getMessage());
        }
    }
}
