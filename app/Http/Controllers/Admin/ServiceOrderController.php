<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use App\Models\ServiceOrderItem;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\Mechanic;
use App\Models\Service;
use App\Models\Sparepart;
use App\Http\Requests\StoreServiceOrderRequest;
use App\Http\Requests\UpdateServiceOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceOrder::with(['customer', 'vehicle', 'mechanic'])->latest();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $serviceOrders = $query->paginate(10);
        return view('admin.service_order.index', compact('serviceOrders'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $mechanics = Mechanic::where('status', 'active')->orderBy('name')->get();
        $services = Service::orderBy('name')->get();
        $spareparts = Sparepart::orderBy('name')->get();
        return view('admin.service_order.create', compact('customers', 'mechanics', 'services', 'spareparts'));
    }

    public function store(StoreServiceOrderRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            // Generate Order Number: SO-YYYYMMDD-XXXX
            $datePrefix = date('Ymd');
            $lastOrder = ServiceOrder::where('order_number', 'like', "SO-{$datePrefix}-%")->orderBy('id', 'desc')->first();
            $sequence = $lastOrder ? (int)substr($lastOrder->order_number, -4) + 1 : 1;
            $orderNumber = "SO-{$datePrefix}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            $serviceOrder = ServiceOrder::create([
                'order_number' => $orderNumber,
                'customer_id' => $data['customer_id'],
                'vehicle_id' => $data['vehicle_id'],
                'mechanic_id' => $data['mechanic_id'] ?? null,
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
                'total_price' => 0,
            ]);

            $totalPrice = 0;

            foreach ($data['items'] as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['price'];
                $totalPrice += $subtotal;

                $itemName = '';
                if ($itemData['type'] === 'service') {
                    $service = Service::find($itemData['item_id']);
                    $itemName = $service ? $service->name : 'Unknown Service';
                } else {
                    $sparepart = Sparepart::find($itemData['item_id']);
                    $itemName = $sparepart ? $sparepart->name : 'Unknown Sparepart';
                    
                    if ($sparepart) {
                        $sparepart->decrement('stock', $itemData['quantity']);
                        
                        \App\Models\InventoryTransaction::create([
                            'sparepart_id' => $sparepart->id,
                            'type' => 'out',
                            'quantity' => $itemData['quantity'],
                            'notes' => 'Otomatis: Digunakan untuk Service Order #' . $serviceOrder->id,
                            'user_id' => auth()->id() ?? 1,
                        ]);
                    }
                }

                ServiceOrderItem::create([
                    'service_order_id' => $serviceOrder->id,
                    'type' => $itemData['type'],
                    'service_id' => $itemData['type'] === 'service' ? $itemData['item_id'] : null,
                    'sparepart_id' => $itemData['type'] === 'sparepart' ? $itemData['item_id'] : null,
                    'item_name' => $itemName,
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'subtotal' => $subtotal,
                ]);
            }

            $serviceOrder->update(['total_price' => $totalPrice]);

            DB::commit();

            try {
                $fonnte = app(\App\Services\FonnteService::class);
                $customer = Customer::find($data['customer_id']);
                $vehicle = Vehicle::find($data['vehicle_id']);
                
                if ($customer && $customer->phone) {
                    $servicesStr = $serviceOrder->items->where('type', 'service')->map(function($item) {
                        return '- ' . $item->service->name;
                    })->implode("\n");
                    if (empty($servicesStr)) $servicesStr = '-';

                    $commonVars = [
                        'nama' => $customer->name,
                        'nomor_order' => $serviceOrder->order_number,
                        'kendaraan' => $vehicle ? ($vehicle->brand . ' ' . $vehicle->model) : '-',
                        'plat_nomor' => $vehicle ? $vehicle->license_plate : '-',
                        'nama_bengkel' => config('app.name', 'Bengkel')
                    ];

                    if ($serviceOrder->status === 'pending') {
                        $fonnte->sendTemplateMessage($customer->phone, 'diterima', array_merge($commonVars, [
                            'tanggal_pesan' => $serviceOrder->created_at->format('d M Y H:i'),
                        ]));
                    } elseif ($serviceOrder->status === 'in_progress') {
                        $fonnte->sendTemplateMessage($customer->phone, 'diproses', array_merge($commonVars, [
                            'layanan' => $servicesStr,
                        ]));
                    } elseif ($serviceOrder->status === 'completed') {
                        $fonnte->sendTemplateMessage($customer->phone, 'siap_diambil', array_merge($commonVars, [
                            'layanan' => $servicesStr,
                            'total' => number_format($totalPrice, 0, ',', '.'),
                        ]));
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Gagal mengirim WhatsApp: ' . $e->getMessage());
            }

            return redirect()->route('admin.service-order.show', $serviceOrder->id)->with('success', 'Service Order berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(string $id)
    {
        $serviceOrder = ServiceOrder::with(['customer', 'vehicle', 'mechanic', 'items.service', 'items.sparepart'])->findOrFail($id);
        return view('admin.service_order.show', compact('serviceOrder'));
    }

    public function edit(string $id)
    {
        $serviceOrder = ServiceOrder::with(['items'])->findOrFail($id);
        $mechanics = Mechanic::where('status', 'active')->orWhere('id', $serviceOrder->mechanic_id)->orderBy('name')->get();
        $services = Service::orderBy('name')->get();
        $spareparts = Sparepart::orderBy('name')->get();
        return view('admin.service_order.edit', compact('serviceOrder', 'mechanics', 'services', 'spareparts'));
    }

    public function update(UpdateServiceOrderRequest $request, string $id)
    {
        try {
            \Log::info('UpdateServiceOrderRequest Data:', $request->validated());
            DB::beginTransaction();
            $serviceOrder = ServiceOrder::with('items')->findOrFail($id);
            $data = $request->validated();
            
            // 1. Restore previous sparepart stock and remove old transaction logs
            foreach ($serviceOrder->items as $item) {
                if ($item->type === 'sparepart' && $item->sparepart_id) {
                    $sparepart = Sparepart::find($item->sparepart_id);
                    if ($sparepart) {
                        $sparepart->increment('stock', $item->quantity);
                        
                        // Delete automatic transactions created for this order
                        \App\Models\InventoryTransaction::where('sparepart_id', $sparepart->id)
                            ->where('type', 'out')
                            ->where('notes', 'like', '%Service Order #' . $serviceOrder->id . '%')
                            ->delete();
                    }
                }
            }

            // 2. Delete old items
            $serviceOrder->items()->delete();

            // 3. Update main info
            $serviceOrder->update([
                'mechanic_id' => $data['mechanic_id'] ?? null,
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
            ]);

            // 4. Insert new items and deduct stock (only if not cancelled)
            $totalPrice = 0;
            foreach ($data['items'] as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['price'];
                $totalPrice += $subtotal;

                $itemName = '';
                if ($itemData['type'] === 'service') {
                    $service = Service::find($itemData['item_id']);
                    $itemName = $service ? $service->name : 'Unknown Service';
                } else {
                    $sparepart = Sparepart::find($itemData['item_id']);
                    $itemName = $sparepart ? $sparepart->name : 'Unknown Sparepart';
                    
                    if ($sparepart && $data['status'] !== 'cancelled') {
                        $sparepart->decrement('stock', $itemData['quantity']);
                        
                        \App\Models\InventoryTransaction::create([
                            'sparepart_id' => $sparepart->id,
                            'type' => 'out',
                            'quantity' => $itemData['quantity'],
                            'notes' => 'Otomatis: Digunakan untuk Service Order #' . $serviceOrder->id,
                            'user_id' => auth()->id() ?? 1,
                        ]);
                    }
                }

                ServiceOrderItem::create([
                    'service_order_id' => $serviceOrder->id,
                    'type' => $itemData['type'],
                    'service_id' => $itemData['type'] === 'service' ? $itemData['item_id'] : null,
                    'sparepart_id' => $itemData['type'] === 'sparepart' ? $itemData['item_id'] : null,
                    'item_name' => $itemName,
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'subtotal' => $subtotal,
                ]);
            }

            $serviceOrder->update(['total_price' => $totalPrice]);

            DB::commit();

            try {
                $fonnte = app(\App\Services\FonnteService::class);
                $customer = $serviceOrder->customer;
                $vehicle = $serviceOrder->vehicle;
                
                if ($customer && $customer->phone) {
                    $servicesStr = $serviceOrder->items->where('type', 'service')->map(function($item) {
                        return '- ' . $item->service->name;
                    })->implode("\n");
                    if (empty($servicesStr)) $servicesStr = '-';

                    $commonVars = [
                        'nama' => $customer->name,
                        'nomor_order' => $serviceOrder->order_number,
                        'kendaraan' => $vehicle ? ($vehicle->brand . ' ' . $vehicle->model) : '-',
                        'plat_nomor' => $vehicle ? $vehicle->license_plate : '-',
                        'nama_bengkel' => config('app.name', 'Bengkel')
                    ];

                    if ($data['status'] === 'in_progress') {
                        $fonnte->sendTemplateMessage($customer->phone, 'diproses', array_merge($commonVars, [
                            'layanan' => $servicesStr,
                        ]));
                    } elseif ($data['status'] === 'completed') {
                        $fonnte->sendTemplateMessage($customer->phone, 'siap_diambil', array_merge($commonVars, [
                            'layanan' => $servicesStr,
                            'total' => number_format($totalPrice, 0, ',', '.'),
                        ]));
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Gagal mengirim WhatsApp (Update): ' . $e->getMessage());
            }

            return redirect()->route('admin.service-order.show', $serviceOrder->id)->with('success', 'Service Order berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(string $id)
    {
        $serviceOrder = ServiceOrder::findOrFail($id);
        
        // Restore stock if deleting
        foreach ($serviceOrder->items as $item) {
            if ($item->type === 'sparepart' && $item->sparepart_id) {
                $sparepart = Sparepart::find($item->sparepart_id);
                if ($sparepart) {
                    $sparepart->increment('stock', $item->quantity);
                }
            }
        }
        
        $serviceOrder->delete(); // Soft delete usually doesn't delete items, but let's just use it
        return redirect()->route('admin.service-order.index')->with('success', 'Service Order berhasil dihapus.');
    }

    // Helper for AJAX
    public function getCustomerVehicles($customerId)
    {
        $vehicles = Vehicle::where('customer_id', $customerId)->get();
        return response()->json($vehicles);
    }
}
