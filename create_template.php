<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$t = \App\Models\WhatsappTemplate::where('code', 'qris_payment')->first();
if (!$t) {
    $t = new \App\Models\WhatsappTemplate();
    $t->code = 'qris_payment';
}
$t->name = 'Kirim QRIS Tagihan';
$t->content = "*Tagihan Servis - {bengkel_name}*\n\nHalo *{customer_name}*,\nBerikut adalah tautan QRIS untuk pembayaran tagihan Anda (No. {invoice_number}) sebesar *Rp {grand_total}*.\n\nKlik tautan ini untuk melihat dan scan QRIS:\n{qris_url}\n\nPembayaran akan otomatis terkonfirmasi setelah Anda scan.\nTerima kasih!";
$t->variables = json_encode([
    'bengkel_name' => 'Nama Bengkel', 
    'customer_name' => 'Nama Pelanggan', 
    'invoice_number' => 'Nomor Invoice', 
    'grand_total' => 'Total Tagihan', 
    'qris_url' => 'URL Gambar QRIS'
]);
$t->is_active = true;
$t->save();
echo "Done\n";
