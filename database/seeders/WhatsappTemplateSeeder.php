<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WhatsappTemplate;

class WhatsappTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Notifikasi Pesanan Diterima',
                'code' => 'diterima',
                'content' => "Halo *{nama}* 👋\n\n*PESANAN ANDA TELAH DITERIMA*\n\nNomor Order:\n*{nomor_order}*\n\nKendaraan:\n{kendaraan}\n\nPlat Nomor:\n{plat_nomor}\n\nTanggal Pesanan:\n{tanggal_pesan}\n\nPesanan Anda telah kami terima dan akan segera diproses oleh tim kami.\n\nKami akan memberikan informasi kembali ketika pesanan mulai dikerjakan.\n\nTerima kasih telah menggunakan layanan *{nama_bengkel}*. 🙏",
                'variables' => ['nama', 'nomor_order', 'kendaraan', 'plat_nomor', 'tanggal_pesan', 'nama_bengkel'],
                'is_active' => true,
            ],
            [
                'name' => 'Notifikasi Pesanan Diproses',
                'code' => 'diproses',
                'content' => "Halo *{nama}* 👋\n\n*PESANAN ANDA SEDANG DIPROSES*\n\nNomor Order:\n*{nomor_order}*\n\nKendaraan:\n{kendaraan}\n\nPlat Nomor:\n{plat_nomor}\n\nLayanan:\n{layanan}\n\nPesanan Anda saat ini sedang dalam proses pengerjaan oleh tim kami.\n\nKami akan memberikan informasi kembali ketika pengerjaan telah selesai.\n\nTerima kasih atas kesabarannya. 🙏\n\n*{nama_bengkel}*",
                'variables' => ['nama', 'nomor_order', 'kendaraan', 'plat_nomor', 'layanan', 'nama_bengkel'],
                'is_active' => true,
            ],
            [
                'name' => 'Notifikasi Pesanan Siap',
                'code' => 'siap_diambil',
                'content' => "Halo *{nama}* 👋\n\n*PESANAN ANDA TELAH SELESAI* 🎉\n\nNomor Order:\n*{nomor_order}*\n\nKendaraan:\n{kendaraan}\n\nPlat Nomor:\n{plat_nomor}\n\nLayanan:\n{layanan}\n\nTotal Tagihan:\n*Rp {total}*\n\nPesanan Anda telah selesai dan siap untuk diambil.\n\nSilakan datang ke *{nama_bengkel}* untuk melakukan pengambilan kendaraan.\n\nTerima kasih telah mempercayakan kendaraan Anda kepada kami. 🙏",
                'variables' => ['nama', 'nomor_order', 'kendaraan', 'plat_nomor', 'layanan', 'total', 'nama_bengkel'],
                'is_active' => true,
            ],
            [
                'name' => 'Kirim QRIS Tagihan',
                'code' => 'qris_tagihan',
                'content' => "Halo *{nama}* 👋\n\n*PEMBAYARAN TAGIHAN SERVICE*\n\nNomor Invoice:\n*{nomor_invoice}*\n\nNomor Order:\n*{nomor_order}*\n\nKendaraan:\n{kendaraan}\n\nTotal Tagihan:\n*Rp {total}*\n\nSilakan melakukan pembayaran menggunakan QRIS yang kami kirimkan pada link berikut:\n{qris_url}\n\nSetelah pembayaran berhasil, mohon simpan bukti pembayaran untuk proses verifikasi.\n\nTerima kasih telah menggunakan layanan *{nama_bengkel}*. 🙏",
                'variables' => ['nama', 'nomor_invoice', 'nomor_order', 'kendaraan', 'total', 'qris_url', 'nama_bengkel'],
                'is_active' => true,
            ]
        ];

        foreach ($templates as $template) {
            WhatsappTemplate::updateOrCreate(
                ['code' => $template['code']],
                $template
            );
        }
    }
}
