<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Invoice;
use App\Models\ServiceOrder;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\KiPayConfig;
use App\Services\KiPayService;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class KiPayIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'ADMIN']);
        Role::create(['name' => 'KASIR']);
        Role::create(['name' => 'OWNER']);
    }

    public function test_kipay_service_verifies_webhook_signature()
    {
        config(['kipay.webhook_secret' => 'my_test_secret_key']);
        $service = app(KiPayService::class);

        $payload = json_encode(['event' => 'transaction.paid', 'trx_id' => 'SBX-123']);
        $timestamp = '2026-11-04T05:00:00Z';
        $deliveryId = 'del_abc123';

        $signatureToHash = "{$timestamp}.{$deliveryId}.{$payload}";
        $validHash = hash_hmac('sha256', $signatureToHash, 'my_test_secret_key');

        // With sha256= prefix
        $this->assertTrue(
            $service->verifyWebhookSignature($payload, "sha256={$validHash}", $timestamp, $deliveryId)
        );

        // Without prefix
        $this->assertTrue(
            $service->verifyWebhookSignature($payload, $validHash, $timestamp, $deliveryId)
        );

        // Invalid signature
        $this->assertFalse(
            $service->verifyWebhookSignature($payload, 'sha256=invalidhash', $timestamp, $deliveryId)
        );
    }

    public function test_kipay_webhook_acknowledges_test_event()
    {
        $response = $this->postJson('/api/webhook/kipay', [
            'event' => 'webhook.test',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_kipay_webhook_rejects_invalid_signature_when_secret_set()
    {
        config(['kipay.webhook_secret' => 'super_secret_key']);

        $response = $this->withHeaders([
            'X-Webhook-Event' => 'transaction.paid',
            'X-Webhook-Delivery' => 'del_123',
            'X-Webhook-Timestamp' => '2026-11-04T05:00:00Z',
            'X-Webhook-Signature' => 'sha256=wrong_signature',
        ])->postJson('/api/webhook/kipay', [
            'event' => 'transaction.paid',
            'transaction' => [
                'trx_id' => 'SBX-TEST-001',
                'status' => 'paid',
            ]
        ]);

        $response->assertStatus(401);
    }

    public function test_kipay_webhook_marks_invoice_paid_and_is_idempotent()
    {
        config(['kipay.webhook_secret' => '']); // skip secret check for this functional flow

        $customer = Customer::create([
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'email' => 'budi@example.com',
            'address' => 'Jl. Merdeka No. 1',
        ]);

        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'license_plate' => 'B 1234 ABC',
            'brand' => 'Honda',
            'model' => 'Vario 150',
            'year' => 2022,
        ]);

        $serviceOrder = ServiceOrder::create([
            'order_number' => 'SO-20261104-0001',
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'status' => 'completed',
            'entry_date' => now(),
            'total_price' => 150000,
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-20261104-0001',
            'service_order_id' => $serviceOrder->id,
            'subtotal' => 150000,
            'grand_total' => 150000,
            'payment_status' => 'unpaid',
            'kipay_trx_id' => 'SBX-20261104-ABCDEF',
        ]);

        $payload = [
            'event' => 'transaction.paid',
            'sent_at' => '2026-11-04T05:10:00Z',
            'transaction' => [
                'trx_id' => 'SBX-20261104-ABCDEF',
                'status' => 'paid',
                'amount' => 285215,
                'fee_amount' => 2925,
                'net_amount' => 282290,
                'fee_bearer' => 'merchant',
                'provider' => 'shopeepay',
                'note' => 'INV-20261104-0001',
                'matched_at' => '2026-11-04 05:10:00',
            ]
        ];

        // 1. First webhook call: should mark invoice as paid
        $response1 = $this->withHeaders([
            'X-Webhook-Event' => 'transaction.paid',
            'X-Webhook-Delivery' => 'del_unique_delivery_001',
            'X-Webhook-Timestamp' => '2026-11-04T05:10:00Z',
        ])->postJson('/api/webhook/kipay', $payload);
        $response1->assertStatus(200);

        $invoice->refresh();
        $this->assertEquals('paid', $invoice->payment_status);
        $this->assertEquals('qris', $invoice->payment_method);
        $this->assertEquals('shopeepay', $invoice->kipay_provider);
        $this->assertEquals(282290, $invoice->kipay_net_amount);
        $this->assertEquals('del_unique_delivery_001', $invoice->kipay_delivery_id);
        $this->assertNotNull($invoice->paid_at);

        // 2. Second webhook call (retry / duplicate): should still return 200 OK without errors (idempotent)
        $response2 = $this->withHeaders([
            'X-Webhook-Event' => 'transaction.paid',
            'X-Webhook-Delivery' => 'del_unique_delivery_001',
            'X-Webhook-Timestamp' => '2026-11-04T05:10:00Z',
        ])->postJson('/api/webhook/kipay', $payload);
        $response2->assertStatus(200);
        $response2->assertJson([
            'success' => true,
            'message' => 'Invoice already paid',
        ]);
    }

    public function test_kipay_webhook_handles_transaction_expired()
    {
        $customer = Customer::create([
            'name' => 'Siti',
            'phone' => '08777888999',
        ]);

        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'license_plate' => 'L 1234 CD',
            'brand' => 'Daihatsu',
            'model' => 'Sigra',
        ]);

        $serviceOrder = ServiceOrder::create([
            'order_number' => 'SO-20261104-0004',
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'status' => 'completed',
            'entry_date' => now(),
            'total_price' => 100000,
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-20261104-0004',
            'service_order_id' => $serviceOrder->id,
            'subtotal' => 100000,
            'grand_total' => 100000,
            'payment_status' => 'unpaid',
            'kipay_trx_id' => 'SBX-EXPIRED-TEST',
        ]);

        $response = $this->withHeaders([
            'X-Webhook-Event' => 'transaction.expired',
            'X-Webhook-Delivery' => 'del_expired_001',
            'X-Webhook-Timestamp' => '2026-11-04T05:25:00Z',
        ])->postJson('/api/webhook/kipay', [
            'event' => 'transaction.expired',
            'transaction' => [
                'trx_id' => 'SBX-EXPIRED-TEST',
                'status' => 'expired',
            ]
        ]);

        $response->assertStatus(200);
        $invoice->refresh();
        $this->assertEquals('unpaid', $invoice->payment_status);
        $this->assertEquals('del_expired_001', $invoice->kipay_delivery_id);
    }

    public function test_admin_can_access_and_save_kipay_config()
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');

        $response = $this->actingAs($admin)->get(route('admin.kipay.index'));
        $response->assertStatus(200);

        $saveResponse = $this->actingAs($admin)->post(route('admin.kipay.update'), [
            'api_key' => 'apg_sandbox_test12345678',
            'webhook_secret' => 'whsec_testsecret1234',
            'mode' => 'sandbox',
            'is_active' => '1',
        ]);

        $saveResponse->assertRedirect(route('admin.kipay.index'));
        $this->assertDatabaseHas('kipay_configs', [
            'mode' => 'sandbox',
            'is_active' => true,
        ]);
    }

    public function test_invoice_check_kipay_status_flow()
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');

        $customer = Customer::create([
            'name' => 'Dewi',
            'phone' => '08987654321',
        ]);

        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'license_plate' => 'D 9999 XYZ',
            'brand' => 'Yamaha',
            'model' => 'NMAX',
            'year' => 2023,
        ]);

        $serviceOrder = ServiceOrder::create([
            'order_number' => 'SO-20261104-0002',
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'status' => 'completed',
            'entry_date' => now(),
            'total_price' => 250000,
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-20261104-0002',
            'service_order_id' => $serviceOrder->id,
            'subtotal' => 250000,
            'grand_total' => 250000,
            'payment_status' => 'unpaid',
            'kipay_trx_id' => 'SBX-TEST-CHECK-999',
        ]);

        config(['kipay.api_key' => 'apg_sandbox_test']);

        Http::fake([
            'https://api.kipay.id/api/pay/*/transactions/SBX-TEST-CHECK-999' => Http::response([
                'trx_id' => 'SBX-TEST-CHECK-999',
                'status' => 'paid',
                'provider' => 'bca',
                'amount' => 250123,
                'matched_at' => '2026-11-04 05:20:00',
            ], 200),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.invoice.check-kipay', $invoice->id));
        $response->assertRedirect(route('admin.invoice.show', $invoice->id));

        $invoice->refresh();
        $this->assertEquals('paid', $invoice->payment_status);
        $this->assertEquals('bca', $invoice->kipay_provider);
    }

    public function test_public_checkout_page_and_status_polling()
    {
        $customer = Customer::create([
            'name' => 'Ahmad',
            'phone' => '08111222333',
        ]);

        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'license_plate' => 'B 1111 AAA',
            'brand' => 'Toyota',
            'model' => 'Avanza',
        ]);

        $serviceOrder = ServiceOrder::create([
            'order_number' => 'SO-20261104-0003',
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'status' => 'completed',
            'entry_date' => now(),
            'total_price' => 300000,
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-20261104-0003',
            'service_order_id' => $serviceOrder->id,
            'subtotal' => 300000,
            'grand_total' => 300000,
            'payment_status' => 'unpaid',
            'kipay_trx_id' => 'SBX-CHECKOUT-TEST',
        ]);

        // 1. Customer opens checkout page
        $pageResponse = $this->get(route('checkout.show', $invoice->invoice_number));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('INV-20261104-0003');

        // 2. Status polling endpoint before payment
        $statusResponse = $this->get(route('checkout.status', $invoice->invoice_number));
        $statusResponse->assertStatus(200);
        $statusResponse->assertJson([
            'is_paid' => false,
            'payment_status' => 'unpaid',
        ]);

        // 3. When invoice is paid, customer checkout shows official invoice document with LUNAS stamp
        $invoice->update([
            'payment_status' => 'paid',
            'payment_method' => 'qris',
            'kipay_provider' => 'SHOPEEPAY',
            'paid_at' => now(),
        ]);

        $paidPageResponse = $this->get(route('checkout.show', $invoice->invoice_number));
        $paidPageResponse->assertStatus(200);
        $paidPageResponse->assertSee('LUNAS');
        $paidPageResponse->assertSee('id="invoice-doc"', false);
        $paidPageResponse->assertSee('SHOPEEPAY');
        $paidPageResponse->assertSee('Cetak / Simpan Nota');
    }

    public function test_internal_checkout_qr_route_protects_credentials()
    {
        config(['kipay.api_key' => 'apg_sandbox_test']);

        Http::fake([
            'https://api.kipay.id/api/pay/*/transactions/SBX-QR-TEST/qr.png' => Http::response('fake-png-binary-data', 200, [
                'Content-Type' => 'image/png'
            ]),
        ]);

        $response = $this->get(route('checkout.qr', 'SBX-QR-TEST'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
        $this->assertEquals('fake-png-binary-data', $response->getContent());
    }

    public function test_invoice_show_displays_enlarged_qris_and_wa_direct_link()
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');

        $customer = Customer::create([
            'name' => 'Bambang Sudirman',
            'phone' => '081234567890',
        ]);

        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'license_plate' => 'B 9999 PRO',
            'brand' => 'Honda',
            'model' => 'HRV',
        ]);

        $serviceOrder = ServiceOrder::create([
            'order_number' => 'SO-20261105-9999',
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'status' => 'completed',
            'entry_date' => now(),
            'total_price' => 500000,
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-20261105-9999',
            'service_order_id' => $serviceOrder->id,
            'subtotal' => 500000,
            'grand_total' => 500000,
            'payment_status' => 'unpaid',
            'payment_method' => 'qris',
            'kipay_trx_id' => 'SBX-20261105-TESTQR',
            'kipay_amount' => 500000,
            'kipay_expired_at' => now()->addMinutes(15),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.invoice.show', $invoice->id));

        $response->assertStatus(200);
        $response->assertSee('KIPAY QRIS RESMI');
        $response->assertSee('Perbesar Layar');
        $response->assertSee('Bayar Tunai');
        $response->assertSee('https://wa.me/6281234567890?text=', false);
        // When unpaid, the invoice document paper below is hidden
        $response->assertDontSee('id="invoice-doc"', false);

        // When invoice is paid, QRIS standee is hidden and official invoice-doc with LUNAS stamp appears
        $invoice->update([
            'payment_status' => 'paid',
            'payment_method' => 'qris',
            'kipay_provider' => 'SHOPEEPAY',
            'paid_at' => now(),
        ]);

        $paidResponse = $this->actingAs($admin)->get(route('admin.invoice.show', $invoice->id));
        $paidResponse->assertStatus(200);
        $paidResponse->assertSee('LUNAS');
        $paidResponse->assertSee('id="invoice-doc"', false);
        $paidResponse->assertSee('SHOPEEPAY');
        $paidResponse->assertDontSee('KIPAY QRIS RESMI');
    }

    public function test_fonnte_service_formats_phone_numbers_consistently()
    {
        $fonnteService = app(\App\Services\FonnteService::class);

        $this->assertEquals('6281234567890', $fonnteService->formatPhoneNumber('081234567890'));
        $this->assertEquals('6281234567890', $fonnteService->formatPhoneNumber('0812-3456-7890'));
        $this->assertEquals('6281234567890', $fonnteService->formatPhoneNumber('+6281234567890'));
        $this->assertEquals('6281234567890', $fonnteService->formatPhoneNumber('81234567890'));
    }

    public function test_simulate_kipay_auto_renews_expired_transaction()
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');

        $customer = Customer::create(['name' => 'Doni', 'phone' => '08123456789']);
        $vehicle = Vehicle::create(['customer_id' => $customer->id, 'license_plate' => 'B 1234 DON', 'brand' => 'Honda', 'model' => 'Beat']);
        $serviceOrder = ServiceOrder::create([
            'order_number' => 'SO-SIM-001',
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'status' => 'completed',
            'entry_date' => now(),
            'total_price' => 150000,
        ]);
        $invoice = Invoice::create([
            'invoice_number' => 'INV-SIM-001',
            'service_order_id' => $serviceOrder->id,
            'subtotal' => 150000,
            'grand_total' => 150000,
            'payment_status' => 'unpaid',
            'kipay_trx_id' => 'SBX-OLD-EXPIRED',
            'kipay_expired_at' => now()->subMinutes(10), // expired 10 minutes ago
        ]);

        config(['kipay.api_key' => 'apg_sandbox_test']);

        Http::fake([
            'https://api.kipay.id/api/pay/*/transactions' => Http::response([
                'success' => true,
                'trx_id' => 'SBX-NEW-RENEWED',
                'amount' => 150000,
                'qr_url' => 'https://api.kipay.id/qr/test.png',
                'qr_payload' => '000201...',
                'expires_at' => now()->addMinutes(15)->toIso8601String(),
            ], 201),
            'https://api.kipay.id/api/pay/*/transactions/SBX-NEW-RENEWED/simulate' => Http::response([
                'status' => 'paid',
                'provider' => 'shopeepay',
                'matched_at' => now()->toIso8601String(),
            ], 200),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.invoice.simulate-kipay', $invoice->id));

        $response->assertRedirect(route('admin.invoice.show', $invoice->id));
        $response->assertSessionHas('success');

        $invoice->refresh();
        $this->assertEquals('paid', $invoice->payment_status);
        $this->assertEquals('SBX-NEW-RENEWED', $invoice->kipay_trx_id);
    }
}
