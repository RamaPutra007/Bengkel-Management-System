<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-3xl text-slate-800 tracking-tight font-heading">
                    Dashboard <span class="text-primary font-light">Owner</span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">Ringkasan bisnis bengkel hari ini, {{ now()->translatedFormat('d F Y') }}</p>
            </div>
            
            <div class="hidden sm:flex gap-3">
                <a href="{{ route('owner.laporan', ['export' => 'excel']) }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Unduh Laporan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <!-- Keuangan Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 p-8 rounded-3xl shadow-lg shadow-slate-900/20 text-white relative overflow-hidden group">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:bg-primary/20 transition-all duration-500"></div>
                <p class="text-slate-400 text-sm font-medium mb-1">Pendapatan Hari Ini</p>
                <h3 class="text-4xl font-bold font-heading mb-4">Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</h3>
                <div class="flex items-center gap-2 text-sm">
                    @if($stats['revenue_growth'] >= 0)
                        <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded-md text-xs font-semibold">+{{ $stats['revenue_growth'] }}%</span>
                    @else
                        <span class="bg-red-500/20 text-red-400 px-2 py-1 rounded-md text-xs font-semibold">{{ $stats['revenue_growth'] }}%</span>
                    @endif
                    <span class="text-slate-400">vs kemarin</span>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <p class="text-slate-500 text-sm font-medium">Pendapatan Minggu Ini</p>
                    <div class="p-2 bg-slate-50 rounded-lg text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 font-heading">Rp {{ number_format($stats['revenue_week'], 0, ',', '.') }}</h3>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <p class="text-slate-500 text-sm font-medium">Pendapatan Bulan Ini</p>
                    <div class="p-2 bg-slate-50 rounded-lg text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 font-heading">Rp {{ number_format($stats['revenue_month'], 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Operational Metrics -->
        <h3 class="text-lg font-bold text-slate-800 mb-4 font-heading">Metrik Operasional</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center text-center hover:-translate-y-1 transition-transform">
                <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h4 class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_customers']) }}</h4>
                <p class="text-xs text-slate-500 font-medium">Total Pelanggan</p>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center text-center hover:-translate-y-1 transition-transform">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </div>
                <h4 class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_vehicles']) }}</h4>
                <p class="text-xs text-slate-500 font-medium">Kendaraan Terdaftar</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center text-center hover:-translate-y-1 transition-transform">
                <div class="w-12 h-12 bg-orange-50 text-primary rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_service_orders']) }}</h4>
                <p class="text-xs text-slate-500 font-medium">Order Servis (Semua)</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center text-center hover:-translate-y-1 transition-transform">
                <div class="w-12 h-12 bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h4 class="text-2xl font-bold text-slate-800">{{ number_format($stats['low_stock']) }}</h4>
                <p class="text-xs text-slate-500 font-medium">Stok Suku Cadang Menipis</p>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800 font-heading">Booking Pelanggan Baru</h3>
                <a href="#" class="text-sm font-medium text-primary hover:underline">Lihat Semua</a>
            </div>
            
            @if($pending_bookings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-slate-500 text-xs uppercase tracking-wider">
                                <th class="p-4 font-medium border-y border-slate-100">Pelanggan</th>
                                <th class="p-4 font-medium border-y border-slate-100">Kendaraan</th>
                                <th class="p-4 font-medium border-y border-slate-100">Jadwal</th>
                                <th class="p-4 font-medium border-y border-slate-100">Keluhan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pending_bookings as $booking)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4 align-top">
                                    <p class="font-bold text-slate-800">{{ $booking->customer->name ?? '-' }}</p>
                                    <p class="text-xs text-slate-500">{{ $booking->customer->phone ?? '-' }}</p>
                                </td>
                                <td class="p-4 align-top">
                                    <p class="font-bold text-slate-700">{{ $booking->vehicle->license_plate ?? '-' }}</p>
                                    <p class="text-xs text-slate-500">{{ $booking->vehicle->brand ?? '' }} {{ $booking->vehicle->model ?? '' }}</p>
                                </td>
                                <td class="p-4 align-top">
                                    <p class="font-bold text-[#FF6B00] whitespace-nowrap">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</p>
                                    <p class="text-xs font-medium text-slate-600">{{ $booking->booking_time }}</p>
                                </td>
                                <td class="p-4 align-top">
                                    <div class="text-sm text-slate-600 line-clamp-2 w-48" title="{{ $booking->complaints }}">{{ $booking->complaints }}</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 flex flex-col items-center justify-center text-center bg-slate-50/50">
                    <div class="w-16 h-16 bg-white shadow-sm rounded-2xl flex items-center justify-center text-slate-300 mb-4 rotate-3 hover:rotate-0 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <h4 class="text-slate-700 font-semibold">Belum ada aktivitas terekam</h4>
                    <p class="text-slate-500 text-sm mt-1 max-w-sm">Aktivitas pendaftaran pelanggan, servis masuk, dan pembayaran akan muncul secara otomatis di sini.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
