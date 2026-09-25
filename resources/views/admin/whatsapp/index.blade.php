<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Koneksi WhatsApp Bot') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">


            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left Card (Status & QR) -->
                <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="text-slate-600 font-medium">Status:</span>
                        @if($isConnected)
                            <span class="px-3 py-1 bg-green-50 text-green-600 text-xs font-bold rounded-full flex items-center gap-1.5 border border-green-100">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                Terhubung
                            </span>
                        @else
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-full flex items-center gap-1.5 border border-slate-200">
                                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                                Tidak Terhubung
                            </span>
                        @endif
                    </div>

                    @if($isConnected)
                        <!-- Connection Status Box -->
                        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-8 text-center flex flex-col items-center justify-center flex-1">
                            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-5 shadow-sm">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800 mb-3">WhatsApp Terhubung!</h3>
                            <p class="text-sm text-slate-500 mb-8 max-w-[260px] leading-relaxed mx-auto">
                                Bot WhatsApp sudah aktif dan siap digunakan untuk mengirim notifikasi ke pelanggan.
                            </p>
                            
                            <form action="{{ route('admin.whatsapp.disconnect') }}" method="POST" class="w-full mt-auto" onsubmit="return confirm('Putuskan koneksi WhatsApp?\n\nWhatsApp Bot tidak akan dapat mengirim notifikasi kepada pelanggan setelah koneksi diputus.')">
                                @csrf
                                <button type="submit" class="w-full py-2.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-sm font-medium rounded-xl transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Putuskan Koneksi
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- QR Code Box -->
                        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-8 text-center flex flex-col items-center justify-center flex-1">
                            <h3 class="text-lg font-bold text-slate-800 mb-2">Hubungkan WhatsApp</h3>
                            <p class="text-sm text-slate-500 mb-6 leading-relaxed">
                                Scan QR Code menggunakan WhatsApp untuk menghubungkan perangkat.
                            </p>
                            
                            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-6 flex items-center justify-center min-h-[200px]">
                                @if($qrCodeUrl)
                                    <img src="data:image/png;base64,{{ $qrCodeUrl }}" alt="WhatsApp QR Code" class="w-48 h-48">
                                @else
                                    <div class="text-slate-400 text-sm flex flex-col items-center">
                                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        Gagal memuat QR Code.
                                        <br>Periksa API Key di Config.
                                    </div>
                                @endif
                            </div>
                            
                            <a href="{{ route('admin.whatsapp.index') }}" class="w-full py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 text-sm font-medium rounded-xl transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Refresh QR Code
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Right Card (Device Info) -->
                <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8 flex flex-col">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-slate-800 tracking-tight">Informasi Device API</h3>
                        <a href="{{ route('admin.whatsapp.index') }}" class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 text-xs font-semibold rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Refresh Info
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Info Block -->
                        <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl">
                            <p class="text-xs text-slate-500 font-medium mb-1 uppercase tracking-wider">Nomor WhatsApp</p>
                            <p class="text-base font-bold text-slate-800">{{ $deviceInfo['device'] ?? '-' }}</p>
                        </div>
                        
                        <!-- Info Block -->
                        <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl flex flex-col justify-center items-start">
                            <p class="text-xs text-slate-500 font-medium mb-2 uppercase tracking-wider">Status Device</p>
                            @if($isConnected)
                            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full inline-flex items-center gap-1.5 border border-green-200">
                                <span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span>
                                Terhubung
                            </span>
                            @else
                            <span class="px-3 py-1 bg-slate-200 text-slate-700 text-xs font-bold rounded-full inline-flex items-center gap-1.5 border border-slate-300">
                                <span class="w-1.5 h-1.5 bg-slate-500 rounded-full"></span>
                                Disconnect
                            </span>
                            @endif
                        </div>

                        <!-- Info Block -->
                        <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl">
                            <p class="text-xs text-slate-500 font-medium mb-1 uppercase tracking-wider">Nama Device</p>
                            <p class="text-base font-bold text-slate-800 tracking-wide">{{ $deviceInfo['name'] ?? '-' }}</p>
                        </div>

                        <!-- Info Block -->
                        <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl">
                            <p class="text-xs text-slate-500 font-medium mb-1 uppercase tracking-wider">Paket Fonnte</p>
                            <p class="text-base font-bold text-[#FF6B00]">{{ $deviceInfo['package'] ?? '-' }}</p>
                        </div>

                        <!-- Info Block -->
                        <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl">
                            <p class="text-xs text-slate-500 font-medium mb-1 uppercase tracking-wider">Sisa Kuota Pesan</p>
                            <div class="flex items-end gap-2">
                                <p class="text-2xl font-bold text-slate-800 leading-none">{{ $deviceInfo['quota'] ?? 0 }}</p>
                                <p class="text-xs text-slate-400 font-medium mb-0.5">pesan</p>
                            </div>
                        </div>

                        <!-- Info Block -->
                        <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl">
                            <p class="text-xs text-slate-500 font-medium mb-1 uppercase tracking-wider">Total Terkirim</p>
                            <div class="flex items-end gap-2">
                                <p class="text-2xl font-bold text-slate-800 leading-none">{{ $deviceInfo['messages'] ?? 0 }}</p>
                                <p class="text-xs text-slate-400 font-medium mb-0.5">pesan</p>
                            </div>
                        </div>
                    </div>

                    <!-- Wide Info Block -->
                    <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl mt-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-slate-500 font-medium mb-1 uppercase tracking-wider">Masa Aktif Paket (Expired)</p>
                                <p class="text-base font-bold text-slate-800">{{ $deviceInfo['expired'] ?? '-' }}</p>
                            </div>
                            <div class="text-right">
                                <a href="https://fonnte.com/" target="_blank" class="text-xs font-semibold text-[#FF6B00] hover:text-orange-700 bg-orange-50 px-3 py-1.5 rounded-lg transition-colors">
                                    Upgrade Paket
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            
            <div class="mt-8 bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-slate-800">Pengaturan Fonnte API Token</h4>
                        <p class="text-sm text-slate-500 mt-1">Perbarui token API dari akun Fonnte Anda jika terjadi masalah koneksi.</p>
                    </div>
                    <a href="{{ route('admin.whatsapp.config') }}" class="px-5 py-2.5 bg-[#FF6B00] hover:bg-orange-600 text-white font-medium text-sm rounded-xl transition-colors shadow-sm shadow-orange-500/20">
                        Ganti Token API
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
