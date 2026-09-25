<?php

namespace App\Services;

class QrisService
{
    /**
     * Mengubah QRIS Statis menjadi Dinamis dengan nominal tertentu
     */
    public static function generateDynamicQris(string $staticQris, int $amount): string
    {
        // Pastikan format valid (cek 6304 di akhir)
        if (substr($staticQris, -8, 4) !== '6304') {
            return $staticQris; // Return as is jika format tidak dikenali
        }

        // Hapus CRC dan Tag 63 (8 karakter terakhir)
        $base = substr($staticQris, 0, -8);
        
        // Ubah Point of Initiation Method (Tag 01) dari Statis (11) ke Dinamis (12)
        $base = str_replace('010211', '010212', $base);

        // Siapkan Tag 54 (Transaction Amount)
        $amountStr = (string)$amount;
        $amountLen = str_pad((string)strlen($amountStr), 2, '0', STR_PAD_LEFT);
        $tag54 = '54' . $amountLen . $amountStr;

        // Sisipkan Tag 54 sebelum Tag 58 (Country Code)
        $pos58 = strpos($base, '5802');
        if ($pos58 !== false) {
            $base = substr_replace($base, $tag54, $pos58, 0);
        } else {
            $base .= $tag54; // Jika tidak ada, tambahkan di akhir
        }

        // Tambahkan kembali Tag 6304 untuk menghitung CRC
        $base .= '6304';

        // Hitung ulang CRC
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($base); $i++) {
            $crc ^= (ord($base[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = ($crc << 1) ^ 0x1021;
                } else {
                    $crc <<= 1;
                }
            }
        }
        $crc &= 0xFFFF;
        
        $crcHex = strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));

        return $base . $crcHex;
    }
}
