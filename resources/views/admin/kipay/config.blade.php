<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    {{ __('Konfigurasi Payment Gateway KiPay') }}
                </h2>
            </div>
            <div>
                <a href="https://kipay.id" target="_blank" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl inline-flex items-center gap-1.5 transition-colors">
                    <span>Buka Portal KiPay</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Banner Intro -->
            <div class="p-6 bg-gradient-to-r from-orange-500/10 via-amber-500/5 to-transparent border border-orange-200 rounded-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-[#FF6B00] flex items-center justify-center text-white font-black text-xl shrink-0 shadow-sm shadow-orange-500/30">
                        KP
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            KiPay Digital Payment Gateway (QRIS)
                            @if($isConfigured)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    {{ strtoupper($mode) }} AKTIF
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    BELUM DIKONFIGURASI
                                </span>
                            @endif
                        </h3>
                        <p class="text-sm text-slate-600 mt-0.5">
                            Terima pembayaran QRIS otomatis, verifikasi lunas seketika melalui webhook real-time, dan pantau rekonsiliasi pembayaran bengkel tanpa input manual.
                        </p>
                    </div>
                </div>

                @if($isConfigured)
                    <button type="button" id="btn_test_conn" onclick="testKiPayConnection()" class="px-4 py-2.5 bg-white border border-slate-200 hover:border-[#FF6B00] hover:text-[#FF6B00] text-slate-700 font-semibold text-xs rounded-xl shadow-sm transition-all flex items-center gap-2 shrink-0">
                        <svg class="w-4 h-4 text-[#FF6B00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span>Tes Koneksi API</span>
                    </button>
                @endif
            </div>

            <!-- Form Pengaturan -->
            <form action="{{ route('admin.kipay.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">
                @csrf
                
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <div id="test_conn_alert" class="hidden mb-6 p-4 rounded-xl text-sm font-medium"></div>

                <!-- Kredensial API -->
                <div class="mb-8">
                    <h3 class="text-base font-bold text-slate-800 tracking-tight mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold">1</span>
                        Kredensial API KiPay
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-slate-700">
                                    API Key Project <span class="text-red-500">*</span>
                                </label>
                                @if($isConfigured)
                                    <span class="inline-flex items-center gap-1 text-xs text-emerald-600 font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Tersimpan
                                    </span>
                                @endif
                            </div>
                            <input type="text" name="api_key" autocomplete="off" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] transition-colors text-sm font-mono text-slate-700" placeholder="{{ $isConfigured ? '•••••••••••••••• (Tersimpan - Kosongkan jika tidak diubah)' : 'Masukkan API Key (contoh: qpg_pub_...)' }}">
                            <p class="text-xs text-slate-500 mt-2">
                                Dapatkan API Key di <a href="https://kipay.id/login" target="_blank" class="text-[#FF6B00] hover:underline font-semibold">Dashboard KiPay</a> &rarr; Pilih Project Sandbox / Production &rarr; Copy API Key.
                            </p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-slate-700">
                                    Webhook Secret (Opsional)
                                </label>
                                @if($config && $config->webhook_secret)
                                    <span class="inline-flex items-center gap-1 text-xs text-emerald-600 font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Tersimpan
                                    </span>
                                @endif
                            </div>
                            <input type="text" name="webhook_secret" autocomplete="off" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] transition-colors text-sm font-mono text-slate-700" placeholder="{{ $config && $config->webhook_secret ? '•••••••••••••••• (Tersimpan - Kosongkan jika tidak diubah)' : 'whsec_... (dari dashboard KiPay)' }}">
                            <p class="text-xs text-slate-500 mt-2">
                                Digunakan untuk validasi keamanan HMAC-SHA256 agar backend hanya menerima sinyal resmi dari KiPay.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Mode Transaksi -->
                <div class="mb-8 pt-6 border-t border-slate-100">
                    <h3 class="text-base font-bold text-slate-800 tracking-tight mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold">2</span>
                        Mode Lingkungan (Environment)
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl">
                        <label class="flex items-start gap-3 p-4 border rounded-xl hover:bg-slate-50 cursor-pointer transition-colors {{ $mode === 'sandbox' ? 'border-[#FF6B00] bg-orange-50/40' : 'border-slate-200' }}">
                            <input type="radio" name="mode" value="sandbox" {{ $mode === 'sandbox' ? 'checked' : '' }} class="mt-1 text-[#FF6B00] focus:ring-[#FF6B00]">
                            <div>
                                <p class="text-sm font-bold text-slate-800">Sandbox (Uji Coba)</p>
                                <p class="text-xs text-slate-500 mt-0.5">Simulasi pembayaran gratis tanpa potongan uang nyata. Cocok untuk uji alur QRIS dan webhook.</p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-4 border rounded-xl hover:bg-slate-50 cursor-pointer transition-colors {{ $mode === 'production' ? 'border-[#FF6B00] bg-orange-50/40' : 'border-slate-200' }}">
                            <input type="radio" name="mode" value="production" {{ $mode === 'production' ? 'checked' : '' }} class="mt-1 text-[#FF6B00] focus:ring-[#FF6B00]">
                            <div>
                                <p class="text-sm font-bold text-slate-800">Production (Live)</p>
                                <p class="text-xs text-slate-500 mt-0.5">Memproses dana riil dari e-wallet (GoPay, OVO, ShopeePay, Dana, BCA, Mandiri, dll.).</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Webhook URL Info -->
                <div class="mb-8 pt-6 border-t border-slate-100">
                    <h3 class="text-base font-bold text-slate-800 tracking-tight mb-2 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold">3</span>
                        Webhook Endpoint URL
                    </h3>
                    <p class="text-xs text-slate-500 mb-3">
                        Salin URL ini dan masukkan ke pengaturan <strong>Webhook URL</strong> pada project di dashboard KiPay Anda:
                    </p>
                    
                    <div class="flex items-center gap-2 max-w-2xl">
                        <input type="text" readonly id="webhook_url_input" value="{{ $webhookUrl }}" class="flex-1 px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-mono text-slate-700 focus:outline-none">
                        <button type="button" onclick="copyWebhookUrl()" id="copy_btn" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium text-xs rounded-xl transition-colors shrink-0">
                            Salin URL
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">
                        KiPay akan otomatis mengirim event <code>transaction.paid</code> ke endpoint ini ketika pelanggan selesai melakukan scan QRIS.
                    </p>
                </div>

                <!-- Status Aktif -->
                <div class="mb-8 pt-6 border-t border-slate-100">
                    <label class="flex items-center gap-3 p-4 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors max-w-sm">
                        <input type="checkbox" name="is_active" value="1" {{ ($config->is_active ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-[#FF6B00] focus:ring-[#FF6B00] transition-colors">
                        <div>
                            <p class="text-sm font-bold text-slate-800">Aktifkan KiPay Gateway</p>
                            <p class="text-xs text-slate-500 mt-0.5">Aktifkan integrasi QRIS dinamis otomatis pada pembuatan invoice.</p>
                        </div>
                    </label>
                </div>

                <div class="pt-6 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-[#FF6B00] hover:bg-orange-600 text-white font-bold text-sm rounded-xl transition-colors shadow-sm shadow-orange-500/20">
                        Simpan Konfigurasi KiPay
                    </button>
                </div>
            </form>

        </div>
    </div>

    <x-slot name="scripts">
        <script>
            function copyWebhookUrl() {
                const input = document.getElementById('webhook_url_input');
                navigator.clipboard.writeText(input.value).then(() => {
                    const btn = document.getElementById('copy_btn');
                    const orig = btn.innerText;
                    btn.innerText = 'Tersalin!';
                    btn.classList.add('bg-emerald-600');
                    setTimeout(() => {
                        btn.innerText = orig;
                        btn.classList.remove('bg-emerald-600');
                    }, 2000);
                });
            }

            function testKiPayConnection() {
                const alertBox = document.getElementById('test_conn_alert');
                const btn = document.getElementById('btn_test_conn');
                
                alertBox.className = 'mb-6 p-4 rounded-xl text-sm font-medium bg-blue-50 border border-blue-200 text-blue-800 flex items-center gap-2';
                alertBox.innerHTML = '<svg class="animate-spin h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> <span>Sedang menghubungi KiPay API...</span>';
                alertBox.classList.remove('hidden');

                fetch("{{ route('admin.kipay.test-connection') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alertBox.className = 'mb-6 p-4 rounded-xl text-sm font-medium bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-2';
                        alertBox.innerHTML = '<svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> <span>' + data.message + '</span>';
                    } else {
                        alertBox.className = 'mb-6 p-4 rounded-xl text-sm font-medium bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-2';
                        alertBox.innerHTML = '<svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> <span>' + data.message + '</span>';
                    }
                })
                .catch(err => {
                    alertBox.className = 'mb-6 p-4 rounded-xl text-sm font-medium bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-2';
                    alertBox.innerHTML = 'Gagal menghubungi server lokal: ' + err.message;
                });
            }
        </script>
    </x-slot>
</x-app-layout>

