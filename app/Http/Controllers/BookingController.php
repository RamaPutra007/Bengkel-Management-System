<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\Booking;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'license_plate' => 'required|string|max:20',
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'complaints' => 'required|string',
        ]);

        // Find or create customer
        $customer = Customer::firstOrCreate(
            ['phone' => $validated['phone']],
            ['name' => $validated['name']]
        );

        // Find or create vehicle
        $vehicle = Vehicle::firstOrCreate(
            ['license_plate' => strtoupper($validated['license_plate'])],
            [
                'customer_id' => $customer->id,
                'brand' => $validated['brand'],
                'model' => $validated['model']
            ]
        );

        // Create booking
        Booking::create([
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'booking_date' => $validated['booking_date'],
            'booking_time' => $validated['booking_time'],
            'complaints' => $validated['complaints'],
            'status' => 'Menunggu'
        ]);

        // Kirim Notifikasi Internal ke Admin (menggunakan nomor bot/admin)
        try {
            $fonnte = app(\App\Services\FonnteService::class);
            $config = \App\Models\WhatsappConfig::first();
            
            if ($config && $config->bot_number && $config->is_active) {
                $template = \App\Models\WhatsappTemplate::where('code', 'booking_baru')->first();
                if ($template && $template->is_active) {
                    $adminMessage = $template->content;
                    $adminMessage = str_replace('{nama}', $customer->name, $adminMessage);
                    $adminMessage = str_replace('{telepon}', $customer->phone, $adminMessage);
                    $adminMessage = str_replace('{kendaraan}', $vehicle->brand . ' ' . $vehicle->model, $adminMessage);
                    $adminMessage = str_replace('{plat_nomor}', $vehicle->license_plate, $adminMessage);
                    $adminMessage = str_replace('{jadwal}', \Carbon\Carbon::parse($validated['booking_date'])->format('d M Y') . " " . $validated['booking_time'], $adminMessage);
                    $adminMessage = str_replace('{keluhan}', $validated['complaints'], $adminMessage);
                    
                    $fonnte->sendMessage($config->bot_number, $adminMessage);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('WA Error on New Booking: ' . $e->getMessage());
        }

        return back()->with('success', 'Booking berhasil dikirim! Kami akan segera menghubungi Anda.');
    }
}
