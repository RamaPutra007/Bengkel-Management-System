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
            @if($invoice->payment_status === 'paid')
            <div>
                <a href="{{ route('admin.invoice.print', $invoice->id) }}" target="_blank" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700 transition-colors font-medium text-sm flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Nota
                </a>
            </div>
            @endif
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

            <!-- KiPay QRIS Standee Card Section (Hanya tampil jika BELUM lunas) -->
            @if($invoice->payment_status !== 'paid' && ($invoice->payment_method === 'qris' || $invoice->kipay_trx_id))
            <div class="space-y-4">
                <!-- Authentic Indonesian QRIS Standee Card -->
                <div class="max-w-md mx-auto bg-white rounded-3xl shadow-xl shadow-slate-200/60 border-2 border-slate-200 overflow-hidden relative font-sans">
                    
                    <!-- Official QRIS Red Header -->
                    <div class="bg-gradient-to-r from-red-600 via-rose-600 to-red-700 text-white px-6 py-4 flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="font-black text-2xl tracking-tighter text-white">QRIS</span>
                            <div class="h-6 w-px bg-white/30"></div>
                            <div class="text-[9px] font-bold tracking-widest uppercase leading-tight text-white/95">
                                PEMBAYARAN DIGITAL<br>NASIONAL
                            </div>
                        </div>
                        <div class="px-2.5 py-1 bg-white/20 backdrop-blur-sm rounded-lg text-[10px] font-black tracking-wider text-white border border-white/30">
                            GPN
                        </div>
                    </div>

                    <!-- Merchant Header Info -->
                    <div class="px-6 pt-5 pb-3 text-center border-b border-slate-100 bg-slate-50/50">
                        <h3 class="font-black text-lg text-slate-800 tracking-tight">{{ config('app.name', 'BengkelPro') }}</h3>
                        <p class="text-xs text-slate-500 font-mono mt-0.5">
                            Trx ID: <strong class="text-slate-700">{{ $invoice->kipay_trx_id ?? 'Belum ada Trx' }}</strong>
                        </p>
                    </div>

                    <!-- BIG QRIS CODE DISPLAY (288px - 320px) -->
                    <div class="p-6 text-center">
                        <div class="relative group cursor-pointer inline-block mx-auto" @click="showQrModal = true" title="Klik untuk memperbesar QRIS ke layar penuh">
                            
                            <!-- Viewfinder Box with Red Corner Guides -->
                            <div class="w-72 h-72 sm:w-80 sm:h-80 p-4 bg-white border-2 border-slate-300 rounded-2xl shadow-inner flex items-center justify-center relative transition-all group-hover:border-[#FF6B00] group-hover:shadow-md">
                                
                                <!-- Corner Guides -->
                                <div class="absolute -top-1 -left-1 w-5 h-5 border-t-4 border-l-4 border-red-600 rounded-tl"></div>
                                <div class="absolute -top-1 -right-1 w-5 h-5 border-t-4 border-r-4 border-red-600 rounded-tr"></div>
                                <div class="absolute -bottom-1 -left-1 w-5 h-5 border-b-4 border-l-4 border-red-600 rounded-bl"></div>
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 border-b-4 border-r-4 border-red-600 rounded-br"></div>

                                @if($invoice->kipay_trx_id)
                                    <img src="{{ route('admin.invoice.kipay-qr', $invoice->id) }}" alt="QRIS KiPay" class="w-full h-full object-contain">
                                @elseif(!empty($invoice->kipay_qr_payload))
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ urlencode($invoice->kipay_qr_payload) }}" alt="QRIS Dinamis" class="w-full h-full object-contain">
                                @else
                                    <div class="text-center p-6 text-xs text-slate-400">
                                        QRIS belum dibuat
                                    </div>
                                @endif
                            </div>

                            <!-- Overlay Hover Pill -->
                            <div class="absolute inset-0 bg-slate-900/50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-2 backdrop-blur-[2px]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                <span>Klik Perbesar Layar Penuh</span>
                            </div>
                        </div>

                        <!-- Supported Payment Methods -->
                        <p class="text-[11px] text-slate-500 font-medium mt-3">
                            Menerima pembayaran dari seluruh aplikasi:
                        </p>
                        <div class="flex flex-wrap items-center justify-center gap-1.5 mt-1.5 text-[10px] font-bold text-slate-600">
                            <span class="px-2 py-0.5 bg-slate-100 rounded">BCA</span>
                            <span class="px-2 py-0.5 bg-slate-100 rounded">Mandiri</span>
                            <span class="px-2 py-0.5 bg-slate-100 rounded">BRI</span>
                            <span class="px-2 py-0.5 bg-slate-100 rounded">BNI</span>
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded">GoPay</span>
                            <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded">OVO</span>
                            <span class="px-2 py-0.5 bg-sky-50 text-sky-700 rounded">DANA</span>
                            <span class="px-2 py-0.5 bg-orange-50 text-[#FF6B00] rounded">ShopeePay</span>
                        </div>

                        <!-- Card Bottom: Total Bayar & Status -->
                        <div class="mt-5 pt-4 border-t border-slate-100 bg-slate-50/70 -mx-6 -mb-6 p-6 rounded-b-3xl">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pembayaran</span>
                                @if($invoice->payment_status === 'paid')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        LUNAS ({{ strtoupper($invoice->kipay_provider ?? 'QRIS') }})
                                    </span>
                                @elseif($invoice->kipay_expired_at && $invoice->kipay_expired_at->isPast())
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200 flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                        KADALUARSA
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                        MENUNGGU PEMBAYARAN
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-baseline justify-between gap-2">
                                <span class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                                    Rp {{ number_format($invoice->kipay_amount ?: $invoice->grand_total, 0, ',', '.') }}
                                </span>
                                @if($invoice->kipay_amount && $invoice->kipay_amount != $invoice->grand_total)
                                    <span class="text-xs font-bold text-orange-700 bg-orange-100 border border-orange-200 px-2 py-0.5 rounded-full">
                                        + kode unik
                                    </span>
                                @endif
                            </div>

                            @if($invoice->kipay_expired_at)
                                <p class="text-xs text-slate-500 mt-2 text-left">
                                    Batas Waktu Scan: <strong class="{{ $invoice->kipay_expired_at->isPast() ? 'text-rose-600 font-bold' : 'text-slate-800' }}">{{ $invoice->kipay_expired_at->format('d M Y, H:i') }} WIB</strong>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Controls Below Card -->
                <div class="max-w-md mx-auto space-y-3">
                    
                    <!-- WhatsApp Direct Send Button -->
                    @if($waDirectUrl)
                    <a href="{{ $waDirectUrl }}" target="_blank" rel="noopener noreferrer" class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-2xl text-sm font-bold text-center transition-all flex items-center justify-center gap-2.5 shadow-md shadow-emerald-600/25">
                        <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        <span>Kirim Tagihan & QRIS via WhatsApp (Web / HP)</span>
                    </a>
                    <p class="text-[11px] text-slate-500 text-center">Buka WhatsApp ke nomor <strong>{{ $waCustomerPhone ?? ($invoice->serviceOrder->customer->phone ?? '-') }}</strong> tanpa perlu koneksi bot.</p>
                    @endif

                    <!-- Payment Action Grid (3 Buttons) -->
                    @if($invoice->payment_status !== 'paid')
                    <div class="grid grid-cols-3 gap-2">
                        @if($invoice->kipay_trx_id)
                            <a href="{{ route('admin.invoice.check-kipay', $invoice->id) }}" class="py-2.5 px-3 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold text-center transition-colors flex items-center justify-center gap-1 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>Cek Status</span>
                            </a>

                            @if(($kiPayMode ?? 'sandbox') === 'sandbox')
                                <form action="{{ route('admin.invoice.simulate-kipay', $invoice->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-full py-2.5 px-3 bg-[#FF6B00] hover:bg-orange-600 text-white rounded-xl text-xs font-bold text-center transition-colors flex items-center justify-center gap-1 shadow-sm" title="Simulasi bayar berhasil (otomatis perbarui QRIS jika kadaluarsa)">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        <span>Simulasi Bayar</span>
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('admin.invoice.edit', $invoice->id) }}" class="py-2.5 px-3 border border-slate-300 hover:border-slate-400 bg-white hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold text-center transition-colors flex items-center justify-center gap-1 shadow-sm">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span>Bayar Tunai</span>
                            </a>
                        @else
                            <form action="{{ route('admin.invoice.send-qris', $invoice->id) }}" method="POST" class="inline col-span-2">
                                @csrf
                                <button type="submit" class="w-full py-2.5 px-3 bg-[#FF6B00] hover:bg-orange-600 text-white rounded-xl text-xs font-bold text-center transition-colors shadow-sm">
                                    Buat Transaksi KiPay
                                </button>
                            </form>
                            <a href="{{ route('admin.invoice.edit', $invoice->id) }}" class="py-2.5 px-3 border border-slate-300 bg-white text-slate-700 rounded-xl text-xs font-bold text-center">
                                Bayar Tunai
                            </a>
                        @endif
                    </div>
                    @endif

                    <!-- Utilities & Secondary Links -->
                    <div class="flex flex-wrap items-center justify-center gap-3 pt-1 text-xs">
                        <button type="button" @click="showQrModal = true" class="text-slate-600 hover:text-slate-900 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                            <span>Layar Penuh</span>
                        </button>
                        @if($invoice->kipay_trx_id)
                        <a href="{{ route('admin.invoice.kipay-qr', $invoice->id) }}" download="qris-{{ $invoice->invoice_number }}.png" class="text-slate-600 hover:text-slate-900 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Unduh PNG</span>
                        </a>
                        <a href="{{ route('admin.invoice.kipay-qr', $invoice->id) }}" target="_blank" class="text-slate-600 hover:text-slate-900 font-medium flex items-center gap-1">
                            <span>PNG Asli (500x500) ↗</span>
                        </a>
                        @endif
                        <a href="{{ route('checkout.show', $invoice->invoice_number) }}" target="_blank" class="text-[#FF6B00] hover:underline font-bold flex items-center gap-1">
                            <span>Checkout Publik ↗</span>
                        </a>
                    </div>

                    <!-- Expired State Re-create Action -->
                    @if($invoice->kipay_expired_at && $invoice->kipay_expired_at->isPast() && $invoice->payment_status !== 'paid')
                    <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 flex items-center justify-between gap-3 shadow-sm">
                        <div>
                            <strong class="font-bold">QRIS Kadaluarsa:</strong> Batas waktu 15 menit telah habis.
                        </div>
                        <form action="{{ route('admin.invoice.send-qris', $invoice->id) }}" method="POST" class="shrink-0">
                            @csrf
                            <button type="submit" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs shadow-sm transition-all">
                                🔄 Perbarui QRIS
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($invoice->payment_status !== 'paid' && $invoice->payment_method !== 'qris' && !$invoice->kipay_trx_id)
            <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">Status Tagihan: Belum Lunas</h4>
                    <p class="text-xs text-slate-500">Pilih metode pembayaran yang diinginkan pelanggan:</p>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('admin.invoice.send-qris', $invoice->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-[#FF6B00] hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                            Bayar via QRIS KiPay
                        </button>
                    </form>
                    <a href="{{ route('admin.invoice.edit', $invoice->id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all">
                        Bayar Manual (Tunai)
                    </a>
                </div>
            </div>
            @endif

            <!-- Paid Confirmation Banner (Ketika sudah lunas, langsung fokus ke Nota) -->
            @if($invoice->payment_status === 'paid')
            <div class="p-5 bg-emerald-50 border border-emerald-200 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold shrink-0 shadow-sm shadow-emerald-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-base">Pembayaran Telah Lunas Terverifikasi</h4>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Metode: <strong class="capitalize text-slate-800">{{ $invoice->payment_method ?? 'QRIS' }}</strong>
                            @if($invoice->kipay_provider)
                                ({{ strtoupper($invoice->kipay_provider) }})
                            @endif
                            • Tanggal: <span class="font-medium text-slate-800">{{ $invoice->paid_at ? $invoice->paid_at->format('d M Y, H:i') : '-' }} WIB</span>
                            @if($invoice->kipay_trx_id)
                                • Trx ID: <span class="font-mono text-slate-700 font-semibold">{{ $invoice->kipay_trx_id }}</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('admin.invoice.print', $invoice->id) }}" target="_blank" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span>Cetak Nota</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- Invoice Document Paper (Hanya Muncul Resmi Ketika Sudah Pembayaran Selesai) -->
            @if($invoice->payment_status === 'paid')
            <div class="bg-white overflow-hidden shadow-sm border border-slate-200 printable-area rounded-2xl" id="invoice-doc">
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
                                <span class="inline-block px-4 py-1.5 border-2 border-emerald-500 text-emerald-600 font-black text-lg tracking-widest rounded rotate-[-5deg] bg-white">LUNAS</span>
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
            @else
            <!-- Saat Belum Lunas: Tampilkan Rincian Tagihan yang Rapi & Ringkas (Collapsible) -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <details class="group">
                    <summary class="cursor-pointer p-5 font-bold text-slate-800 flex justify-between items-center list-none select-none hover:bg-slate-50 transition-colors">
                        <span class="flex items-center gap-2.5 text-sm">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Rincian Item Tagihan & Estimasi Biaya ({{ $invoice->serviceOrder->items->count() }} item)</span>
                        </span>
                        <span class="text-xs text-[#FF6B00] font-bold group-open:hidden">
                            Lihat Rincian ▼
                        </span>
                        <span class="text-xs text-slate-400 font-bold hidden group-open:inline">
                            Tutup Rincian ▲
                        </span>
                    </summary>
                    <div class="p-6 border-t border-slate-100 bg-slate-50/50 space-y-4">
                        <div class="border border-slate-200 rounded-lg overflow-hidden bg-white">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-100 border-b border-slate-200">
                                    <tr>
                                        <th class="py-2.5 px-3 font-bold text-slate-600 uppercase">Deskripsi Item</th>
                                        <th class="py-2.5 px-3 font-bold text-slate-600 uppercase text-right">Harga</th>
                                        <th class="py-2.5 px-3 font-bold text-slate-600 uppercase text-center w-16">Qty</th>
                                        <th class="py-2.5 px-3 font-bold text-slate-600 uppercase text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($invoice->serviceOrder->items as $item)
                                        <tr>
                                            <td class="py-3 px-3">
                                                <p class="font-bold text-slate-700">{{ $item->item_name }}</p>
                                                <p class="text-[10px] text-slate-400 capitalize">{{ $item->type === 'service' ? 'Jasa Servis' : 'Suku Cadang' }}</p>
                                            </td>
                                            <td class="py-3 px-3 text-right text-slate-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                            <td class="py-3 px-3 text-center font-bold text-slate-700">{{ $item->quantity }}</td>
                                            <td class="py-3 px-3 text-right font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row justify-between items-center text-xs gap-2 pt-1">
                            <span class="text-slate-500">Subtotal: Rp {{ number_format($invoice->subtotal, 0, ',', '.') }} | Diskon: Rp {{ number_format($invoice->discount, 0, ',', '.') }}</span>
                            <span class="text-sm font-black text-slate-800">Total Tagihan: <strong class="text-[#FF6B00]">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</strong></span>
                        </div>
                    </div>
                </details>
            </div>
            @endif
        </div>

        <!-- Fullscreen QRIS Modal for Counter Scan (Hanya Saat Belum Lunas) -->
        @if($invoice->payment_status !== 'paid')
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
        @endif
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
