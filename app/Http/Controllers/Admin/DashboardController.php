<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();

        $stats = [
            'total_customers' => \App\Models\Customer::count(),
            'total_vehicles' => \App\Models\Vehicle::count(),
            'service_order_aktif' => \App\Models\ServiceOrder::whereIn('status', ['pending', 'in_progress'])->count(),
            'service_order_selesai' => \App\Models\ServiceOrder::where('status', 'completed')->where('created_at', '>=', $startOfMonth)->count(),
            'total_mechanics' => \App\Models\Mechanic::count(),
            'total_spare_parts' => \App\Models\Sparepart::count(),
            'stok_menipis' => \App\Models\Sparepart::whereColumn('stock', '<=', 'reorder_level')->count(),
        ];
        $pending_bookings = \App\Models\Booking::with(['customer', 'vehicle'])->where('status', 'Menunggu')->get();
        
        return view('admin.dashboard', compact('stats', 'pending_bookings'));
    }

    public function acceptBooking(Request $request, $id)
    {
        $booking = \App\Models\Booking::findOrFail($id);
        
        if ($booking->status !== 'Menunggu') {
            return redirect()->back()->with('error', 'Booking sudah diproses sebelumnya.');
        }

        // Ubah status booking
        $booking->update(['status' => 'Diproses']);

        // Generate Order Number: SO-YYYYMMDD-XXXX
        $datePrefix = date('Ymd');
        $lastOrder = \App\Models\ServiceOrder::where('order_number', 'like', "SO-{$datePrefix}-%")->orderBy('id', 'desc')->first();
        $sequence = $lastOrder ? (int)substr($lastOrder->order_number, -4) + 1 : 1;
        $orderNumber = "SO-{$datePrefix}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        // Buat Service Order dari Booking
        $serviceOrder = \App\Models\ServiceOrder::create([
            'order_number' => $orderNumber,
            'customer_id' => $booking->customer_id,
            'vehicle_id' => $booking->vehicle_id,
            'status' => 'pending', // Pending in SO means waiting for mechanic
            'notes' => "Jadwal: " . \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') . " " . $booking->booking_time . "\nKeluhan: " . $booking->complaints,
            'total_price' => 0,
        ]);

        // Kirim notifikasi WA (Notifikasi Pesanan Diterima)
        try {
            $fonnte = app(\App\Services\FonnteService::class);
            $customer = $booking->customer;
            $vehicle = $booking->vehicle;
            
            if ($customer && $customer->phone) {
                $commonVars = [
                    'nama' => $customer->name,
                    'nomor_order' => $serviceOrder->order_number,
                    'kendaraan' => $vehicle ? ($vehicle->brand . ' ' . $vehicle->model) : '-',
                    'plat_nomor' => $vehicle ? $vehicle->license_plate : '-',
                    'nama_bengkel' => config('app.name', 'Bengkel')
                ];
                
                $template = \App\Models\WhatsappTemplate::where('code', 'diterima')->first();
                if ($template && $template->is_active) {
                    $message = $template->content;
                    foreach ($commonVars as $key => $val) {
                        $message = str_replace('{' . $key . '}', $val, $message);
                    }
                    $message = str_replace('{daftar_servis}', '-', $message);
                    $message = str_replace('{estimasi_waktu}', 'Akan diinformasikan oleh mekanik', $message);
                    
                    $fonnte->sendMessage($customer->phone, $message);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('WA Error on Accept Booking: ' . $e->getMessage());
        }

        return redirect()->route('admin.service-order.edit', $serviceOrder->id)->with('success', 'Booking berhasil diterima dan diubah menjadi Service Order! Silakan tentukan layanan/mekanik.');
    }
}
