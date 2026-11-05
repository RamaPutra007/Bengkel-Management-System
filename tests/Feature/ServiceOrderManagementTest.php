<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Mechanic;
use Spatie\Permission\Models\Role;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\Sparepart;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Customer $customer;
    protected Vehicle $vehicle;
    protected Mechanic $mechanic;
    protected Service $service;
    protected Sparepart $sparepart;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'ADMIN']);
        Role::firstOrCreate(['name' => 'KASIR']);
        Role::firstOrCreate(['name' => 'OWNER']);

        $this->admin = User::factory()->create();
        $this->admin->roles()->attach(Role::where('name', 'ADMIN')->first());

        $this->customer = Customer::create([
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 1'
        ]);

        $this->vehicle = Vehicle::create([
            'customer_id' => $this->customer->id,
            'license_plate' => 'B1234XYZ',
            'brand' => 'Honda',
            'model' => 'Vario 150',
            'year' => 2022
        ]);

        $this->mechanic = Mechanic::create([
            'name' => 'Pak Joko',
            'phone' => '08987654321',
            'status' => 'active'
        ]);

        $this->service = Service::create([
            'name' => 'Ganti Oli & Tune Up',
            'price' => 75000,
        ]);

        $this->sparepart = Sparepart::create([
            'name' => 'Oli MPX 0.8L',
            'part_number' => 'SP-OLI-01',
            'price' => 55000,
            'stock' => 10,
        ]);
    }

    public function test_can_create_service_order_with_correct_order_number_and_stock()
    {
        $payload = [
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'mechanic_id' => $this->mechanic->id,
            'status' => 'in_progress',
            'notes' => 'Keluhan mesin brebet',
            'items' => [
                [
                    'type' => 'service',
                    'item_id' => $this->service->id,
                    'quantity' => 1,
                    'price' => 75000,
                ],
                [
                    'type' => 'sparepart',
                    'item_id' => $this->sparepart->id,
                    'quantity' => 2,
                    'price' => 55000,
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.service-order.store'), $payload);

        $expectedOrderNumber = 'SO-' . date('Ymd') . '-0001';
        $order = ServiceOrder::where('order_number', $expectedOrderNumber)->first();

        $this->assertNotNull($order, 'Service order harus berhasil dibuat.');
        $response->assertRedirect(route('admin.service-order.show', $order->id));
        $this->assertEquals(185000, $order->total_price); // 75000 + (2 * 55000) = 185000

        // Stock harus berkurang 2 (10 - 2 = 8)
        $this->sparepart->refresh();
        $this->assertEquals(8, $this->sparepart->stock);
    }

    public function test_can_update_service_order_and_reconcile_items_and_stock()
    {
        // Buat order pertama
        $order = ServiceOrder::create([
            'order_number' => 'SO-' . date('Ymd') . '-0001',
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'mechanic_id' => $this->mechanic->id,
            'status' => 'pending',
            'total_price' => 55000,
        ]);

        $order->items()->create([
            'type' => 'sparepart',
            'sparepart_id' => $this->sparepart->id,
            'item_name' => $this->sparepart->name,
            'quantity' => 1,
            'price' => 55000,
            'subtotal' => 55000,
        ]);
        $this->sparepart->decrement('stock', 1); // 9 remaining

        $updatePayload = [
            'mechanic_id' => $this->mechanic->id,
            'status' => 'completed',
            'notes' => 'Selesai diservis',
            'items' => [
                [
                    'type' => 'service',
                    'item_id' => $this->service->id,
                    'quantity' => 1,
                    'price' => 75000,
                ],
                [
                    'type' => 'sparepart',
                    'item_id' => $this->sparepart->id,
                    'quantity' => 3, // ganti jadi 3 botol
                    'price' => 55000,
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.service-order.update', $order->id), $updatePayload);

        $response->assertRedirect(route('admin.service-order.show', $order->id));
        $order->refresh();
        $this->assertEquals('completed', $order->status);
        $this->assertEquals(240000, $order->total_price); // 75000 + (3 * 55000) = 240000

        // Stock awal 10, order awal ambil 1 (sisa 9). Update mengembalikan 1 (10) lalu kurangi 3 (sisa 7).
        $this->sparepart->refresh();
        $this->assertEquals(7, $this->sparepart->stock);
    }

    public function test_edit_view_renders_existing_items()
    {
        $order = ServiceOrder::create([
            'order_number' => 'SO-' . date('Ymd') . '-0001',
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'mechanic_id' => $this->mechanic->id,
            'status' => 'pending',
            'total_price' => 55000,
        ]);

        $order->items()->create([
            'type' => 'sparepart',
            'sparepart_id' => $this->sparepart->id,
            'item_name' => $this->sparepart->name,
            'quantity' => 1,
            'price' => 55000,
            'subtotal' => 55000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.service-order.edit', $order->id));
        $response->assertStatus(200);
        $response->assertSee('Oli MPX 0.8L');
        $response->assertSee('55.000');
    }

    public function test_completed_service_order_hides_edit_action_in_index_and_detail()
    {
        $pendingOrder = ServiceOrder::create([
            'order_number' => 'SO-PENDING-001',
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'mechanic_id' => $this->mechanic->id,
            'status' => 'pending',
            'total_price' => 50000,
        ]);

        $completedOrder = ServiceOrder::create([
            'order_number' => 'SO-COMPLETED-001',
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'mechanic_id' => $this->mechanic->id,
            'status' => 'completed',
            'total_price' => 75000,
        ]);

        $indexResponse = $this->actingAs($this->admin)->get(route('admin.service-order.index'));
        $indexResponse->assertStatus(200);
        // Pending order should have edit link
        $indexResponse->assertSee(route('admin.service-order.edit', $pendingOrder->id));
        // Completed order should NOT have edit link
        $indexResponse->assertDontSee(route('admin.service-order.edit', $completedOrder->id));

        // In show view, completed order should not have edit button
        $showResponse = $this->actingAs($this->admin)->get(route('admin.service-order.show', $completedOrder->id));
        $showResponse->assertStatus(200);
        $showResponse->assertDontSee(route('admin.service-order.edit', $completedOrder->id));
    }

    public function test_completed_service_order_cannot_be_edited_via_route()
    {
        $completedOrder = ServiceOrder::create([
            'order_number' => 'SO-COMPLETED-002',
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'mechanic_id' => $this->mechanic->id,
            'status' => 'completed',
            'total_price' => 75000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.service-order.edit', $completedOrder->id));
        $response->assertRedirect(route('admin.service-order.show', $completedOrder->id));
        $response->assertSessionHas('warning');
    }
}
