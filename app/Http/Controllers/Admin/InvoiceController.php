<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\ServiceOrder;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use Illuminate\Http\Request;
use App\Services\FonnteService;
use App\Services\KiPayService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    protected $kiPayService;
    protected $fonnteService;

    public function __construct(KiPayService $kiPayService, FonnteService $fonnteService)
    {
        $this->kiPayService = $kiPayService;
        $this->fonnteService = $fonnteService;
    }

    public function index(Request $request)
    {
        $query = Invoice::with(['serviceOrder.customer', 'serviceOrder.vehicle'])->latest();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('serviceOrder.customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $invoices = $query->paginate(10);
        return view('admin.invoice.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $serviceOrderId = $request->get('service_order_id');
        if (!$serviceOrderId) {
            return redirect()->route('admin.service-order.index')->with('error', 'Silakan pilih Service Order yang sudah selesai untuk dibuatkan Invoice.');
        }

        $serviceOrder = ServiceOrder::with(['customer', 'vehicle', 'items'])->findOrFail($serviceOrderId);
        
        if ($serviceOrder->status !== 'completed') {
            return redirect()->route('admin.service-order.show', $serviceOrder->id)->with('error', 'Service Order belum selesai.');
        }

        $existingInvoice = Invoice::where('service_order_id', $serviceOrder->id)->first();
        if ($existingInvoice) {
            return redirect()->route('admin.invoice.show', $existingInvoice->id)->with('success', 'Invoice untuk Service Order ini sudah ada.');
        }

        return view('admin.invoice.create', compact('serviceOrder'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $data = $request->validated();
        
        $serviceOrder = ServiceOrder::findOrFail($data['service_order_id']);

        $datePrefix = date('Ymd');
        $lastInvoice = Invoice::where('invoice_number', 'like', "INV-{$datePrefix}-%")->orderBy('id', 'desc')->first();
        $sequence = $lastInvoice ? (int)substr($lastInvoice->invoice_number, -4) + 1 : 1;
        $invoiceNumber = "INV-{$datePrefix}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        $subtotal = $serviceOrder->total_price;
        $taxPercent = $data['tax'] ?? 0;
        $discountPercent = $data['discount'] ?? 0;
        
        $taxAmount = $subtotal * ($taxPercent / 100);
        $discountAmount = $subtotal * ($discountPercent / 100);
        $grandTotal = $subtotal + $taxAmount - $discountAmount;

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'service_order_id' => $serviceOrder->id,
            'subtotal' => $subtotal,
            'tax' => $taxAmount,
            'discount' => $discountAmount,
            'grand_total' => $grandTotal,
            'payment_status' => 'unpaid',
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('admin.invoice.show', $invoice->id)->with('success', 'Invoice berhasil diterbitkan.');
    }

    public function show(string $id)
    {
        $invoice = Invoice::with(['serviceOrder.customer', 'serviceOrder.vehicle', 'serviceOrder.items.service', 'serviceOrder.items.sparepart'])->findOrFail($id);
        $isKiPayConfigured = $this->kiPayService->isConfigured();
        $kiPayMode = $this->kiPayService->getMode();

        return view('admin.invoice.show', compact('invoice', 'isKiPayConfigured', 'kiPayMode'));
    }

    public function print(string $id)
    {
        $invoice = Invoice::with(['serviceOrder.customer', 'serviceOrder.vehicle', 'serviceOrder.items.service', 'serviceOrder.items.sparepart'])->findOrFail($id);
        return view('admin.invoice.print', compact('invoice'));
    }

    public function edit(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $isKiPayConfigured = $this->kiPayService->isConfigured();
        $kiPayMode = $this->kiPayService->getMode();

        $dynamicQris = null;
        if (!empty($invoice->kipay_qr_payload)) {
            $dynamicQris = $invoice->kipay_qr_payload;
        } else {
            // Fallback manual static QRIS
            $staticQris = '00020101021126570011ID.DANA.WWW011893600915303325959802090332595980303UMI51440014ID.CO.QRIS.WWW0215ID10265494495550303UMI5204573253033605802ID5906KOSTKU6013Kota Makassar61059021163048265';
            $dynamicQris = \App\Services\QrisService::generateDynamicQris($staticQris, (int) $invoice->grand_total);
        }
        
        return view('admin.invoice.edit', compact('invoice', 'dynamicQris', 'isKiPayConfigured', 'kiPayMode'));
    }

    public function update(UpdateInvoiceRequest $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $data = $request->validated();
        $action = $data['payment_action'];

        if ($action === 'cash') {
            $invoice->update([
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'paid_at' => $invoice->payment_status === 'unpaid' ? now() : $invoice->paid_at,
                'notes' => $data['notes'] ?? $invoice->notes,
            ]);
            return redirect()->route('admin.invoice.show', $invoice->id)->with('success', 'Invoice berhasil ditandai Lunas (Cash).');
        } elseif ($action === 'qris') {
            $invoice->update([
                'payment_status' => 'unpaid',
                'payment_method' => 'qris',
                'notes' => $data['notes'] ?? $invoice->notes,
            ]);
            
            // Buat transaksi di KiPay & Kirim QRIS via WhatsApp
            return $this->sendQris($id);
        } else {
            // unpaid
            $invoice->update([
                'payment_status' => 'unpaid',
                'payment_method' => null,
                'notes' => $data['notes'] ?? $invoice->notes,
            ]);
            return redirect()->route('admin.invoice.show', $invoice->id)->with('success', 'Tagihan disimpan sebagai Belum Lunas.');
        }
    }

    /**
     * Buat transaksi KiPay QRIS dan kirimkan ke WhatsApp pelanggan.
     */
    public function sendQris(string $id)
    {
        $invoice = Invoice::with('serviceOrder.customer')->findOrFail($id);
        
        $customer = $invoice->serviceOrder->customer ?? null;
        if (!$customer || !$customer->phone) {
            return redirect()->route('admin.invoice.show', $invoice->id)->with('error', 'Pelanggan tidak memiliki nomor telepon yang valid.');
        }

        // 1. Integrasi KiPay jika terkonfigurasi
        $isKiPayUsed = false;
        $kipayNotice = '';

        if ($this->kiPayService->isConfigured()) {
            // Cek apakah invoice sudah punya transaksi KiPay yang masih berlaku
            $needNewTransaction = empty($invoice->kipay_trx_id) || 
                                  ($invoice->kipay_expired_at && $invoice->kipay_expired_at->isPast());

            if ($needNewTransaction) {
                $trxResult = $this->kiPayService->createTransaction((int) $invoice->grand_total, $invoice->invoice_number);

                if ($trxResult['success'] && isset($trxResult['data'])) {
                    $d = $trxResult['data'];
                    $invoice->update([
                        'kipay_trx_id' => $d['trx_id'] ?? null,
                        'kipay_qr_url' => $d['qr_url'] ?? null,
                        'kipay_qr_payload' => $d['qr_payload'] ?? null,
                        'kipay_amount' => $d['amount'] ?? $invoice->grand_total,
                        'kipay_fee' => $d['fee_amount'] ?? 0,
                        'kipay_expired_at' => isset($d['expires_at']) ? Carbon::parse($d['expires_at']) : now()->addMinutes(15),
                        'payment_method' => 'qris',
                        'payment_status' => 'unpaid',
                    ]);
                    $isKiPayUsed = true;
                    $kipayNotice = " (KiPay Trx ID: {$d['trx_id']})";
                } else {
                    Log::warning('KiPay create transaction failed, fallback to static QRIS:', $trxResult);
                    $errMsg = $trxResult['message'] ?? 'gagal merespons';
                    $kipayNotice = " (Peringatan: KiPay {$errMsg}, menggunakan QRIS statis)";
                }
            } else {
                $isKiPayUsed = true;
                $kipayNotice = " (KiPay Trx ID: {$invoice->kipay_trx_id})";
            }
        }

        // 2. Siapkan Gambar QRIS
        $totalPay = $invoice->kipay_amount ?: (int) $invoice->grand_total;
        
        if ($isKiPayUsed && !empty($invoice->kipay_qr_payload)) {
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($invoice->kipay_qr_payload) . "&ext=.png";
        } else {
            // Fallback
            $staticQris = '00020101021126570011ID.DANA.WWW011893600915303325959802090332595980303UMI51440014ID.CO.QRIS.WWW0215ID10265494495550303UMI5204573253033605802ID5906KOSTKU6013Kota Makassar61059021163048265';
            $dynamicQris = \App\Services\QrisService::generateDynamicQris($staticQris, (int) $invoice->grand_total);
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($dynamicQris) . "&ext=.png";
        }

        // 3. Kirim via WhatsApp (Fonnte)
        try {
            $checkoutPageUrl = route('checkout.show', $invoice->invoice_number);

            $response = $this->fonnteService->sendTemplateMessage($customer->phone, 'qris_tagihan', [
                'nama' => $customer->name,
                'nomor_invoice' => $invoice->invoice_number,
                'nomor_order' => $invoice->serviceOrder->order_number,
                'kendaraan' => ($invoice->serviceOrder->vehicle->brand ?? '') . ' ' . ($invoice->serviceOrder->vehicle->model ?? ''),
                'total' => number_format($totalPay, 0, ',', '.'),
                'qris_url' => $checkoutPageUrl,
                'nama_bengkel' => config('app.name'),
            ], $qrUrl);

            if (isset($response['status']) && $response['status']) {
                return redirect()->route('admin.invoice.show', $invoice->id)->with('success', 'Metode QRIS dipilih' . $kipayNotice . '. Pesan tagihan dan kode QRIS berhasil dikirim ke WhatsApp pelanggan.');
            } else {
                return redirect()->route('admin.invoice.show', $invoice->id)->with('warning', 'Metode QRIS dibuat' . $kipayNotice . ', namun pesan WhatsApp gagal dikirim: ' . ($response['reason'] ?? $response['detail'] ?? 'Koneksi Fonnte bermasalah.'));
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.invoice.show', $invoice->id)->with('warning', 'QRIS berhasil dibuat' . $kipayNotice . ', namun pengiriman WhatsApp gagal: ' . $e->getMessage());
        }
    }

    /**
     * Cek status pembayaran secara real-time ke API KiPay.
     */
    public function checkKiPayStatus(string $id)
    {
        $invoice = Invoice::findOrFail($id);

        if (!$invoice->kipay_trx_id) {
            return redirect()->route('admin.invoice.show', $invoice->id)->with('error', 'Invoice ini belum memiliki transaksi KiPay terdaftar.');
        }

        $result = $this->kiPayService->getTransaction($invoice->kipay_trx_id);

        if (!$result['success']) {
            return redirect()->route('admin.invoice.show', $invoice->id)->with('error', 'Gagal memeriksa status ke KiPay: ' . ($result['message'] ?? 'Error tidak diketahui'));
        }

        $data = $result['data'];
        $status = strtolower($data['status'] ?? '');

        if ($status === 'paid' || $status === 'settlement' || $status === 'success') {
            $invoice->update([
                'payment_status' => 'paid',
                'payment_method' => 'qris',
                'paid_at' => isset($data['matched_at']) ? Carbon::parse($data['matched_at']) : now(),
                'kipay_provider' => $data['provider'] ?? 'qris',
                'kipay_fee' => $data['fee_amount'] ?? $invoice->kipay_fee,
            ]);

            return redirect()->route('admin.invoice.show', $invoice->id)->with('success', "Status Pembayaran KiPay: LUNAS via " . strtoupper($data['provider'] ?? 'QRIS') . "! Tagihan telah diperbarui.");
        }

        if ($status === 'expired') {
            return redirect()->route('admin.invoice.show', $invoice->id)->with('warning', "Status KiPay: KADALUARSA (Expired). Pelanggan belum membayar sebelum batas waktu. Silakan kirim ulang QRIS.");
        }

        return redirect()->route('admin.invoice.show', $invoice->id)->with('info', "Status KiPay: PENDING (Menunggu Pembayaran). Pelanggan belum melakukan scan/pembayaran.");
    }

    /**
     * Jalankan simulasi pembayaran KiPay (Khusus Sandbox).
     */
    public function simulateKiPayPayment(string $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Jika belum ada trx_id KiPay, buat transaksinya terlebih dahulu
        if (!$invoice->kipay_trx_id) {
            $trxResult = $this->kiPayService->createTransaction((int) $invoice->grand_total, $invoice->invoice_number);
            if ($trxResult['success'] && isset($trxResult['data'])) {
                $d = $trxResult['data'];
                $invoice->update([
                    'kipay_trx_id' => $d['trx_id'],
                    'kipay_qr_url' => $d['qr_url'] ?? null,
                    'kipay_qr_payload' => $d['qr_payload'] ?? null,
                    'kipay_amount' => $d['amount'] ?? $invoice->grand_total,
                    'kipay_fee' => $d['fee_amount'] ?? 0,
                    'kipay_expired_at' => isset($d['expires_at']) ? Carbon::parse($d['expires_at']) : now()->addMinutes(15),
                    'payment_method' => 'qris',
                ]);
            } else {
                return redirect()->route('admin.invoice.show', $invoice->id)->with('error', 'Gagal membuat transaksi KiPay untuk simulasi: ' . ($trxResult['message'] ?? 'Error'));
            }
        }

        $simResult = $this->kiPayService->simulatePayment($invoice->kipay_trx_id, 'shopeepay');

        if (!$simResult['success']) {
            return redirect()->route('admin.invoice.show', $invoice->id)->with('error', 'Gagal menjalankan simulasi KiPay: ' . ($simResult['message'] ?? 'Error'));
        }

        $data = $simResult['data'] ?? [];
        $invoice->update([
            'payment_status' => 'paid',
            'payment_method' => 'qris',
            'paid_at' => isset($data['matched_at']) ? Carbon::parse($data['matched_at']) : now(),
            'kipay_provider' => $data['provider'] ?? 'shopeepay',
            'kipay_fee' => $data['fee_amount'] ?? $invoice->kipay_fee,
        ]);

        return redirect()->route('admin.invoice.show', $invoice->id)->with('success', 'Simulasi Pembayaran KiPay Sandbox Berhasil! Tagihan otomatis ditandai LUNAS via ShopeePay.');
    }

    /**
     * Proxy gambar QR KiPay.
     */
    public function kipayQr(string $id)
    {
        $invoice = Invoice::findOrFail($id);

        if (!$invoice->kipay_trx_id) {
            abort(404, 'Transaksi KiPay tidak ditemukan.');
        }

        $result = $this->kiPayService->getQrImage($invoice->kipay_trx_id);

        if ($result['success']) {
            return response($result['body'])->header('Content-Type', $result['content_type']);
        }

        // Fallback jika direct image fetch gagal: render via qrserver dengan qr_payload
        if (!empty($invoice->kipay_qr_payload)) {
            return redirect("https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($invoice->kipay_qr_payload));
        }

        abort(404, 'Gambar QR tidak tersedia.');
    }

    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
        return redirect()->route('admin.invoice.index')->with('success', 'Invoice berhasil dihapus.');
    }
}
