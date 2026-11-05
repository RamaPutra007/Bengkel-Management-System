<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.service-order.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    {{ __('Detail Transaksi') }}
                </h2>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if(!$serviceOrder->inspection)
                    <a href="{{ route('admin.inspection.create', ['service_order_id' => $serviceOrder->id]) }}" class="px-4 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors font-medium text-sm flex items-center gap-2">
                        Buat Laporan Inspeksi
                    </a>
                @else
                    <a href="{{ route('admin.inspection.show', $serviceOrder->inspection->id) }}" class="px-4 py-2 bg-blue-600 text-white border border-blue-700 rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm flex items-center gap-2">
                        Lihat Inspeksi
                    </a>
                @endif
                @if($serviceOrder->status !== 'completed' && $serviceOrder->status !== 'selesai')
                <a href="{{ route('admin.service-order.edit', $serviceOrder->id) }}" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors font-medium text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Ubah Status / Mekanik
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
            
            @if (session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl relative flex items-center gap-2 shadow-sm" role="alert">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="block sm:inline font-medium text-sm">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl relative flex items-center gap-2 shadow-sm" role="alert">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span class="block sm:inline font-medium text-sm">{{ session('error') }}</span>
                </div>
            @endif

            @if (session('warning'))
                <div class="mb-4 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl relative flex items-center gap-2 shadow-sm" role="alert">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span class="block sm:inline font-medium text-sm">{{ session('warning') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <!-- Header Info -->
                <div class="p-5 sm:p-8 border-b border-slate-100">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Nomor Pesanan (Order Number)</p>
                            <h3 class="text-3xl font-black text-[#FF6B00]">{{ $serviceOrder->order_number }}</h3>
                            <p class="text-sm text-slate-500 mt-2">Dibuat pada: {{ $serviceOrder->created_at->format('d F Y, H:i') }}</p>
                        </div>
                        <div class="w-full md:w-auto text-left md:text-right">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Status Pengerjaan</p>
                            @if($serviceOrder->status === 'completed' || $serviceOrder->status === 'selesai')
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Selesai (Completed)
                                </span>
                            @else
                                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200/80 shadow-sm inline-block w-full md:w-auto">
                                    <form action="{{ route('admin.service-order.update-status', $serviceOrder->id) }}" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2" onsubmit="return handleDirectStatusUpdate(event)">
                                        @csrf
                                        @method('PATCH')
                                        <div class="relative flex-1">
                                            <select name="status" id="quick_status_select" class="w-full px-3.5 py-2 text-xs sm:text-sm font-bold rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] shadow-xs transition-all cursor-pointer">
                                                <option value="pending" {{ $serviceOrder->status === 'pending' ? 'selected' : '' }}>🟡 Menunggu (Pending)</option>
                                                <option value="in_progress" {{ $serviceOrder->status === 'in_progress' ? 'selected' : '' }}>🔵 Sedang Dikerjakan (In Progress)</option>
                                                <option value="completed" {{ $serviceOrder->status === 'completed' ? 'selected' : '' }}>🟢 Selesai (Completed)</option>
                                                <option value="cancelled" {{ $serviceOrder->status === 'cancelled' ? 'selected' : '' }}>🔴 Dibatalkan (Cancelled)</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="px-4 py-2 bg-[#FF6B00] hover:bg-orange-600 active:bg-orange-700 text-white rounded-xl font-bold text-xs sm:text-sm shadow-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            <span>Update Status</span>
                                        </button>
                                    </form>
                                </div>
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

    <x-slot name="scripts">
        <script>
            function handleDirectStatusUpdate(e) {
                const select = e.target.querySelector('select[name="status"]');
                const newStatus = select ? select.value : '';
                const currentStatus = '{{ $serviceOrder->status }}';
                const itemsCount = {{ $serviceOrder->items->count() }};

                if (newStatus === currentStatus) {
                    e.preventDefault();
                    alert('Status yang dipilih sama dengan status saat ini.');
                    return false;
                }

                if (newStatus === 'completed' && itemsCount === 0) {
                    e.preventDefault();
                    alert('Tidak dapat mengubah status menjadi Selesai: Pesanan belum memiliki item jasa atau suku cadang.\n\nSilakan klik tombol "Ubah Status / Mekanik" di bagian atas untuk menambahkan item pengerjaan terlebih dahulu.');
                    return false;
                }

                if (newStatus === 'completed') {
                    return confirm('Apakah Anda yakin ingin menyelesaikan pesanan ini?\n\nSetelah berstatus SELESAI, data pesanan dan rincian item akan dikunci dan siap untuk diterbitkan invoice.');
                }

                if (newStatus === 'cancelled') {
                    return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok suku cadang (jika ada) akan dikembalikan.');
                }

                return true;
            }
        </script>
    </x-slot>
</x-app-layout>
