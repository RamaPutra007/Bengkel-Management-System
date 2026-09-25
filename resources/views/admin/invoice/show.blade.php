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
            <div class="flex gap-2">
                @if($invoice->payment_status === 'paid')
                <a href="{{ route('admin.invoice.print', $invoice->id) }}" target="_blank" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700 transition-colors font-medium text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Nota
                </a>
                @endif
                @if($invoice->payment_status !== 'paid')
                
                @if($invoice->payment_method === 'qris')
                <form action="{{ route('admin.invoice.send-qris', $invoice->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 border border-emerald-200 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-colors font-medium text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Kirim Ulang QRIS (WA)
                    </button>
                </form>

                <form action="{{ url('api/webhook/qris') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="invoice_number" value="{{ $invoice->invoice_number }}">
                    <input type="hidden" name="status" value="paid">
                    <button type="submit" class="px-4 py-2 border border-blue-200 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors font-medium text-sm flex items-center gap-2" title="Gunakan ini untuk mensimulasikan sistem menerima notifikasi pembayaran sukses dari Payment Gateway">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Simulasi Pelanggan Bayar
                    </button>
                </form>
                @endif
                
                <a href="{{ route('admin.invoice.edit', $invoice->id) }}" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-[#FF6B00] hover:text-white hover:border-[#FF6B00] active:bg-orange-700 active:border-orange-700 transition-all font-medium text-sm">
                    Bayar Manual
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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
                                    Metode: <span class="capitalize">{{ $invoice->payment_method }}</span><br>
                                    Tanggal: {{ $invoice->paid_at ? $invoice->paid_at->format('d M Y, H:i') : '-' }}
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
