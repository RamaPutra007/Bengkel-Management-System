<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.service-order.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    {{ __('Detail Transaksi') }}
                </h2>
            </div>
            <div class="flex gap-2">
                @if(!$serviceOrder->inspection)
                    <a href="{{ route('admin.inspection.create', ['service_order_id' => $serviceOrder->id]) }}" class="px-4 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors font-medium text-sm flex items-center gap-2">
                        Buat Laporan Inspeksi
                    </a>
                @else
                    <a href="{{ route('admin.inspection.show', $serviceOrder->inspection->id) }}" class="px-4 py-2 bg-blue-600 text-white border border-blue-700 rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm flex items-center gap-2">
                        Lihat Inspeksi
                    </a>
                @endif
                <a href="{{ route('admin.service-order.edit', $serviceOrder->id) }}" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors font-medium text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Ubah Status / Mekanik
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <!-- Header Info -->
                <div class="p-8 border-b border-slate-100">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Nomor Pesanan (Order Number)</p>
                            <h3 class="text-3xl font-black text-[#FF6B00]">{{ $serviceOrder->order_number }}</h3>
                            <p class="text-sm text-slate-500 mt-2">Dibuat pada: {{ $serviceOrder->created_at->format('d F Y, H:i') }}</p>
                        </div>
                        <div class="text-left md:text-right">
                            <p class="text-sm font-medium text-slate-500 mb-2">Status Saat Ini</p>
                            @if($serviceOrder->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Menunggu (Pending)
                                </span>
                            @elseif($serviceOrder->status === 'in_progress')
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Sedang Dikerjakan
                                </span>
                            @elseif($serviceOrder->status === 'completed')
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Selesai
                                </span>
                            @elseif($serviceOrder->status === 'cancelled')
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold bg-red-50 text-red-700 border border-red-200">
                                    <span class="w-2 h-2 rounded-full bg-red-500"></span> Dibatalkan
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Parties Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-100 bg-slate-50">
                    <!-- Customer -->
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <h4 class="font-bold text-slate-700">Info Pelanggan</h4>
                        </div>
                        <p class="font-bold text-slate-800 text-lg">{{ $serviceOrder->customer->name ?? 'Data Terhapus' }}</p>
                        <p class="text-slate-500 text-sm mt-1">{{ $serviceOrder->customer->phone ?? '-' }}</p>
                        <p class="text-slate-500 text-sm">{{ $serviceOrder->customer->email ?? '-' }}</p>
                    </div>

                    <!-- Vehicle -->
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-10 w-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <h4 class="font-bold text-slate-700">Data Kendaraan</h4>
                        </div>
                        @if($serviceOrder->vehicle)
                            <div class="inline-block font-bold text-slate-800 bg-white px-3 py-1.5 rounded border border-slate-200 uppercase mb-2">
                                {{ $serviceOrder->vehicle->license_plate }}
                            </div>
                            <p class="font-bold text-slate-700">{{ $serviceOrder->vehicle->brand }} {{ $serviceOrder->vehicle->model }}</p>
                            <p class="text-slate-500 text-sm mt-1">{{ $serviceOrder->vehicle->color }} &bull; {{ $serviceOrder->vehicle->year }}</p>
                        @else
                            <p class="text-slate-500 text-sm italic">Data kendaraan dihapus</p>
                        @endif
                    </div>

                    <!-- Mechanic -->
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-10 w-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                            </div>
                            <h4 class="font-bold text-slate-700">Mekanik Bertugas</h4>
                        </div>
                        <p class="font-bold text-slate-800 text-lg">{{ $serviceOrder->mechanic->name ?? 'Belum Ditentukan' }}</p>
                        <p class="text-slate-500 text-sm mt-1">{{ $serviceOrder->mechanic->specialization ?? '-' }}</p>
                    </div>
                </div>

                <!-- Items Details -->
                <div class="p-8">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Rincian Pekerjaan & Suku Cadang</h3>
                    
                    <div class="overflow-x-auto border border-slate-200 rounded-xl mb-6">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Item / Deskripsi</th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase w-24">Tipe</th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-right">Harga (Rp)</th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center w-24">Qty</th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-right">Subtotal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($serviceOrder->items as $item)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-800">{{ $item->item_name }}</div>
                                            @if($item->type === 'sparepart' && $item->sparepart)
                                                <div class="text-xs font-mono text-slate-400 mt-1">PN: {{ $item->sparepart->part_number }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($item->type === 'service')
                                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">Jasa</span>
                                            @else
                                                <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded text-xs font-medium">Part</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm text-slate-600">
                                            {{ number_format($item->price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm font-bold text-slate-700">
                                            x{{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-slate-800">
                                            {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary & Notes -->
                    <div class="flex flex-col md:flex-row justify-between gap-8">
                        <div class="w-full md:w-1/2">
                            <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Catatan Keluhan / Tambahan</h4>
                            <div class="bg-amber-50/50 p-4 rounded-xl border border-amber-100/50 text-slate-700 text-sm italic min-h-[100px]">
                                {{ $serviceOrder->notes ?? 'Tidak ada catatan.' }}
                            </div>
                        </div>
                        <div class="w-full md:w-1/2">
                            <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-slate-500 font-medium">Subtotal Jasa & Parts</span>
                                    <span class="font-bold text-slate-700">Rp {{ number_format($serviceOrder->total_price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-slate-500 font-medium">PPN (11%)</span>
                                    <span class="text-slate-400 italic text-sm">Belum Termasuk / Diatur Modul Invoice</span>
                                </div>
                                <div class="border-t border-slate-200 pt-4 flex justify-between items-center">
                                    <span class="text-lg font-black text-slate-800">TOTAL ESTIMASI</span>
                                    <span class="text-3xl font-black text-[#FF6B00]">Rp {{ number_format($serviceOrder->total_price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            @if($serviceOrder->status === 'completed')
                                <div class="mt-4 text-right">
                                    <a href="{{ route('admin.invoice.create', ['service_order_id' => $serviceOrder->id]) }}" class="px-6 py-3 bg-slate-800 text-white w-full rounded-lg hover:bg-slate-700 transition-colors font-bold flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Buat Invoice / Tagihan
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
