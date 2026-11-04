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
            <div class="flex flex-wrap gap-2">
                @if($invoice->payment_status === 'paid')
                <a href="{{ route('admin.invoice.print', $invoice->id) }}" target="_blank" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700 transition-colors font-medium text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Nota
                </a>
                @endif
                @if($invoice->payment_status !== 'paid')
                
                @if($invoice->payment_method === 'qris' || $invoice->kipay_trx_id)
                <form action="{{ route('admin.invoice.send-qris', $invoice->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 border border-emerald-200 bg-emerald-50 text-emerald-700 rounded-lg hover:bg-emerald-100 transition-colors font-medium text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Kirim QRIS (WA)
                    </button>
                </form>

                @if($invoice->kipay_trx_id)
                <a href="{{ route('admin.invoice.check-kipay', $invoice->id) }}" class="px-4 py-2 border border-sky-200 bg-sky-50 text-sky-700 rounded-lg hover:bg-sky-100 transition-colors font-medium text-sm flex items-center gap-2" title="Cek apakah pembayaran sudah masuk di sistem KiPay">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Cek Status KiPay
                </a>

                @if(($kiPayMode ?? 'sandbox') === 'sandbox')
                <form action="{{ route('admin.invoice.simulate-kipay', $invoice->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 border border-orange-200 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition-colors font-medium text-sm flex items-center gap-2" title="Simulasikan pembayaran sukses via KiPay API Sandbox">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Simulasi Bayar KiPay
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

    <div class="py-12">
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
            <div class="bg-white rounded-2xl border border-orange-200 shadow-sm p-6 overflow-hidden relative">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="w-36 h-36 p-2 bg-white border border-slate-200 rounded-xl shadow-sm shrink-0 flex items-center justify-center">
                            @if($invoice->kipay_trx_id)
                                <img src="{{ route('admin.invoice.kipay-qr', $invoice->id) }}" alt="QRIS KiPay" class="w-32 h-32 object-contain">
                            @elseif(!empty($invoice->kipay_qr_payload))
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($invoice->kipay_qr_payload) }}" alt="QRIS Dinamis" class="w-32 h-32 object-contain">
                            @else
                                <div class="text-center p-2 text-xs text-slate-400">
                                    QRIS belum dibuat
                                </div>
                            @endif
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-[#FF6B00] text-white">
                                    KIPAY QRIS
                                </span>
                                @if($invoice->payment_status === 'paid')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        LUNAS ({{ strtoupper($invoice->kipay_provider ?? 'QRIS') }})
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        MENUNGGU PEMBAYARAN
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 font-mono">
                                Trx ID: <strong class="text-slate-700">{{ $invoice->kipay_trx_id ?? 'Belum terdaftar di KiPay' }}</strong>
                            </p>
                            <p class="text-sm text-slate-600">
                                Total Bayar: <strong class="text-base text-slate-900 font-black">Rp {{ number_format($invoice->kipay_amount ?: $invoice->grand_total, 0, ',', '.') }}</strong>
                                @if($invoice->kipay_amount && $invoice->kipay_amount != $invoice->grand_total)
                                    <span class="text-xs text-slate-400">(termasuk kode unik)</span>
                                @endif
                            </p>
                            @if($invoice->kipay_expired_at)
                                <p class="text-xs text-slate-500">
                                    Batas Waktu: <span class="{{ $invoice->kipay_expired_at->isPast() ? 'text-rose-600 font-bold' : 'text-slate-700' }}">{{ $invoice->kipay_expired_at->format('d M Y, H:i') }} WIB</span>
                                    @if($invoice->kipay_expired_at->isPast() && $invoice->payment_status !== 'paid')
                                        <span class="text-rose-600 font-semibold">(Kadaluarsa)</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($invoice->payment_status !== 'paid')
                    <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                        @if($invoice->kipay_trx_id)
                            <a href="{{ route('admin.invoice.check-kipay', $invoice->id) }}" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold text-center transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>Cek Status</span>
                            </a>

                            @if(($kiPayMode ?? 'sandbox') === 'sandbox')
                                <form action="{{ route('admin.invoice.simulate-kipay', $invoice->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 bg-[#FF6B00] hover:bg-orange-600 text-white rounded-xl text-xs font-bold text-center transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        <span>Simulasi Bayar</span>
                                    </button>
                                </form>
                            @endif
                        @else
                            <form action="{{ route('admin.invoice.send-qris', $invoice->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-[#FF6B00] hover:bg-orange-600 text-white rounded-xl text-xs font-bold text-center transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                                    <span>Buat Transaksi KiPay</span>
                                </button>
                            </form>
                        @endif
                    </div>
                    @endif
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
