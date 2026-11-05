<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #{{ $invoice->invoice_number }} - BengkelPro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Figtree', sans-serif; }
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            #invoice-doc {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased py-8 px-4 sm:px-6">

    @if($invoice->payment_status === 'paid')
        <!-- TAMPILAN RESMI KETIKA SUDAH LUNAS: NOTA INVOICE LENGKAP -->
        <div class="max-w-3xl mx-auto space-y-6">
            
            <!-- Top Action Banner (No Print) -->
            <div class="no-print p-4 sm:p-5 bg-emerald-50 border border-emerald-200 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold shrink-0 shadow-sm shadow-emerald-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-slate-800 text-base">Pembayaran Berhasil Terverifikasi</h4>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Metode: <strong class="capitalize text-slate-800">{{ $invoice->payment_method ?? 'QRIS' }}</strong>
                            @if($invoice->kipay_provider)
                                ({{ strtoupper($invoice->kipay_provider) }})
                            @endif
                            • {{ $invoice->paid_at ? $invoice->paid_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }} WIB
                            @if($invoice->kipay_trx_id)
                                • Trx: <span class="font-mono font-semibold text-slate-700">{{ $invoice->kipay_trx_id }}</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" onclick="window.print()" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span>Cetak / Simpan Nota (PDF)</span>
                    </button>
                </div>
            </div>

            <!-- Invoice Document Paper (Sesuai Desain Nota Resmi BengkelPro) -->
            <div class="bg-white overflow-hidden shadow-sm border border-slate-200 rounded-3xl printable-area" id="invoice-doc">
                <div class="p-6 sm:p-10">
                    
                    <!-- Header Invoice -->
                    <div class="flex flex-col md:flex-row justify-between items-start border-b border-slate-200 pb-8 mb-8">
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-[#FF6B00] rounded-xl flex items-center justify-center text-white font-black text-xl shadow-sm shadow-orange-500/30">
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

                    <!-- Kepada Yth & Informasi Kendaraan -->
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
                    <div class="mb-8 border border-slate-200 rounded-xl overflow-hidden">
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

                    <!-- Totals & Payment Info -->
                    <div class="flex flex-col md:flex-row justify-between items-end gap-8 mb-12">
                        <div class="w-full md:w-1/2">
                            @if($invoice->notes)
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Catatan Tambahan</p>
                                <p class="text-slate-600 text-sm italic">{{ $invoice->notes }}</p>
                            @endif
                            <div class="mt-4 p-4 bg-emerald-50 rounded-xl border border-emerald-100 text-sm">
                                <span class="font-bold text-emerald-800 block mb-1">Informasi Pembayaran:</span>
                                Metode: <span class="capitalize">{{ $invoice->payment_method ?? 'QRIS' }}</span>
                                @if($invoice->kipay_provider)
                                    ({{ strtoupper($invoice->kipay_provider) }})
                                @endif
                                <br>
                                Tanggal: {{ $invoice->paid_at ? $invoice->paid_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }} WIB<br>
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

            <!-- Bottom Action (No Print) -->
            <div class="no-print text-center pt-2">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl text-sm font-bold shadow-md transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak / Simpan Bukti Pembayaran (PDF)</span>
                </button>
            </div>
        </div>

    @else
        <!-- TAMPILAN KETIKA BELUM LUNAS: CHECKOUT & SCAN QRIS -->
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
                            @if($invoice->kipay_expired_at && $invoice->kipay_expired_at->isPast())
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-500/20 text-rose-300 border border-rose-500/30 rounded-full text-xs font-bold">
                                    <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                    KADALUARSA
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
                <div id="unpaid_section" class="p-6 text-center">
                    @if($invoice->kipay_expired_at && $invoice->kipay_expired_at->isPast())
                        <!-- State: Expired -->
                        <div class="p-6 bg-rose-50 border border-rose-200 rounded-2xl text-center mb-6">
                            <div class="w-14 h-14 mx-auto rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mb-3">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h4 class="font-bold text-rose-900 text-base mb-1">QRIS Telah Kadaluarsa</h4>
                            <p class="text-xs text-rose-600">
                                Batas waktu pembayaran telah berakhir pada <strong>{{ $invoice->kipay_expired_at->format('H:i') }} WIB</strong>.
                                Sesuai aturan keamanan QRIS, silakan hubungi kasir/bengkel untuk membuat transaksi QRIS baru.
                            </p>
                        </div>
                    @else
                        <p class="text-xs text-slate-500 mb-4">
                            Pindai QRIS di bawah menggunakan aplikasi <strong>Mobile Banking</strong> (BCA, Mandiri, BRI, BNI) atau <strong>E-Wallet</strong> (GoPay, OVO, ShopeePay, DANA):
                        </p>

                        <!-- Protected QR Image Container using internal route -->
                        <div class="inline-block p-4 bg-white border-2 border-dashed border-orange-300 rounded-3xl shadow-sm mb-3 relative">
                            @if($invoice->kipay_trx_id)
                                <img
                                    src="{{ route('checkout.qr', $invoice->kipay_trx_id) }}"
                                    width="280"
                                    height="280"
                                    class="w-64 h-64 sm:w-72 sm:h-72 mx-auto rounded-xl object-contain"
                                    alt="QRIS untuk {{ $invoice->invoice_number }}"
                                />
                            @elseif(!empty($invoice->kipay_qr_payload))
                                <img
                                    src="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ urlencode($invoice->kipay_qr_payload) }}"
                                    width="280"
                                    height="280"
                                    class="w-64 h-64 sm:w-72 sm:h-72 mx-auto rounded-xl object-contain"
                                    alt="QRIS untuk {{ $invoice->invoice_number }}"
                                />
                            @else
                                <div class="w-64 h-64 flex items-center justify-center text-xs text-slate-400">
                                    QRIS sedang dipersiapkan...
                                </div>
                            @endif

                            <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-white px-3 py-0.5 rounded-full border border-slate-200 text-[10px] font-bold text-slate-600 shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                Otomatis Terverifikasi
                            </div>
                        </div>

                        @if($invoice->kipay_trx_id)
                        <div class="mt-3 flex justify-center">
                            <a href="{{ route('checkout.qr', $invoice->kipay_trx_id) }}" download="qris-{{ $invoice->invoice_number }}.png" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                <span>Simpan / Unduh QRIS</span>
                            </a>
                        </div>
                        @endif

                        @if($invoice->kipay_expired_at)
                            <p class="text-xs text-slate-400 mt-2">
                                Batas Waktu Pembayaran: <strong class="text-slate-700 font-semibold">{{ $invoice->kipay_expired_at->format('H:i') }} WIB</strong>
                            </p>
                        @endif
                    @endif

                    <!-- Button Manual Check & Polling Info -->
                    <div class="mt-6 flex flex-col items-center gap-3">
                        <button type="button" onclick="checkPaymentStatusNow()" id="btn_manual_check" class="w-full py-2.5 px-4 bg-orange-50 hover:bg-orange-100 text-[#FF6B00] border border-orange-200 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span id="btn_check_text">Saya Sudah Bayar (Cek Status)</span>
                        </button>

                        <div class="flex items-center justify-center gap-2 text-[11px] text-slate-400">
                            <svg class="animate-spin h-3.5 w-3.5 text-orange-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Menunggu sinyal pembayaran otomatis dari KiPay...</span>
                        </div>
                    </div>

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

        <!-- Polling Script (Hanya Berjalan Saat Belum Lunas) -->
        <script>
            const invoiceNumber = "{{ $invoice->invoice_number }}";
            let checkInterval = null;

            function handlePaymentSuccess(data) {
                if (checkInterval) clearInterval(checkInterval);
                // Langsung refresh halaman agar menampilkan nota invoice resmi dengan stempel LUNAS
                window.location.reload();
            }

            function checkPaymentStatusNow() {
                const btn = document.getElementById('btn_manual_check');
                const textEl = document.getElementById('btn_check_text');
                if (btn) btn.disabled = true;
                if (textEl) textEl.innerText = 'Memeriksa status...';

                fetch(`/checkout/${invoiceNumber}/status`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (btn) btn.disabled = false;
                    if (data.is_paid) {
                        if (textEl) textEl.innerText = 'Pembayaran Lunas!';
                        handlePaymentSuccess(data);
                    } else {
                        if (textEl) textEl.innerText = 'Belum Ada Pembayaran (Cek Lagi)';
                        setTimeout(() => {
                            if (textEl) textEl.innerText = 'Saya Sudah Bayar (Cek Status)';
                        }, 3000);
                    }
                })
                .catch(err => {
                    if (btn) btn.disabled = false;
                    if (textEl) textEl.innerText = 'Gagal memeriksa. Coba lagi.';
                });
            }

            checkInterval = setInterval(() => {
                fetch(`/checkout/${invoiceNumber}/status`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.is_paid) {
                        handlePaymentSuccess(data);
                    }
                })
                .catch(err => console.log('Polling check:', err));
            }, 3000); // Check every 3 seconds
        </script>
    @endif
</body>
</html>
