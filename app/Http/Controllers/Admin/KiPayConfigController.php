<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KiPayConfig;
use App\Services\KiPayService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class KiPayConfigController extends Controller
{
    protected $kiPayService;

    public function __construct(KiPayService $kiPayService)
    {
        $this->kiPayService = $kiPayService;
    }

    public function index()
    {
        $config = KiPayConfig::first();
        $webhookUrl = url('/api/webhook/kipay');
        $apiKey = $this->kiPayService->getApiKey();
        $isConfigured = !empty($apiKey);
        $mode = $this->kiPayService->getMode();

        return view('admin.kipay.config', compact('config', 'webhookUrl', 'apiKey', 'isConfigured', 'mode'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'api_key' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'mode' => 'required|in:sandbox,production',
            'is_active' => 'nullable|boolean',
        ]);

        $config = KiPayConfig::first();

        $data = [
            'mode' => $request->input('mode', 'sandbox'),
            'is_active' => $request->has('is_active') ? true : false,
        ];

        if ($request->filled('api_key')) {
            $data['api_key'] = Crypt::encryptString(trim($request->input('api_key')));
        } elseif ($request->has('clear_api_key')) {
            $data['api_key'] = null;
        }

        if ($request->filled('webhook_secret')) {
            $data['webhook_secret'] = Crypt::encryptString(trim($request->input('webhook_secret')));
        } elseif ($request->has('clear_webhook_secret')) {
            $data['webhook_secret'] = null;
        }

        if ($config) {
            $config->update($data);
        } else {
            KiPayConfig::create($data);
        }

        return redirect()->route('admin.kipay.index')->with('success', 'Konfigurasi KiPay Payment Gateway berhasil disimpan.');
    }

    public function testConnection()
    {
        $apiKey = $this->kiPayService->getApiKey();

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Key belum diatur. Masukkan API Key terlebih dahulu.',
            ], 422);
        }

        $baseUrl = $this->kiPayService->getBaseUrl();

        try {
            // Cek endpoint transactions dengan invalid trx_id untuk memvalidasi otorisasi key
            $response = Http::timeout(10)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->get("{$baseUrl}/api/pay/{$apiKey}/transactions/PING-CHECK");

            // Jika status 404 (transaksi tidak ditemukan), berarti API Key valid dan diakui server KiPay!
            // Jika 401/403/Forbidden, API Key salah
            if ($response->status() === 404 || $response->status() === 400 || $response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Koneksi ke KiPay API berhasil! API Key valid dan terhubung.',
                ]);
            }

            if ($response->status() === 401 || $response->status() === 403) {
                return response()->json([
                    'success' => false,
                    'message' => 'API Key KiPay ditolak oleh server (Unauthorized/Forbidden). Periksa kembali API Key Anda.',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Server KiPay merespons status HTTP: ' . $response->status(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi KiPay API: ' . $e->getMessage(),
            ], 500);
        }
    }
}

