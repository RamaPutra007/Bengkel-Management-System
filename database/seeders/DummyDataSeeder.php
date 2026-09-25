<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $adminId = DB::table('users')->where('email', 'admin@example.com')->value('id') ?? 1;

        // 1. Customers
        $customerIds = [];
        for ($i = 1; $i <= 5; $i++) {
            $customerIds[] = DB::table('customers')->insertGetId([
                'name' => 'Customer ' . $i,
                'email' => "customer$i@example.com",
                'phone' => '0812345678' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'address' => 'Jl. Pelanggan No. ' . $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 2. Vehicles
        $vehicleIds = [];
        $vehicleBrands = ['Toyota', 'Honda', 'Suzuki', 'Daihatsu', 'Mitsubishi'];
        $vehicleModels = ['Avanza', 'Brio', 'Ertiga', 'Xenia', 'Xpander'];
        foreach ($customerIds as $index => $cId) {
            $vehicleIds[] = DB::table('vehicles')->insertGetId([
                'customer_id' => $cId,
                'license_plate' => 'B ' . rand(1000, 9999) . ' ABC',
                'brand' => $vehicleBrands[$index],
                'model' => $vehicleModels[$index],
                'year' => rand(2015, 2023),
                'color' => 'Hitam',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 3. Mechanics
        $mechanicIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $mechanicIds[] = DB::table('mechanics')->insertGetId([
                'name' => 'Mekanik Handal ' . $i,
                'phone' => '0898765432' . $i,
                'specialization' => 'General Repair',
                'status' => 'ACTIVE',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 4. Services
        $serviceIds = [];
        $servicesData = [
            ['name' => 'Ganti Oli Mesin', 'price' => 150000],
            ['name' => 'Tune Up', 'price' => 250000],
            ['name' => 'Servis Rem', 'price' => 100000],
        ];
        foreach ($servicesData as $s) {
            $serviceIds[] = DB::table('services')->insertGetId([
                'name' => $s['name'],
                'description' => 'Layanan ' . $s['name'],
                'price' => $s['price'],
                'estimated_time' => 60,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 5. Spareparts
        $sparepartIds = [];
        $sparepartsData = [
            ['name' => 'Oli Mesin TMO 10W-40', 'price' => 85000, 'stock' => 5], // low stock
            ['name' => 'Busi NGK', 'price' => 25000, 'stock' => 50],
            ['name' => 'Kampas Rem Depan', 'price' => 150000, 'stock' => 3], // low stock
            ['name' => 'Filter Udara', 'price' => 60000, 'stock' => 20],
        ];
        foreach ($sparepartsData as $sp) {
            $sparepartIds[] = DB::table('spareparts')->insertGetId([
                'name' => $sp['name'],
                'part_number' => 'PN-' . strtoupper(Str::random(5)),
                'brand' => 'OEM',
                'price' => $sp['price'],
                'stock' => $sp['stock'],
                'reorder_level' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 6. Inventory Transactions
        foreach ($sparepartIds as $spId) {
            DB::table('inventory_transactions')->insert([
                'sparepart_id' => $spId,
                'user_id' => $adminId,
                'type' => 'IN',
                'quantity' => 10,
                'notes' => 'Initial stock seeder',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 7. Bookings
        for ($i = 1; $i <= 2; $i++) {
            DB::table('bookings')->insert([
                'customer_id' => $customerIds[$i],
                'vehicle_id' => $vehicleIds[$i],
                'booking_date' => $now->copy()->addDays($i)->format('Y-m-d'),
                'booking_time' => '10:00',
                'complaints' => 'Mesin agak kasar',
                'status' => 'PENDING',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 8. Service Orders & Invoices
        // Completed Order
        $so1Id = DB::table('service_orders')->insertGetId([
            'order_number' => 'SO-0001',
            'customer_id' => $customerIds[0],
            'vehicle_id' => $vehicleIds[0],
            'mechanic_id' => $mechanicIds[0],
            'status' => 'completed',
            'notes' => 'Ganti oli dan tune up, sudah selesai',
            'total_price' => 150000,
            'created_at' => $now->copy()->subDays(2),
            'updated_at' => $now->copy()->subDays(2),
        ]);
        
        DB::table('service_order_items')->insert([
            'service_order_id' => $so1Id,
            'type' => 'service',
            'service_id' => $serviceIds[0],
            'item_name' => 'Ganti Oli Mesin',
            'quantity' => 1,
            'price' => 150000,
            'subtotal' => 150000,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        DB::table('invoices')->insert([
            'invoice_number' => 'INV-0001',
            'service_order_id' => $so1Id,
            'subtotal' => 150000,
            'grand_total' => 150000,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'paid_at' => $now->copy()->subDays(2),
            'created_at' => $now->copy()->subDays(2),
            'updated_at' => $now->copy()->subDays(2),
        ]);

        // In Progress Order
        DB::table('service_orders')->insertGetId([
            'order_number' => 'SO-0002',
            'customer_id' => $customerIds[1],
            'vehicle_id' => $vehicleIds[1],
            'mechanic_id' => $mechanicIds[1],
            'status' => 'in_progress',
            'notes' => 'Rem bunyi, sedang bongkar roda',
            'total_price' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        // Pending Order
        DB::table('service_orders')->insertGetId([
            'order_number' => 'SO-0003',
            'customer_id' => $customerIds[2],
            'vehicle_id' => $vehicleIds[2],
            'status' => 'pending',
            'notes' => 'Lampu mati, menunggu mekanik',
            'total_price' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
