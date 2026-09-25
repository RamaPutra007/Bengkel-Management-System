<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Models\ServiceOrder;
use App\Http\Requests\StoreInspectionRequest;
use App\Http\Requests\UpdateInspectionRequest;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Inspection::with(['serviceOrder.customer', 'serviceOrder.vehicle'])->latest();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('serviceOrder', function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%");
            })->orWhereHas('serviceOrder.customer', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $inspections = $query->paginate(10);
        return view('admin.inspection.index', compact('inspections'));
    }

    public function create(Request $request)
    {
        $serviceOrderId = $request->get('service_order_id');
        if (!$serviceOrderId) {
            return redirect()->route('admin.service-order.index')->with('error', 'Silakan pilih Service Order untuk dibuatkan Laporan Inspeksi.');
        }

        $serviceOrder = ServiceOrder::with(['customer', 'vehicle'])->findOrFail($serviceOrderId);
        
        $existing = Inspection::where('service_order_id', $serviceOrder->id)->first();
        if ($existing) {
            return redirect()->route('admin.inspection.show', $existing->id)->with('success', 'Inspeksi untuk Service Order ini sudah ada.');
        }

        return view('admin.inspection.create', compact('serviceOrder'));
    }

    public function store(StoreInspectionRequest $request)
    {
        $inspection = Inspection::create($request->validated());
        
        // Kirim WhatsApp Laporan Inspeksi ke Pelanggan
        try {
            $inspection->load(['serviceOrder.customer', 'serviceOrder.vehicle']);
            $customer = $inspection->serviceOrder->customer;
            $vehicle = $inspection->serviceOrder->vehicle;

            if ($customer && $customer->phone) {
                $fonnte = app(\App\Services\FonnteService::class);
                $fonnte->sendTemplateMessage($customer->phone, 'laporan_inspeksi', [
                    'nama' => $customer->name,
                    'kendaraan' => $vehicle ? ($vehicle->brand . ' ' . $vehicle->model) : '-',
                    'plat_nomor' => $vehicle ? $vehicle->license_plate : '-',
                    'catatan_mekanik' => $inspection->mechanic_notes ?: '-',
                    'rekomendasi' => $inspection->recommendations ?: '-',
                    'nama_bengkel' => config('app.name', 'Bengkel')
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('WA Error on Inspection: ' . $e->getMessage());
        }

        return redirect()->route('admin.inspection.show', $inspection->id)->with('success', 'Laporan Inspeksi berhasil dibuat.');
    }

    public function show(string $id)
    {
        $inspection = Inspection::with(['serviceOrder.customer', 'serviceOrder.vehicle', 'serviceOrder.mechanic'])->findOrFail($id);
        return view('admin.inspection.show', compact('inspection'));
    }

    public function edit(string $id)
    {
        $inspection = Inspection::with('serviceOrder')->findOrFail($id);
        return view('admin.inspection.edit', compact('inspection'));
    }

    public function update(UpdateInspectionRequest $request, string $id)
    {
        $inspection = Inspection::findOrFail($id);
        $inspection->update($request->validated());
        return redirect()->route('admin.inspection.show', $inspection->id)->with('success', 'Laporan Inspeksi berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $inspection = Inspection::findOrFail($id);
        $inspection->delete();
        return redirect()->route('admin.inspection.index')->with('success', 'Laporan Inspeksi berhasil dihapus.');
    }
}
