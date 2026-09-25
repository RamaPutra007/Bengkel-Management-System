<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$template = \App\Models\WhatsappTemplate::firstOrCreate(
    ['code' => 'laporan_inspeksi'],
    [
        'name' => 'Laporan Inspeksi Kendaraan',
        'content' => "Halo *{nama}* 👋\n\nMekanik kami telah selesai melakukan inspeksi pada kendaraan Anda ({kendaraan} - {plat_nomor}).\n\n*Catatan Mekanik:*\n{catatan_mekanik}\n\n*Rekomendasi Perbaikan:*\n{rekomendasi}\n\nSilakan hubungi admin kami untuk menyetujui langkah perbaikan selanjutnya.\n\nTerima kasih,\n*{nama_bengkel}*",
        'variables' => ['nama', 'kendaraan', 'plat_nomor', 'catatan_mekanik', 'rekomendasi', 'nama_bengkel'],
        'is_active' => true
    ]
);

echo "Template created: " . $template->id . "\n";
