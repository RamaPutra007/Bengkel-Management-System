<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Config WhatsApp Bot') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <p class="text-slate-500 text-sm">Pengaturan konfigurasi otomatisasi pengiriman pesan WhatsApp ke pelanggan.</p>
            </div>

            <form action="{{ route('admin.whatsapp.updateConfig') }}" method="POST" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">
                @csrf
                
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl">
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-8">
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight mb-4">Pengaturan Utama</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Fonnte API Key</label>
                            <input type="text" name="api_key" value="{{ $config && $config->api_key ? '****************' : '' }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] transition-colors text-sm font-mono text-slate-500" placeholder="Masukkan API Key Fonnte">
                            <p class="text-xs text-slate-500 mt-2">Dapatkan API Key dari dashboard <a href="https://fonnte.com" target="_blank" class="text-[#FF6B00] hover:underline">fonnte.com</a>.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Nomor Bot WhatsApp</label>
                            <input type="text" name="bot_number" value="{{ $config->bot_number ?? '' }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] transition-colors text-sm text-slate-700" placeholder="Contoh: 08123456789">
                            <p class="text-xs text-slate-500 mt-2">Nomor WhatsApp yang digunakan sebagai Bot.</p>
                        </div>
                    </div>
                </div>

                <div class="mb-8 pt-6 border-t border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight mb-4">Status Bot</h3>
                    <label class="flex items-center gap-3 p-4 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors max-w-sm">
                        <input type="checkbox" name="is_active" value="1" {{ ($config->is_active ?? false) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-[#FF6B00] focus:ring-[#FF6B00] transition-colors">
                        <div>
                            <p class="text-sm font-bold text-slate-800">Aktifkan Bot WhatsApp</p>
                            <p class="text-xs text-slate-500 mt-0.5">Sistem akan mengirimkan pesan notifikasi secara otomatis jika diaktifkan.</p>
                        </div>
                    </label>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-[#FF6B00] hover:bg-orange-600 text-white font-medium text-sm rounded-xl transition-colors shadow-sm shadow-orange-500/20">
                        Simpan Konfigurasi
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
