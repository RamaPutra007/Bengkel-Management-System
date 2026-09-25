<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.vehicle.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Detail Kendaraan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Info Utama Kendaraan -->
                <div class="md:col-span-1">
                    <div class="bg-white p-6 shadow-sm sm:rounded-2xl border border-slate-100 text-center">
                        <div class="mx-auto rounded-xl bg-orange-100 text-[#FF6B00] font-bold text-2xl py-3 px-4 inline-block mb-4 border-2 border-orange-200 uppercase tracking-widest">
                            {{ $vehicle->license_plate }}
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-1">{{ $vehicle->brand }} {{ $vehicle->model }}</h3>
                        <p class="text-sm text-slate-500 mb-6">{{ $vehicle->color }} &bull; {{ $vehicle->year }}</p>
                        
                        <a href="{{ route('admin.vehicle.edit', $vehicle->id) }}" class="w-full flex justify-center items-center gap-2 px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors font-medium text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Kendaraan
                        </a>
                    </div>
                </div>

                <!-- Informasi Detail -->
                <div class="md:col-span-2">
                    <div class="bg-white p-8 shadow-sm sm:rounded-2xl border border-slate-100 mb-6">
                        <div class="flex justify-between items-center mb-4 pb-4 border-b border-slate-100">
                            <h4 class="text-lg font-bold text-slate-800">Pemilik Kendaraan</h4>
                            <a href="{{ route('admin.customer.show', $vehicle->customer_id) }}" class="text-sm font-medium text-[#FF6B00] hover:underline">
                                Lihat Profil Pelanggan &rarr;
                            </a>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xl">
                                {{ strtoupper(substr($vehicle->customer->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-base font-bold text-slate-800">{{ $vehicle->customer->name }}</p>
                                <p class="text-sm text-slate-500">{{ $vehicle->customer->phone }} | {{ $vehicle->customer->email ?? 'Tidak ada email' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-8 shadow-sm sm:rounded-2xl border border-slate-100">
                        <h4 class="text-lg font-bold text-slate-800 mb-4 pb-4 border-b border-slate-100">Spesifikasi Detail</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                            <div>
                                <p class="text-sm font-medium text-slate-500 mb-1">Merek</p>
                                <p class="text-base text-slate-800 font-medium">{{ $vehicle->brand ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-500 mb-1">Model / Tipe</p>
                                <p class="text-base text-slate-800 font-medium">{{ $vehicle->model ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-500 mb-1">Warna</p>
                                <p class="text-base text-slate-800">{{ $vehicle->color ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-500 mb-1">Tahun Pembuatan</p>
                                <p class="text-base text-slate-800">{{ $vehicle->year ?? '-' }}</p>
                            </div>
                            <div class="md:col-span-2 border-t border-slate-100 pt-4 mt-2">
                                <p class="text-sm font-medium text-slate-500 mb-1">Nomor Rangka</p>
                                <p class="text-base font-mono text-slate-800 tracking-wider">{{ $vehicle->chassis_number ?? '-' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm font-medium text-slate-500 mb-1">Nomor Mesin</p>
                                <p class="text-base font-mono text-slate-800 tracking-wider">{{ $vehicle->engine_number ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
