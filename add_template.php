<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$template = \App\Models\WhatsappTemplate::firstOrCreate(
    ['code' => 'booking_baru'],
    [
        'name' => 'Notifikasi Booking Baru (Internal)',
        'content' => "🔔 *BOOKING BARU MASUK!*\n\nPelanggan: {nama} ({telepon})\nKendaraan: {kendaraan} ({plat_nomor})\nJadwal: {jadwal}\nKeluhan:\n{keluhan}\n\nSilakan cek Dasbor Admin untuk memproses booking ini.",
        'variables' => ['nama', 'telepon', 'kendaraan', 'plat_nomor', 'jadwal', 'keluhan'],
        'is_active' => true
    ]
);

echo "Template created: " . $template->id . "\n";
