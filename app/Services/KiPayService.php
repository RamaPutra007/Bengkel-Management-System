<?php

namespace App\Services;

use App\Models\KiPayConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class KiPayService
{
    /**
     * Dapatkan konfigurasi KiPay yang aktif.
     */
    public function getConfig(): ?KiPayConfig
    {
        try {
            return KiPayConfig::first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Dapatkan API Key (dari database atau fallback ke config/env).
     */
    public function getApiKey(): ?string
    {
        $config = $this->getConfig();
        if ($config && !empty($config->api_key)) {
            try {
                $decrypted = Crypt::decryptString($config->api_key);
            } catch (\Exception $e) {
                $decrypted = $config->api_key;
            }

            if (!empty($decrypted) && !str_contains($decrypted, '*')) {
                return trim($decrypted);
            }
        }

        $envKey = config('kipay.api_key');
        if (!empty($envKey) && !str_contains($envKey, '*')) {
            return trim($envKey);
        }

        return null;
    }

    /**
     * Dapatkan Webhook Secret (dari database atau fallback ke config/env).
     */
    public function getWebhookSecret(): ?string
    {
        $config = $this->getConfig();
        if ($config && !empty($config->webhook_secret)) {
            try {
                $decrypted = Crypt::decryptString($config->webhook_secret);
            } catch (\Exception $e) {
                $decrypted = $config->webhook_secret;
            }

            if (!empty($decrypted) && !str_contains($decrypted, '*')) {
                return trim($decrypted);
            }
        }

        $envSecret = config('kipay.webhook_secret');
        if (!empty($envSecret) && !str_contains($envSecret, '*')) {
            return trim($envSecret);
        }

        return null;
    }

    /**
     * Dapatkan Mode (sandbox atau production).
     */
    public function getMode(): string
    {
        $config = $this->getConfig();
        if ($config && !empty($config->mode)) {
            return $config->mode;
        }

        return config('kipay.mode', 'sandbox');
    }

    /**
     * Dapatkan Base URL KiPay API.
     */
    public function getBaseUrl(): string
    {
        return rtrim(config('kipay.base_url', 'https://api.kipay.id'), '/');
    }

    /**
     * Cek apakah KiPay sudah dikonfigurasi.
     */
    public function isConfigured(): bool
    {
        $key = $this->getApiKey();
        return !empty($key);
    }

    /**
     * Buat Transaksi QRIS baru di KiPay.
     *
     * @param int $amount Nominal transaksi dalam Rupiah
     * @param string $note Referensi unik / Nomor Invoice (contoh: INV-20261104-0001)
     * @return array
     */
    public function createTransaction(int $amount, string $note): array
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) {
            return [
                'success' => false,
                'message' => 'API Key KiPay belum dikonfigurasi. Silakan atur di menu Integrasi KiPay.',
            ];
        }

        if ($amount < 1000) {
            return [
                'success' => false,
                'status' => 400,
                'message' => 'Nominal tagihan (Rp ' . number_format($amount, 0, ',', '.') . ') di bawah batas minimum QRIS KiPay (minimal Rp 1.000).',
            ];
        }

        if ($amount > 100000000) {
            return [
                'success' => false,
                'status' => 400,
                'message' => 'Nominal tagihan (Rp ' . number_format($amount, 0, ',', '.') . ') di atas batas maksimum QRIS KiPay (maksimal Rp 100.000.000).',
            ];
        }

        $url = "{$this->getBaseUrl()}/api/pay/{$apiKey}/transactions";

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($url, [
                    'amount' => $amount,
                    'note' => $note,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('KiPay createTransaction failed:', [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => $url,
            ]);

            $errorMsg = $response->json('error') ?? $response->json('message') ?? $response->json('detail');
            if ($response->status() === 404 && (empty($errorMsg) || str_contains($errorMsg, 'Project tidak ditemukan'))) {
                $errorMsg = 'Project KiPay tidak ditemukan (HTTP 404). Pastikan API Key di menu Pengaturan KiPay sesuai dengan dashboard KiPay.';
            }

            return [
                'success' => false,
                'status' => $response->status(),
                'message' => $errorMsg ?? 'Gagal membuat transaksi di KiPay (HTTP ' . $response->status() . ')',
                'body' => $response->json() ?? $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('KiPay createTransaction exception:', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Koneksi ke server KiPay gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Cek status transaksi dari KiPay berdasarkan trx_id.
     *
     * @param string $trxId
     * @return array
     */
    public function getTransaction(string $trxId): array
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) {
            return [
                'success' => false,
                'message' => 'API Key KiPay belum dikonfigurasi.',
            ];
        }

        $url = "{$this->getBaseUrl()}/api/pay/{$apiKey}/transactions/{$trxId}";

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'status' => $response->status(),
                'message' => $response->json('message') ?? 'Transaksi tidak ditemukan di KiPay.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Koneksi ke server KiPay gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Ambil stream / binary gambar QRIS dari KiPay.
     *
     * @param string $trxId
     * @return array
     */
    public function getQrImage(string $trxId): array
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) {
            return [
                'success' => false,
                'message' => 'API Key KiPay belum dikonfigurasi.',
            ];
        }

        $url = "{$this->getBaseUrl()}/api/pay/{$apiKey}/transactions/{$trxId}/qr.png";

        try {
            $response = Http::timeout(15)->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'content_type' => $response->header('Content-Type') ?: 'image/png',
                    'body' => $response->body(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal mengambil gambar QR dari KiPay.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Koneksi ke KiPay gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Jalankan simulasi pembayaran (Khusus mode Sandbox).
     *
     * @param string $trxId
     * @param string $provider
     * @return array
     */
    public function simulatePayment(string $trxId, string $provider = 'shopeepay'): array
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) {
            return [
                'success' => false,
                'message' => 'API Key KiPay belum dikonfigurasi.',
            ];
        }

        $url = "{$this->getBaseUrl()}/api/pay/{$apiKey}/transactions/{$trxId}/simulate";

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($url, [
                    'provider' => $provider,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'status' => $response->status(),
                'message' => $response->json('message') ?? 'Gagal menjalankan simulasi pembayaran KiPay.',
                'body' => $response->json(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Koneksi ke KiPay gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verifikasi signature webhook dari KiPay.
     * Kontrak KiPay:
     * X-Webhook-Signature: sha256=<hex>
     * String to sign: "{timestamp}.{deliveryId}.{rawBody}"
     * Key: webhook_secret
     */
    public function verifyWebhookSignature(string $rawBody, ?string $signatureHeader, ?string $timestamp, ?string $deliveryId): bool
    {
        $secret = $this->getWebhookSecret();

        // Jika secret belum diset di server atau environment lokal, lewati verifikasi ketat
        if (empty($secret)) {
            Log::warning('KiPay Webhook: Webhook secret belum diatur. Melewati verifikasi signature HMAC.');
            return true;
        }

        if (empty($signatureHeader)) {
            Log::warning('KiPay Webhook: Header X-Webhook-Signature tidak ada.');
            return false;
        }

        // 1. Verifikasi replay tolerance (maksimal 5 menit)
        if (!app()->environment('testing') && !empty($timestamp)) {
            $parsedTime = strtotime($timestamp);
            if ($parsedTime !== false && abs(time() - $parsedTime) > 300) {
                Log::warning('KiPay Webhook: Timestamp kadaluarsa (> 5 menit), potensi replay attack.', [
                    'timestamp' => $timestamp,
                    'current_time' => date('c'),
                ]);
                return false;
            }
        }

        // Ekstrak hash hex dari format 'sha256=<hex>' jika ada prefix
        $actualSignature = $signatureHeader;
        if (str_starts_with($signatureHeader, 'sha256=')) {
            $actualSignature = substr($signatureHeader, 7);
        }

        $payloadToSign = "{$timestamp}.{$deliveryId}.{$rawBody}";
        $expectedSignature = hash_hmac('sha256', $payloadToSign, $secret);

        $isValid = hash_equals($expectedSignature, $actualSignature);

        if (!$isValid) {
            // Cek juga fallback tanpa timestamp jika dikirim dalam format rawBody saja
            $expectedFallback = hash_hmac('sha256', $rawBody, $secret);
            if (hash_equals($expectedFallback, $actualSignature)) {
                return true;
            }

            Log::warning('KiPay Webhook: Signature tidak cocok.', [
                'received' => $actualSignature,
                'expected' => $expectedSignature,
            ]);
        }

        return $isValid;
    }
}
