<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\FonnteService;
use App\Models\WhatsappConfig;
use App\Models\WhatsappTemplate;
use Illuminate\Support\Facades\Crypt;

class WhatsappController extends Controller
{
    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    public function index()
    {
        $deviceInfo = $this->fonnteService->getDeviceInfo();
        $isConnected = false;
        
        if (isset($deviceInfo['device_status']) && $deviceInfo['device_status'] === 'connect') {
            $isConnected = true;
        }

        $qrCodeUrl = null;
        if (!$isConnected) {
            $qrResponse = $this->fonnteService->getQrCode();
            if (isset($qrResponse['url'])) {
                $qrCodeUrl = $qrResponse['url'];
            }
        }

        return view('admin.whatsapp.index', compact('deviceInfo', 'isConnected', 'qrCodeUrl'));
    }

    public function disconnect()
    {
        $response = $this->fonnteService->disconnect();
        if (isset($response['status']) && $response['status']) {
            return back()->with('success', 'Berhasil memutuskan koneksi WhatsApp.');
        }
        return back()->with('error', $response['message'] ?? 'Gagal memutuskan koneksi WhatsApp.');
    }

    public function config()
    {
        $config = WhatsappConfig::first();
        return view('admin.whatsapp.config', compact('config'));
    }

    public function updateConfig(Request $request)
    {
        $request->validate([
            'api_key' => 'nullable|string',
            'bot_number' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $config = WhatsappConfig::first() ?? new WhatsappConfig();
        
        if ($request->filled('api_key') && $request->api_key !== '****************') {
            $config->api_key = Crypt::encryptString($request->api_key);
        }
        
        $config->bot_number = $request->bot_number;
        $config->is_active = $request->has('is_active');
        $config->save();

        return back()->with('success', 'Konfigurasi WhatsApp berhasil disimpan.');
    }

    public function template(Request $request)
    {
        $query = WhatsappTemplate::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $templates = $query->paginate(9)->withQueryString();
        
        return view('admin.whatsapp.template', compact('templates'));
    }

    public function updateTemplate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean'
        ]);

        $template = WhatsappTemplate::findOrFail($id);
        
        // Cek jika user mencoba menggunakan variabel yang tidak dikenal
        $allowedVariables = is_array($template->variables) ? $template->variables : json_decode($template->variables, true) ?? [];
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $request->content, $matches);
        $usedVariables = $matches[1] ?? [];
        
        foreach ($usedVariables as $var) {
            if (!in_array($var, $allowedVariables)) {
                return back()->with('error', "Variabel tidak dikenali: {{$var}}. Harap gunakan variabel yang tersedia.")->withInput();
            }
        }

        $template->name = $request->name;
        $template->content = $request->content;
        $template->is_active = $request->has('is_active');
        $template->save();

        return back()->with('success', 'Template berhasil diperbarui.');
    }
}
