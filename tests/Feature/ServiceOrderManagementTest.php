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

    public function test_service_order_create_view_renders_dual_pc_and_mobile_elements()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.service-order.create'));
        $response->assertStatus(200);
        // PC elements
        $response->assertSee('hidden md:table-header-group', false);
        $response->assertSee('hidden md:table-footer-group', false);
        // Mobile elements
        $response->assertSee('mobile-grand-total');
        $response->assertSee('fixed bottom-0', false);
    }

    public function test_service_order_edit_view_renders_dual_pc_and_mobile_elements()
    {
        $order = ServiceOrder::create([
            'order_number' => 'SO-EDIT-001',
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'mechanic_id' => $this->mechanic->id,
            'status' => 'pending',
            'total_price' => 75000,
        ]);

        $order->items()->create([
            'type' => 'service',
            'service_id' => $this->service->id,
            'item_name' => $this->service->name,
            'price' => 75000,
            'quantity' => 1,
            'subtotal' => 75000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.service-order.edit', $order->id));
        $response->assertStatus(200);
        // Check dual PC vs mobile elements
        $response->assertSee('hidden md:table-header-group', false);
        $response->assertSee('mobile-grand-total');
        $response->assertSee('fixed bottom-0', false);
        $response->assertSee('Simpan Perubahan');
    }

    public function test_service_master_views_render_successfully()
    {
        // Index
        $resIndex = $this->actingAs($this->admin)->get(route('admin.service.index'));
        $resIndex->assertStatus(200);

        // Create
        $resCreate = $this->actingAs($this->admin)->get(route('admin.service.create'));
        $resCreate->assertStatus(200);
        $resCreate->assertSee('Simpan Layanan');

        // Edit
        $resEdit = $this->actingAs($this->admin)->get(route('admin.service.edit', $this->service->id));
        $resEdit->assertStatus(200);
        $resEdit->assertSee('Simpan Perubahan');
    }

    public function test_can_create_new_service_order_without_items_when_pending()
    {
        $payload = [
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'mechanic_id' => $this->mechanic->id,
            'status' => 'pending',
            'notes' => 'Pesanan baru belum ditentukan itemnya',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.service-order.store'), $payload);
        $response->assertSessionHasNoErrors();

        $order = ServiceOrder::where('notes', 'Pesanan baru belum ditentukan itemnya')->first();
        $this->assertNotNull($order);
        $this->assertEquals(0, $order->total_price);
        $this->assertCount(0, $order->items);
    }

    public function test_cannot_create_service_order_with_status_completed_without_items()
    {
        $payload = [
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'mechanic_id' => $this->mechanic->id,
            'status' => 'completed',
            'notes' => 'Langsung diset selesai tanpa item',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.service-order.store'), $payload);
        $response->assertSessionHasErrors(['items']);
    }

    public function test_can_update_service_order_without_items_when_not_completed()
    {
        $order = ServiceOrder::create([
            'order_number' => 'SO-BOOKING-001',
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => 'pending',
            'total_price' => 0,
        ]);

        $updatePayload = [
            'mechanic_id' => $this->mechanic->id,
            'status' => 'in_progress',
            'notes' => 'Mekanik ditugaskan, belum pilih sparepart',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.service-order.update', $order->id), $updatePayload);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.service-order.show', $order->id));

        $order->refresh();
        $this->assertEquals($this->mechanic->id, $order->mechanic_id);
        $this->assertEquals('in_progress', $order->status);
        $this->assertEquals(0, $order->total_price);
    }

    public function test_cannot_update_service_order_to_completed_without_items()
    {
        $order = ServiceOrder::create([
            'order_number' => 'SO-BOOKING-002',
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => 'in_progress',
            'total_price' => 0,
        ]);

        $updatePayload = [
            'mechanic_id' => $this->mechanic->id,
            'status' => 'completed',
            'notes' => 'Coba selesaikan tanpa item',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.service-order.update', $order->id), $updatePayload);
        $response->assertSessionHasErrors(['items']);

        // Check error message
        $errors = session('errors');
        $this->assertTrue($errors->has('items'));
        $this->assertStringContainsString('selesai', $errors->first('items'));
    }

    public function test_can_accept_booking_and_update_it_without_items_until_completed()
    {
        $booking = \App\Models\Booking::create([
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'booking_time' => '10:00',
            'complaints' => 'Servis berkala',
            'status' => 'Menunggu',
        ]);

        // Accept booking
        $acceptResponse = $this->actingAs($this->admin)->post(route('admin.booking.accept', $booking->id));
        $booking->refresh();
        $this->assertEquals('Diproses', $booking->status);

        $order = ServiceOrder::where('customer_id', $this->customer->id)->where('status', 'pending')->latest('id')->first();
        $this->assertNotNull($order);
        $this->assertEquals(0, $order->total_price);
        $this->assertCount(0, $order->items);

        // Update booking order: assign mechanic without items
        $assignResponse = $this->actingAs($this->admin)->put(route('admin.service-order.update', $order->id), [
            'mechanic_id' => $this->mechanic->id,
            'status' => 'in_progress',
            'notes' => 'Mulai diperiksa',
        ]);
        $assignResponse->assertSessionHasNoErrors();

        // Trying to set completed without items fails
        $failResponse = $this->actingAs($this->admin)->put(route('admin.service-order.update', $order->id), [
            'mechanic_id' => $this->mechanic->id,
            'status' => 'completed',
            'notes' => 'Selesai tanpa item',
        ]);
        $failResponse->assertSessionHasErrors(['items']);

        // Completing with items succeeds
        $successResponse = $this->actingAs($this->admin)->put(route('admin.service-order.update', $order->id), [
            'mechanic_id' => $this->mechanic->id,
            'status' => 'completed',
            'notes' => 'Pengerjaan selesai dengan item',
            'items' => [
                [
                    'type' => 'service',
                    'item_id' => $this->service->id,
                    'quantity' => 1,
                    'price' => $this->service->price,
                ]
            ]
        ]);
        $successResponse->assertSessionHasNoErrors();
        $order->refresh();
        $this->assertEquals('completed', $order->status);
        $this->assertEquals($this->service->price, $order->total_price);
    }

    public function test_can_directly_update_status_from_show_to_in_progress()
    {
        $order = ServiceOrder::create([
            'order_number' => 'SO-DIRECT-001',
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => 'pending',
            'total_price' => 0,
        ]);

        // Show page has the direct status form
        $showRes = $this->actingAs($this->admin)->get(route('admin.service-order.show', $order->id));
        $showRes->assertStatus(200);
        $showRes->assertSee(route('admin.service-order.update-status', $order->id));

        // Submit status update to in_progress
        $response = $this->actingAs($this->admin)->patch(route('admin.service-order.update-status', $order->id), [
            'status' => 'in_progress',
        ]);

        $response->assertRedirect(route('admin.service-order.show', $order->id));
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('in_progress', $order->status);
    }

    public function test_cannot_directly_update_status_to_completed_from_show_if_no_items()
    {
        $order = ServiceOrder::create([
            'order_number' => 'SO-DIRECT-002',
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => 'in_progress',
            'total_price' => 0,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.service-order.update-status', $order->id), [
            'status' => 'completed',
        ]);

        $response->assertRedirect(route('admin.service-order.show', $order->id));
        $response->assertSessionHas('error');

        $order->refresh();
        $this->assertEquals('in_progress', $order->status);
    }

    public function test_can_directly_update_status_to_completed_from_show_if_has_items()
    {
        $order = ServiceOrder::create([
            'order_number' => 'SO-DIRECT-003',
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => 'in_progress',
            'total_price' => 75000,
        ]);

        $order->items()->create([
            'type' => 'service',
            'service_id' => $this->service->id,
            'item_name' => $this->service->name,
            'quantity' => 1,
            'price' => 75000,
            'subtotal' => 75000,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.service-order.update-status', $order->id), [
            'status' => 'completed',
        ]);

        $response->assertRedirect(route('admin.service-order.show', $order->id));
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('completed', $order->status);

        // Once completed, show page shows locked badge and hides direct update form
        $showRes = $this->actingAs($this->admin)->get(route('admin.service-order.show', $order->id));
        $showRes->assertStatus(200);
        $showRes->assertDontSee(route('admin.service-order.update-status', $order->id));
        $showRes->assertSee('Selesai (Completed)');
    }

    public function test_cannot_directly_update_status_if_order_already_completed()
    {
        $order = ServiceOrder::create([
            'order_number' => 'SO-DIRECT-004',
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => 'completed',
            'total_price' => 75000,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.service-order.update-status', $order->id), [
            'status' => 'in_progress',
        ]);

        $response->assertRedirect(route('admin.service-order.show', $order->id));
        $response->assertSessionHas('error');
    }
}
