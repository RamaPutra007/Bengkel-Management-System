<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran Invoice #{{ $invoice->invoice_number }} - BengkelPro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Figtree', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased py-8 px-4 sm:px-6">

    <div class="max-w-md mx-auto">
        <!-- Brand Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center gap-2 mb-2">
                <div class="w-10 h-10 bg-[#FF6B00] rounded-xl flex items-center justify-center text-white font-black text-xl shadow-sm shadow-orange-500/30">
                    B
                </div>
                <span class="font-black text-2xl tracking-tight text-slate-900">Bengkel<span class="text-[#FF6B00]">Pro</span></span>
            </div>
            <p class="text-xs text-slate-500 font-medium">Sistem Pembayaran Resmi Bengkel</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            
            <!-- Invoice Meta Header -->
            <div class="p-6 bg-slate-900 text-white relative overflow-hidden">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[11px] font-bold text-orange-400 uppercase tracking-widest block mb-1">Tagihan Servis</span>
                        <h2 class="text-xl font-bold font-mono tracking-tight">{{ $invoice->invoice_number }}</h2>
                        <p class="text-xs text-slate-400 mt-1">
                            Kendaraan: <span class="text-white font-medium">{{ $invoice->serviceOrder->vehicle->license_plate ?? '-' }} ({{ $invoice->serviceOrder->vehicle->brand ?? '' }} {{ $invoice->serviceOrder->vehicle->model ?? '' }})</span>
                        </p>
                    </div>
                    <div id="status_badge_container">
                        @if($invoice->payment_status === 'paid')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-full text-xs font-bold">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                LUNAS
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-full text-xs font-bold">
                                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                MENUNGGU BAYAR
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-800 flex justify-between items-end">
                    <div>
                        <p class="text-[11px] text-slate-400">Total Pembayaran</p>
                        <p class="text-2xl font-black text-white tracking-tight">
                            Rp {{ number_format($invoice->kipay_amount ?: $invoice->grand_total, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold px-2 py-1 rounded bg-orange-500/20 text-orange-400 border border-orange-500/30">
                            QRIS RESMI
                        </span>
                    </div>
                </div>
            </div>

            <!-- UNPAID VIEW: QRIS DISPLAY -->
            <div id="unpaid_section" class="{{ $invoice->payment_status === 'paid' ? 'hidden' : 'block' }} p-6 text-center">
                <p class="text-xs text-slate-500 mb-4">
                    Pindai QRIS di bawah menggunakan aplikasi <strong>Mobile Banking</strong> (BCA, Mandiri, BRI, BNI) atau <strong>E-Wallet</strong> (GoPay, OVO, ShopeePay, DANA):
                </p>

                <!-- Protected QR Image Container using internal route -->
                <div class="inline-block p-4 bg-white border-2 border-dashed border-orange-300 rounded-2xl shadow-sm mb-4 relative">
                    @if($invoice->kipay_trx_id)
                        <img
                            src="{{ route('checkout.qr', $invoice->kipay_trx_id) }}"
                            width="250"
                            height="250"
                            class="mx-auto rounded-lg"
                            alt="QRIS untuk {{ $invoice->invoice_number }}"
                        />
                    @elseif(!empty($invoice->kipay_qr_payload))
                        <img
                            src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($invoice->kipay_qr_payload) }}"
                            width="250"
                            height="250"
                            class="mx-auto rounded-lg"
                            alt="QRIS untuk {{ $invoice->invoice_number }}"
                        />
                    @else
                        <div class="w-60 h-60 flex items-center justify-center text-xs text-slate-400">
                            QRIS sedang dipersiapkan...
                        </div>
                    @endif

                    <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-white px-3 py-0.5 rounded-full border border-slate-200 text-[10px] font-bold text-slate-600 shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                        Otomatis Terverifikasi
                    </div>
                </div>

                @if($invoice->kipay_expired_at)
                    <p class="text-xs text-slate-400 mt-2">
                        Batas Waktu: <strong class="text-slate-700 font-semibold">{{ $invoice->kipay_expired_at->format('H:i') }} WIB</strong>
                    </p>
                @endif

                <!-- Payment Steps Info -->
                <div class="mt-6 pt-6 border-t border-slate-100 text-left">
                    <p class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Cara Pembayaran:</p>
                    <ol class="text-xs text-slate-500 space-y-1.5 list-decimal pl-4">
                        <li>Buka aplikasi m-Banking atau E-Wallet pilihan Anda.</li>
                        <li>Pilih menu <strong>Scan / Bayar QRIS</strong>.</li>
                        <li>Arahkan kamera ke kode QR di atas.</li>
                        <li>Periksa nominal <strong class="text-slate-800">Rp {{ number_format($invoice->kipay_amount ?: $invoice->grand_total, 0, ',', '.') }}</strong> dan selesaikan pembayaran.</li>
                        <li>Halaman ini akan otomatis berganti status setelah pembayaran sukses.</li>
                    </ol>
                </div>

                <!-- Polling indicator -->
                <div class="mt-6 flex items-center justify-center gap-2 text-[11px] text-slate-400">
                    <svg class="animate-spin h-3.5 w-3.5 text-orange-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Menunggu sinyal pembayaran dari KiPay...</span>
                </div>
            </div>

            <!-- PAID VIEW: SUCCESS SCREEN -->
            <div id="paid_section" class="{{ $invoice->payment_status === 'paid' ? 'block' : 'hidden' }} p-8 text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-1">Pembayaran Berhasil!</h3>
                <p class="text-xs text-slate-500 mb-6">Terima kasih, tagihan Anda telah lunas terverifikasi.</p>

                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-left space-y-2 mb-6 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Metode:</span>
                        <strong class="font-bold text-slate-800 uppercase" id="paid_provider">{{ $invoice->kipay_provider ?? 'QRIS' }}</strong>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Waktu Pembayaran:</span>
                        <span class="font-medium text-slate-800" id="paid_time">{{ $invoice->paid_at ? $invoice->paid_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }} WIB</span>
                    </div>
                    <div class="flex justify-between text-slate-600 pt-2 border-t border-slate-200">
                        <span class="font-bold text-slate-800">Total Dibayar:</span>
                        <span class="font-bold text-emerald-600">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <a href="{{ route('admin.invoice.print', $invoice->id) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak Bukti Pembayaran</span>
                </a>
            </div>

            <!-- Order Items Accordion -->
            <div class="px-6 pb-6 bg-slate-50 border-t border-slate-100">
                <details class="text-xs pt-4 group">
                    <summary class="cursor-pointer font-bold text-slate-700 flex justify-between items-center list-none select-none">
                        <span>Lihat Rincian Item Tagihan</span>
                        <svg class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="mt-3 space-y-2 pt-2 border-t border-slate-200">
                        @foreach($invoice->serviceOrder->items as $item)
                            <div class="flex justify-between items-center py-1">
                                <div>
                                    <p class="font-medium text-slate-800">{{ $item->item_name }} (x{{ $item->quantity }})</p>
                                    <p class="text-[10px] text-slate-400 capitalize">{{ $item->type === 'service' ? 'Jasa Servis' : 'Suku Cadang' }}</p>
                                </div>
                                <span class="font-semibold text-slate-700">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </details>
            </div>

        </div>

        <p class="text-center text-[11px] text-slate-400 mt-6">
            Pembayaran diamankan oleh <strong>KiPay Digital Payment Gateway</strong>
        </p>
    </div>

    <!-- Polling Script -->
    <script>
        const invoiceNumber = "{{ $invoice->invoice_number }}";
        let isPaid = {{ $invoice->payment_status === 'paid' ? 'true' : 'false' }};

        if (!isPaid) {
            const checkInterval = setInterval(() => {
                fetch(`/checkout/${invoiceNumber}/status`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.is_paid) {
                        clearInterval(checkInterval);
                        
                        // Switch UI smoothly
                        document.getElementById('unpaid_section').classList.add('hidden');
                        document.getElementById('paid_section').classList.remove('hidden');
                        
                        document.getElementById('status_badge_container').innerHTML = `
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-full text-xs font-bold">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                LUNAS
                            </span>
                        `;

                        if (data.provider) {
                            document.getElementById('paid_provider').innerText = data.provider;
                        }
                        if (data.paid_at) {
                            document.getElementById('paid_time').innerText = data.paid_at + ' WIB';
                        }
                    }
                })
                .catch(err => console.log('Polling check:', err));
            }, 3000); // Check every 3 seconds
        }
    </script>
</body>
</html>

