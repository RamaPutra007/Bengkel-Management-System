<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Service Orders (Transaksi)') }}
            </h2>
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('warning'))
            <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-sm font-medium flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="p-4 sm:p-6 lg:p-8">
                <!-- Search Bar & Actions -->
                <div class="mb-6 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4">
                    <form method="GET" action="{{ route('admin.service-order.index') }}" class="relative w-full sm:max-w-md lg:max-w-lg">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor order atau nama pelanggan..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] transition-all">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </form>
                    <a href="{{ route('admin.service-order.create') }}" class="w-full sm:w-auto px-5 py-2.5 bg-[#FF6B00] hover:bg-orange-600 text-white rounded-xl transition-all font-bold text-sm flex items-center justify-center gap-2 shadow-sm shadow-orange-500/20 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        <span>Buat Order Baru</span>
                    </a>
                </div>

                <!-- DESKTOP / TABLET VIEW: DATA TABLE (>= 768px) -->
                <div class="hidden md:block overflow-x-auto border border-slate-200/80 rounded-xl">
                    <table class="w-full text-left border-collapse min-w-[700px]">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200">
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">No. Order / Waktu</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Pelanggan & Kendaraan</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Mekanik</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Total Harga</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($serviceOrders as $order)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.service-order.show', $order->id) }}" class="font-bold text-base text-[#FF6B00] hover:text-orange-600 hover:underline">
                                            {{ $order->order_number }}
                                        </a>
                                        <div class="text-xs text-slate-400 font-medium mt-0.5">
                                            {{ $order->created_at->format('d M Y, H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-800 text-sm sm:text-base">{{ $order->customer->name ?? '-' }}</div>
                                        <div class="text-xs text-slate-500 font-medium mt-0.5">
                                            {{ $order->vehicle->brand ?? '' }} {{ $order->vehicle->model ?? '' }}
                                            @if(!empty($order->vehicle->license_plate))
                                                <span class="text-slate-600 font-semibold">({{ $order->vehicle->license_plate }})</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-slate-700 font-medium">{{ $order->mechanic->name ?? 'Belum Ditentukan' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($order->status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Menunggu
                                            </span>
                                        @elseif($order->status === 'in_progress')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span> Diproses
                                            </span>
                                        @elseif($order->status === 'completed')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Selesai
                                            </span>
                                        @elseif($order->status === 'cancelled')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-2 h-2 rounded-full bg-rose-500"></span> Dibatalkan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-800 text-sm sm:text-base font-mono">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.service-order.show', $order->id) }}" class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Detail Order">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            @if($order->status !== 'completed' && $order->status !== 'selesai')
                                            <a href="{{ route('admin.service-order.edit', $order->id) }}" class="p-2 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-colors" title="Ubah Status">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-500">
                                            <svg class="w-12 h-12 mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <p class="text-base font-medium text-slate-600">Belum ada pesanan servis</p>
                                            <p class="text-sm mt-1">Gunakan tombol "Buat Order Baru" untuk memulai transaksi pengerjaan kendaraan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE VIEW: RESPONSIVE CARDS (< 768px) -->
                <div class="block md:hidden divide-y divide-slate-100">
                    @forelse ($serviceOrders as $order)
                        <div class="py-4 space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <a href="{{ route('admin.service-order.show', $order->id) }}" class="font-extrabold text-base text-[#FF6B00] hover:underline">
                                    {{ $order->order_number }}
                                </a>
                                <div>
                                    @if($order->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                                        </span>
                                    @elseif($order->status === 'in_progress')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span> Diproses
                                        </span>
                                    @elseif($order->status === 'completed')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Selesai
                                        </span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span> Dibatalkan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="bg-slate-50/80 rounded-xl p-3.5 space-y-2 border border-slate-100 text-xs">
                                <div class="flex justify-between items-baseline gap-2">
                                    <span class="text-slate-500 shrink-0">Pelanggan:</span>
                                    <span class="font-bold text-slate-800 text-right">{{ $order->customer->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between items-baseline gap-2">
                                    <span class="text-slate-500 shrink-0">Kendaraan:</span>
                                    <span class="font-medium text-slate-700 text-right">
                                        {{ $order->vehicle->brand ?? '' }} {{ $order->vehicle->model ?? '' }}
                                        @if(!empty($order->vehicle->license_plate))
                                            <span class="text-slate-800 font-semibold font-mono">({{ $order->vehicle->license_plate }})</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between items-baseline gap-2">
                                    <span class="text-slate-500 shrink-0">Mekanik:</span>
                                    <span class="font-medium text-slate-700 text-right">{{ $order->mechanic->name ?? 'Belum Ditentukan' }}</span>
                                </div>
                                <div class="flex justify-between items-baseline gap-2 pt-1.5 border-t border-slate-200/60">
                                    <span class="text-slate-400 shrink-0">Waktu:</span>
                                    <span class="text-slate-500 text-right">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                <div>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Total Biaya</span>
                                    <span class="text-base font-black text-slate-900 font-mono">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.service-order.show', $order->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span>Detail</span>
                                    </a>
                                    @if($order->status !== 'completed' && $order->status !== 'selesai')
                                    <a href="{{ route('admin.service-order.edit', $order->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-xs font-bold rounded-xl transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        <span>Ubah</span>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-500">
                                <svg class="w-12 h-12 mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-base font-medium text-slate-600">Belum ada pesanan servis</p>
                                <p class="text-sm mt-1">Gunakan tombol "Buat Order Baru" untuk memulai transaksi pengerjaan kendaraan.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if($serviceOrders->hasPages())
                    <div class="mt-6 border-t border-slate-100 pt-4">
                        {{ $serviceOrders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
