<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.invoice.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    {{ __('Nota / Invoice') }} #{{ $invoice->invoice_number }}
                </h2>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if($invoice->payment_status === 'paid')
                <a href="{{ route('admin.invoice.print', $invoice->id) }}" target="_blank" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700 transition-colors font-medium text-sm flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Nota
                </a>
                @endif
                @if($invoice->payment_status !== 'paid')

                @if(!empty($waDirectUrl))
                <a href="{{ $waDirectUrl }}" target="_blank" rel="noopener noreferrer" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors font-semibold text-sm flex items-center gap-2 shadow-sm" title="Kirim rincian invoice dan QRIS via WhatsApp langsung ke nomor pelanggan">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    <span>Kirim WA</span>
                </a>
                @endif
                
                @if($invoice->payment_method === 'qris' || $invoice->kipay_trx_id)
                <form action="{{ route('admin.invoice.send-qris', $invoice->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 border border-emerald-200 bg-emerald-50 text-emerald-700 rounded-lg hover:bg-emerald-100 transition-colors font-medium text-sm flex items-center gap-2" title="Kirim otomatis melalui bot WhatsApp">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Bot WA
                    </button>
                </form>

                @if($invoice->kipay_trx_id)
                <a href="{{ route('admin.invoice.check-kipay', $invoice->id) }}" class="px-4 py-2 border border-sky-200 bg-sky-50 text-sky-700 rounded-lg hover:bg-sky-100 transition-colors font-medium text-sm flex items-center gap-2" title="Cek apakah pembayaran sudah masuk di sistem KiPay">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Cek KiPay
                </a>

                @if(($kiPayMode ?? 'sandbox') === 'sandbox')
                <form action="{{ route('admin.invoice.simulate-kipay', $invoice->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 border border-orange-200 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition-colors font-medium text-sm flex items-center gap-2" title="Simulasikan pembayaran sukses via KiPay API Sandbox">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Simulasi KiPay
                    </button>
                </form>
                @endif
                @endif
                @endif
                
                <a href="{{ route('admin.invoice.edit', $invoice->id) }}" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-[#FF6B00] hover:text-white hover:border-[#FF6B00] active:bg-orange-700 active:border-orange-700 transition-all font-medium text-sm">
                    Bayar Manual
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ showQrModal: false, copiedTrx: false }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alerts -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 bg-sky-50 border border-sky-200 text-sky-800 rounded-xl text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- KiPay QRIS Card Section -->
            @if($invoice->payment_method === 'qris' || $invoice->kipay_trx_id)
            <div class="bg-white rounded-3xl border-2 border-orange-200/80 shadow-md p-6 sm:p-8 overflow-hidden relative">
                <div class="flex flex-col lg:flex-row items-center lg:items-start gap-8">
                    
                    <!-- Large QR Frame Section -->
                    <div class="flex flex-col items-center shrink-0">
                        <div class="relative group cursor-pointer" @click="showQrModal = true" title="Klik untuk memperbesar QRIS ke layar penuh">
                            <div class="w-64 h-64 sm:w-72 sm:h-72 p-4 bg-white border-2 border-slate-200 rounded-2xl shadow-sm flex items-center justify-center transition-all duration-200 group-hover:border-[#FF6B00] group-hover:shadow-md">
                                @if($invoice->kipay_trx_id)
                                    <img src="{{ route('admin.invoice.kipay-qr', $invoice->id) }}" alt="QRIS KiPay" class="w-full h-full object-contain rounded-lg">
                                @elseif(!empty($invoice->kipay_qr_payload))
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ urlencode($invoice->kipay_qr_payload) }}" alt="QRIS Dinamis" class="w-full h-full object-contain rounded-lg">
                                @else
                                    <div class="text-center p-4 text-xs text-slate-400">
                                        QRIS belum dibuat
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Overlay Hover Pill -->
                            <div class="absolute inset-0 bg-slate-900/40 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-2 backdrop-blur-[2px]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                <span>Klik Perbesar Layar Penuh</span>
                            </div>
                        </div>

                        <!-- Action Buttons below QR -->
                        <div class="flex flex-wrap items-center justify-center gap-2 mt-3">
                            <button type="button" @click="showQrModal = true" class="px-3 py-1.5 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-colors flex items-center gap-1.5 shadow-sm">
                                <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                <span>Perbesar Layar</span>
                            </button>
                            @if($invoice->kipay_trx_id)
                            <a href="{{ route('admin.invoice.kipay-qr', $invoice->id) }}" target="_blank" class="px-3 py-1.5 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-colors flex items-center gap-1.5 shadow-sm" title="Buka gambar QR 500x500 piksel di tab baru">
                                <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                <span>Buka PNG Asli</span>
                            </a>
                            <a href="{{ route('admin.invoice.kipay-qr', $invoice->id) }}" download="qris-{{ $invoice->invoice_number }}.png" class="px-3 py-1.5 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-colors flex items-center gap-1.5 shadow-sm" title="Unduh gambar QRIS">
                                <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                <span>Unduh</span>
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Details & Operations Section -->
                    <div class="flex-1 w-full space-y-4 text-left">
                        <!-- Badges -->
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-black bg-[#FF6B00] text-white tracking-wide shadow-sm shadow-orange-500/20">
                                KIPAY QRIS RESMI
                            </span>
                            @if($invoice->payment_status === 'paid')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    LUNAS ({{ strtoupper($invoice->kipay_provider ?? 'QRIS') }})
                                </span>
                            @elseif($invoice->kipay_expired_at && $invoice->kipay_expired_at->isPast())
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                    KADALUARSA (EXPIRED)
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                    MENUNGGU PEMBAYARAN
                                </span>
                            @endif
                        </div>

                        <!-- Total Amount -->
                        <div class="p-4 bg-orange-50/70 rounded-2xl border border-orange-100">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Pembayaran QRIS</p>
                            <div class="flex flex-wrap items-baseline gap-2">
                                <span class="text-3xl font-black text-slate-900 tracking-tight">
                                    Rp {{ number_format($invoice->kipay_amount ?: $invoice->grand_total, 0, ',', '.') }}
                                </span>
                                @if($invoice->kipay_amount && $invoice->kipay_amount != $invoice->grand_total)
                                    <span class="text-xs text-orange-700 font-bold bg-orange-100 border border-orange-200 px-2.5 py-0.5 rounded-full">
                                        + kode unik Rp {{ number_format($invoice->kipay_amount - $invoice->grand_total, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Meta Info Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="text-slate-400 block font-medium mb-0.5">KiPay Trx ID:</span>
                                <div class="flex items-center justify-between font-mono font-bold text-slate-800">
                                    <span class="truncate mr-2">{{ $invoice->kipay_trx_id ?? 'Belum terdaftar di KiPay' }}</span>
                                    @if($invoice->kipay_trx_id)
                                    <button type="button" @click="navigator.clipboard.writeText('{{ $invoice->kipay_trx_id }}'); copiedTrx = true; setTimeout(() => copiedTrx = false, 2000)" class="text-slate-400 hover:text-slate-700 shrink-0 transition-colors" title="Salin Trx ID">
                                        <span x-show="!copiedTrx" class="text-xs">📋</span>
                                        <span x-show="copiedTrx" class="text-emerald-600 font-sans font-bold text-[10px]">Tersalin!</span>
                                    </button>
                                    @endif
                                </div>
                            </div>

                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="text-slate-400 block font-medium mb-0.5">Batas Waktu Scan:</span>
                                @if($invoice->kipay_expired_at)
                                    <span class="font-bold {{ $invoice->kipay_expired_at->isPast() ? 'text-rose-600' : 'text-slate-800' }}">
                                        {{ $invoice->kipay_expired_at->format('d M Y, H:i') }} WIB
                                        @if($invoice->kipay_expired_at->isPast() && $invoice->payment_status !== 'paid')
                                            (Kadaluarsa)
                                        @endif
                                    </span>
                                @else
                                    <span class="font-bold text-slate-600">15 Menit sejak dibuat</span>
                                @endif
                            </div>
                        </div>

                        <!-- Direct WhatsApp Send Card / Button -->
                        @if($waDirectUrl)
                        <div class="pt-1">
                            <a href="{{ $waDirectUrl }}" target="_blank" rel="noopener noreferrer" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl text-sm font-bold text-center transition-all flex items-center justify-center gap-2.5 shadow-md shadow-emerald-600/20">
                                <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                <span>Kirim Tagihan & QRIS via WhatsApp (Web / HP)</span>
                            </a>
                            <p class="text-[11px] text-slate-500 text-center mt-1">
                                Kirim langsung dari WhatsApp Anda ke <strong>{{ $waCustomerPhone ?? ($invoice->serviceOrder->customer->phone ?? '-') }}</strong> tanpa perlu koneksi bot Fonnte.
                            </p>
                        </div>
                        @endif

                        <!-- Check Status & Simulate Actions -->
                        @if($invoice->payment_status !== 'paid')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                            @if($invoice->kipay_trx_id)
                                <a href="{{ route('admin.invoice.check-kipay', $invoice->id) }}" class="py-2.5 px-3 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold text-center transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    <span>Cek Status KiPay</span>
                                </a>

                                @if(($kiPayMode ?? 'sandbox') === 'sandbox')
                                    <form action="{{ route('admin.invoice.simulate-kipay', $invoice->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="w-full py-2.5 px-3 bg-[#FF6B00] hover:bg-orange-600 text-white rounded-xl text-xs font-bold text-center transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            <span>Simulasi Bayar</span>
                                        </button>
                                    </form>
                                @endif
                            @else
                                <form action="{{ route('admin.invoice.send-qris', $invoice->id) }}" method="POST" class="inline col-span-2">
                                    @csrf
                                    <button type="submit" class="w-full py-2.5 px-3 bg-[#FF6B00] hover:bg-orange-600 text-white rounded-xl text-xs font-bold text-center transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                                        <span>Buat Transaksi KiPay</span>
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-2 pt-1 text-xs">
                            <form action="{{ route('admin.invoice.send-qris', $invoice->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-slate-500 hover:text-slate-800 underline flex items-center gap-1" title="Kirim ulang notifikasi menggunakan bot otomatis WhatsApp Fonnte">
                                    <span>🤖 Kirim Ulang via Bot WA</span>
                                </button>
                            </form>

                            <a href="{{ route('checkout.show', $invoice->invoice_number) }}" target="_blank" class="text-[#FF6B00] hover:underline font-medium flex items-center gap-1">
                                <span>Halaman Checkout Publik ↗</span>
                            </a>
                        </div>
                        @endif

                        <!-- Bot Offline Warning Helper -->
                        @if(isset($isFonnteConnected) && !$isFonnteConnected)
                        <div class="mt-2 p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-800 flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div>
                                <strong>Status Bot WA:</strong> Bot otomatis Fonnte saat ini terputus (<em>disconnect</em>). Anda tetap dapat mengirimkan tagihan dengan mengklik tombol hijau <strong>Kirim Tagihan & QRIS via WhatsApp</strong> di atas, atau sambungkan kembali bot di <a href="{{ route('admin.whatsapp.index') }}" class="underline font-bold text-amber-900">Integrasi WhatsApp</a>.
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
            @endif

            <!-- Invoice Document Paper -->
            <div class="bg-white overflow-hidden shadow-sm border border-slate-200 printable-area" id="invoice-doc">
                <div class="p-10">
                    
                    <!-- Header Invoice -->
                    <div class="flex flex-col md:flex-row justify-between items-start border-b border-slate-200 pb-8 mb-8">
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-[#FF6B00] rounded-xl flex items-center justify-center text-white font-black text-xl">
                                    B
                                </div>
                                <h1 class="text-2xl font-black text-slate-800 tracking-tight">BengkelPro</h1>
                            </div>
                            <p class="text-slate-500 text-sm">Jl. Contoh Alamat Bengkel No. 123</p>
                            <p class="text-slate-500 text-sm">Kota, Provinsi 12345</p>
                            <p class="text-slate-500 text-sm">Telp: 0812-3456-7890</p>
                        </div>
                        <div class="mt-6 md:mt-0 md:text-right">
                            <h2 class="text-4xl font-black text-slate-200 tracking-wider mb-2">INVOICE</h2>
                            <p class="font-bold text-slate-800 text-lg">{{ $invoice->invoice_number }}</p>
                            <p class="text-slate-500 text-sm mt-1">Tanggal: {{ $invoice->created_at->format('d M Y') }}</p>
                            
                            <div class="mt-4">
                                @if($invoice->payment_status === 'paid')
                                    <span class="inline-block px-4 py-1.5 border-2 border-emerald-500 text-emerald-600 font-black text-lg tracking-widest rounded rotate-[-5deg] bg-white">LUNAS</span>
                                @else
                                    <span class="inline-block px-4 py-1.5 border-2 border-rose-500 text-rose-600 font-black text-lg tracking-widest rounded rotate-[-5deg] bg-white">BELUM LUNAS</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Kepada Yth -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Ditagihkan Kepada</p>
                            <p class="font-bold text-slate-800 text-lg">{{ $invoice->serviceOrder->customer->name ?? '-' }}</p>
                            <p class="text-slate-500 text-sm mt-1">{{ $invoice->serviceOrder->customer->phone ?? '-' }}</p>
                            <p class="text-slate-500 text-sm">{{ $invoice->serviceOrder->customer->email ?? '-' }}</p>
                        </div>
                        <div class="md:text-right">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Informasi Kendaraan</p>
                            <p class="font-bold text-slate-800 text-lg uppercase">{{ $invoice->serviceOrder->vehicle->license_plate ?? '-' }}</p>
                            <p class="text-slate-500 text-sm mt-1">{{ $invoice->serviceOrder->vehicle->brand ?? '' }} {{ $invoice->serviceOrder->vehicle->model ?? '' }}</p>
                            <p class="text-slate-500 text-sm">Ref Order: {{ $invoice->serviceOrder->order_number }}</p>
                        </div>
                    </div>

                    <!-- Item Table -->
                    <div class="mb-8 border border-slate-200 rounded-lg overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-slate-100 border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase">Deskripsi Item</th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase text-right">Harga</th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase text-center w-20">Qty</th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($invoice->serviceOrder->items as $item)
                                    <tr>
                                        <td class="py-4 px-4">
                                            <p class="font-bold text-slate-700">{{ $item->item_name }}</p>
                                            <p class="text-xs text-slate-500 capitalize">{{ $item->type === 'service' ? 'Jasa Servis' : 'Suku Cadang' }}</p>
                                        </td>
                                        <td class="py-4 px-4 text-right text-slate-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="py-4 px-4 text-center font-bold text-slate-700">{{ $item->quantity }}</td>
                                        <td class="py-4 px-4 text-right font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals -->
                    <div class="flex flex-col md:flex-row justify-between items-end gap-8 mb-12">
                        <div class="w-full md:w-1/2">
                            @if($invoice->notes)
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Catatan Tambahan</p>
                                <p class="text-slate-600 text-sm italic">{{ $invoice->notes }}</p>
                            @endif
                            @if($invoice->payment_status === 'paid')
                                <div class="mt-4 p-4 bg-emerald-50 rounded-lg border border-emerald-100 text-sm">
                                    <span class="font-bold text-emerald-800 block mb-1">Informasi Pembayaran:</span>
                                    Metode: <span class="capitalize">{{ $invoice->payment_method }}</span>
                                    @if($invoice->kipay_provider)
                                        ({{ strtoupper($invoice->kipay_provider) }})
                                    @endif
                                    <br>
                                    Tanggal: {{ $invoice->paid_at ? $invoice->paid_at->format('d M Y, H:i') : '-' }}<br>
                                    @if($invoice->kipay_trx_id)
                                        ID KiPay: <span class="font-mono text-xs">{{ $invoice->kipay_trx_id }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="w-full md:w-1/2 md:w-72">
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between text-slate-600">
                                    <span>Subtotal Jasa & Parts:</span>
                                    <span>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                                </div>
                                @php
                                    $taxPercent = $invoice->subtotal > 0 ? round(($invoice->tax / $invoice->subtotal) * 100) : 0;
                                    $discPercent = $invoice->subtotal > 0 ? round(($invoice->discount / $invoice->subtotal) * 100) : 0;
                                @endphp
                                <div class="flex justify-between text-slate-600">
                                    <span>Pajak Tambahan ({{ $taxPercent }}%):</span>
                                    <span>Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-emerald-600">
                                    <span>Potongan Diskon ({{ $discPercent }}%):</span>
                                    <span>- Rp {{ number_format($invoice->discount, 0, ',', '.') }}</span>
                                </div>
                                <div class="pt-3 border-t border-slate-200 flex justify-between items-center mt-2">
                                    <span class="font-black text-slate-800 text-lg">TOTAL TAGIHAN:</span>
                                    <span class="font-black text-[#FF6B00] text-xl">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="text-center pt-8 border-t border-slate-200 text-slate-400 text-sm">
                        <p>Terima kasih telah mempercayakan perawatan kendaraan Anda kepada Si Bengkel.</p>
                        <p>Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan.</p>
                    </div>

                </div>
            </div>
        </div>

        <!-- Fullscreen QRIS Modal for Counter Scan -->
        <div x-show="showQrModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" @keydown.escape.window="showQrModal = false">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="showQrModal = false"></div>

            <div class="flex min-h-screen items-center justify-center p-4 text-center">
                <div class="relative transform overflow-hidden rounded-3xl bg-white p-6 sm:p-8 text-left shadow-2xl transition-all sm:max-w-lg w-full border border-slate-100" @click.stop>
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-[#FF6B00] text-white">
                                KIPAY QRIS RESMI
                            </span>
                            <h3 class="text-xl font-black text-slate-800 mt-1">Pindai QRIS untuk Bayar</h3>
                            <p class="text-xs text-slate-500 font-mono">Invoice: {{ $invoice->invoice_number }}</p>
                        </div>
                        <button type="button" @click="showQrModal = false" class="text-slate-400 hover:text-slate-600 rounded-xl p-2 hover:bg-slate-100 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Huge QR Image Container (350x350) -->
                    <div class="p-4 bg-white border-2 border-dashed border-orange-300 rounded-3xl shadow-inner mx-auto flex items-center justify-center max-w-[340px] sm:max-w-[380px] aspect-square">
                        @if($invoice->kipay_trx_id)
                            <img src="{{ route('admin.invoice.kipay-qr', $invoice->id) }}" alt="QRIS KiPay Full" class="w-full h-full object-contain rounded-xl">
                        @elseif(!empty($invoice->kipay_qr_payload))
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ urlencode($invoice->kipay_qr_payload) }}" alt="QRIS Dinamis Full" class="w-full h-full object-contain rounded-xl">
                        @endif
                    </div>

                    <!-- Amount & Instructions -->
                    <div class="text-center mt-6">
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Total yang Harus Dibayar</p>
                        <p class="text-3xl sm:text-4xl font-black text-slate-900 mt-0.5 tracking-tight">
                            Rp {{ number_format($invoice->kipay_amount ?: $invoice->grand_total, 0, ',', '.') }}
                        </p>
                        @if($invoice->kipay_amount && $invoice->kipay_amount != $invoice->grand_total)
                            <p class="text-xs text-orange-600 font-semibold mt-1">
                                (Termasuk kode unik transaksi KiPay)
                            </p>
                        @endif

                        <div class="mt-4 p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 leading-relaxed">
                            Buka aplikasi <strong>BCA, Mandiri, BRI, BNI, GoPay, OVO, Dana, ShopeePay</strong> lalu scan QRIS di atas.
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row justify-center gap-2">
                        @if($invoice->kipay_trx_id)
                        <a href="{{ route('admin.invoice.kipay-qr', $invoice->id) }}" target="_blank" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors text-center">
                            Buka File PNG Asli ↗
                        </a>
                        @endif
                        <button type="button" @click="showQrModal = false" class="py-2.5 px-4 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-xs transition-colors flex-1">
                            Tutup Tampilan Layar (Esc)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .printable-area, .printable-area * {
                visibility: visible;
            }
            .printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: none !important;
                box-shadow: none !important;
            }
            @page { margin: 0; }
            body { margin: 1.6cm; }
        }
    </style>
</x-app-layout>
