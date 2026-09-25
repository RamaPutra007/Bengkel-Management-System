<?php

namespace App\Services;

use App\Models\WhatsappConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

class FonnteService
{
    protected $apiUrl = 'https://api.fonnte.com';

    public function getToken()
    {
        $config = WhatsappConfig::first();
        if (!$config || !$config->api_key) {
            return config('services.fonnte.api_key');
        }
        
        try {
            return Crypt::decryptString($config->api_key);
        } catch (\Exception $e) {
            return $config->api_key;
        }
    }

    public function isEnabled()
    {
        $config = WhatsappConfig::first();
        return $config && $config->is_active;
    }

    public function getDeviceInfo()
    {
        $token = $this->getToken();
        if (!$token) {
            return [
                'status' => false,
                'message' => 'Token API belum dikonfigurasi.'
            ];
        }

        try {
            $response = Http::timeout(15)->withHeaders([
                'Authorization' => $token
            ])->post($this->apiUrl . '/device');

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => false,
                'message' => 'Gagal mengambil informasi device.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Koneksi ke server Fonnte gagal: ' . $e->getMessage()
            ];
        }
    }

    public function getStatus()
    {
        $info = $this->getDeviceInfo();
        if (isset($info['device_status'])) {
            return $info['device_status'] === 'connect';
        }
        return false;
    }

    public function getQrCode()
    {
        $token = $this->getToken();
        if (!$token) return ['status' => false, 'message' => 'Token belum dikonfigurasi.'];
        
        $config = WhatsappConfig::first();

        try {
            $response = Http::timeout(15)->withHeaders([
                'Authorization' => $token
            ])->post($this->apiUrl . '/qr', [
                'type' => 'qr',
                'whatsapp' => $config ? $config->bot_number : ''
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['status' => false, 'message' => 'Gagal mendapatkan QR Code.'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Koneksi ke server Fonnte gagal.'];
        }
    }

    public function disconnect()
    {
        $token = $this->getToken();
        if (!$token) return ['status' => false, 'message' => 'Token belum dikonfigurasi.'];

        try {
            $response = Http::timeout(15)->withHeaders([
                'Authorization' => $token
            ])->post($this->apiUrl . '/disconnect');

            if ($response->successful()) {
                return $response->json();
            }

            return ['status' => false, 'message' => 'Gagal memutuskan koneksi.'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Koneksi ke server Fonnte gagal.'];
        }
    }

    public function sendMessage($target, $message)
    {
        if (!$this->isEnabled()) {
            return ['status' => false, 'message' => 'WhatsApp Bot sedang dinonaktifkan.'];
        }

        $token = $this->getToken();
        if (!$token) {
            return ['status' => false, 'message' => 'Token API belum dikonfigurasi.'];
        }

        try {
            $response = Http::timeout(15)->withHeaders([
                'Authorization' => $token
            ])->post($this->apiUrl . '/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Koneksi ke server Fonnte gagal.'];
        }
    }

    public function sendTemplateMessage($target, $templateCode, $variables = [], $fileUrl = null)
    {
        $templateService = app(\App\Services\WhatsAppTemplateService::class);
        $message = $templateService->render($templateCode, $variables);
        
        if (!$message) {
            return ['status' => false, 'message' => 'Template tidak ditemukan atau tidak aktif.'];
        }

        if ($fileUrl) {

            // Attach file as message caption
            return $this->sendFile($target, $fileUrl, $message);
        }

        return $this->sendMessage($target, $message);
    }
    
    public function sendFile($target, $fileUrl, $message = '')
    {
        if (!$this->isEnabled()) {
            return ['status' => false, 'message' => 'WhatsApp Bot sedang dinonaktifkan.'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->getToken(),
            ])->post($this->apiUrl . '/send', [
                'target' => $target,
                'message' => $message,
                'url' => $fileUrl,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            \Log::error('Fonnte Send File Error: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
