<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\ServiceOrder;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
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
        // Biasanya dibuat dari Service Order yang sudah completed
        $serviceOrderId = $request->get('service_order_id');
        if (!$serviceOrderId) {
            return redirect()->route('admin.service-order.index')->with('error', 'Silakan pilih Service Order yang sudah selesai untuk dibuatkan Invoice.');
        }

        $serviceOrder = ServiceOrder::with(['customer', 'vehicle', 'items'])->findOrFail($serviceOrderId);
        
        if ($serviceOrder->status !== 'completed') {
            return redirect()->route('admin.service-order.show', $serviceOrder->id)->with('error', 'Service Order belum selesai.');
        }

        // Cek apakah sudah ada invoice
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
        return view('admin.invoice.show', compact('invoice'));
    }

    public function print(string $id)
    {
        $invoice = Invoice::with(['serviceOrder.customer', 'serviceOrder.vehicle', 'serviceOrder.items.service', 'serviceOrder.items.sparepart'])->findOrFail($id);
        return view('admin.invoice.print', compact('invoice'));
    }

    public function edit(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        
        // QRIS Statis dari toko (NMDI KOSTKU)
        $staticQris = '00020101021126570011ID.DANA.WWW011893600915303325959802090332595980303UMI51440014ID.CO.QRIS.WWW0215ID10265494495550303UMI5204573253033605802ID5906KOSTKU6013Kota Makassar61059021163048265';
        
        // Generate Dynamic QRIS menggunakan QrisService
        $dynamicQris = \App\Services\QrisService::generateDynamicQris($staticQris, (int) $invoice->grand_total);
        
        return view('admin.invoice.edit', compact('invoice', 'dynamicQris'));
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
            
            // Generate & send QRIS
            return $this->sendQris($id, app(\App\Services\FonnteService::class));
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

    public function sendQris(string $id, \App\Services\FonnteService $fonnte)
    {
        $invoice = Invoice::with('serviceOrder.customer')->findOrFail($id);
        
        $customer = $invoice->serviceOrder->customer ?? null;
        if (!$customer || !$customer->phone) {
            return redirect()->route('admin.invoice.show', $invoice->id)->with('error', 'Pelanggan tidak memiliki nomor telepon yang valid.');
        }

        // Generate URL QRIS Dinamis
        $staticQris = '00020101021126570011ID.DANA.WWW011893600915303325959802090332595980303UMI51440014ID.CO.QRIS.WWW0215ID10265494495550303UMI5204573253033605802ID5906KOSTKU6013Kota Makassar61059021163048265';
        $dynamicQris = \App\Services\QrisService::generateDynamicQris($staticQris, (int) $invoice->grand_total);
        
        // Fonnte API membutuhkan akhiran ekstensi file (contoh: .png) pada string URL agar dikenali sebagai gambar
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($dynamicQris) . "&ext=.png";

        // Kirim via Fonnte menggunakan Template
        try {
            $response = $fonnte->sendTemplateMessage($customer->phone, 'qris_tagihan', [
                'nama' => $customer->name,
                'nomor_invoice' => $invoice->invoice_number,
                'nomor_order' => $invoice->serviceOrder->order_number,
                'kendaraan' => $invoice->serviceOrder->vehicle->brand . ' ' . $invoice->serviceOrder->vehicle->model,
                'total' => number_format($invoice->grand_total, 0, ',', '.'),
                'qris_url' => str_replace('&ext=.png', '', $qrUrl),
                'nama_bengkel' => config('app.name')
            ], $qrUrl);

            // Karena kita membungkusnya dalam sendTemplateMessage, kita harus cek return json-nya
            if (isset($response['status']) && $response['status']) {
                return redirect()->route('admin.invoice.show', $invoice->id)->with('success', 'Metode QRIS dipilih. Pesan tagihan dan gambar QRIS berhasil dikirim ke WhatsApp pelanggan.');
            } else {
                return redirect()->route('admin.invoice.show', $invoice->id)->with('error', 'Metode QRIS dipilih, namun gagal mengirim pesan WhatsApp. Fonnte Response: ' . ($response['reason'] ?? $response['detail'] ?? 'Unknown Error'));
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.invoice.show', $invoice->id)->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
        return redirect()->route('admin.invoice.index')->with('success', 'Invoice berhasil dihapus.');
    }
}
