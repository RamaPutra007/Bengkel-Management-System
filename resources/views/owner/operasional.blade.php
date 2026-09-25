<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('Operasional & Servis') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Servis Berjalan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800 font-heading">Servis Berjalan (Hari Ini)</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($activeOrders as $order)
                            <div class="p-4 border border-slate-100 rounded-2xl bg-slate-50">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-bold text-slate-800">{{ $order->vehicle->license_plate ?? 'Unknown' }}</h4>
                                        <p class="text-sm text-slate-500">{{ $order->vehicle->customer->name ?? 'Unknown' }}</p>
                                    </div>
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $order->status == 'IN_PROGRESS' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ str_replace('_', ' ', $order->status) }}
                                    </span>
                                </div>
                                <div class="text-sm text-slate-600 mt-2">
                                    <span class="font-medium">Mekanik:</span> {{ $order->mechanic->name ?? 'Belum Ditentukan' }}
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-slate-500 py-6">Tidak ada servis yang sedang berjalan.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Booking Mendatang -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800 font-heading">Reservasi/Booking Baru</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($bookings as $booking)
                            <div class="p-4 border border-slate-100 rounded-2xl bg-slate-50">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-bold text-slate-800">{{ $booking->customer->name ?? '-' }} - {{ $booking->vehicle->license_plate ?? '-' }}</h4>
                                        <p class="text-sm text-slate-500">{{ $booking->vehicle->brand ?? '-' }} {{ $booking->vehicle->model ?? '-' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-[#FF6B00]">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</div>
                                        <div class="text-xs text-slate-500">{{ $booking->booking_time }}</div>
                                    </div>
                                </div>
                                <div class="text-sm text-slate-600 bg-white p-3 rounded-xl border border-slate-100 mt-2">
                                    "{{ $booking->complaints }}"
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-slate-500 py-6">Tidak ada reservasi baru.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
