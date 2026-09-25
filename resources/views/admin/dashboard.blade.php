<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('Dashboard') }} <span class="text-[#FF6B00]">Admin</span>
        </h2>
    </x-slot>

        <!-- Stats Overview Cards -->
        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Overview Operasional</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Customer</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_customers']) }}</h3>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Kendaraan</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_vehicles']) }}</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
                <div class="p-3 bg-orange-50 text-[#FF6B00] rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Service Order Aktif</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats['service_order_aktif']) }}</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
                <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Service Selesai</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats['service_order_selesai']) }}</h3>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
                <div class="p-3 bg-teal-50 text-teal-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Mekanik</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_mechanics']) }}</h3>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
                <div class="p-3 bg-slate-100 text-slate-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Spare Part</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_spare_parts']) }}</h3>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
                <div class="p-3 bg-red-50 text-red-500 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Stok Menipis</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats['stok_menipis']) }}</h3>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="bg-white overflow-hidden shadow-sm border border-slate-100 sm:rounded-2xl">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Booking Perlu Diproses</h3>
                    <p class="text-slate-500 text-sm mt-1">Daftar booking pelanggan yang belum ditangani.</p>
                </div>
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
                                <th class="p-4 font-medium border-y border-slate-100 text-right">Aksi</th>
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
                                <td class="p-4 text-right align-top">
                                    <form action="{{ route('admin.booking.accept', $booking->id) }}" method="POST" class="flex justify-end">
                                        @csrf
                                        <button type="submit" class="bg-[#FF6B00] hover:bg-orange-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors shadow-sm shadow-[#FF6B00]/20 inline-flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Terima
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 bg-slate-50/50 flex flex-col items-center justify-center text-center border-t border-slate-100 min-h-[250px]">
                    <div class="w-16 h-16 bg-white shadow-sm rounded-2xl flex items-center justify-center text-slate-300 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-700">Tidak ada booking pending</h4>
                    <p class="text-slate-500 max-w-sm mt-2 text-sm">Semua permintaan layanan saat ini telah terproses atau masuk dalam sistem operasional.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
