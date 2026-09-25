<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.customer.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Detail Pelanggan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Profil Singkat -->
                <div class="md:col-span-1">
                    <div class="bg-white p-6 shadow-sm sm:rounded-2xl border border-slate-100 text-center">
                        <div class="h-24 w-24 mx-auto rounded-full bg-orange-100 text-[#FF6B00] flex items-center justify-center font-bold text-4xl mb-4">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-1">{{ $customer->name }}</h3>
                        <p class="text-sm text-slate-500 mb-6">Pelanggan sejak {{ $customer->created_at->format('M Y') }}</p>
                        
                        <a href="{{ route('admin.customer.edit', $customer->id) }}" class="w-full flex justify-center items-center gap-2 px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors font-medium text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Profil
                        </a>
                    </div>
                </div>

                <!-- Informasi Detail -->
                <div class="md:col-span-2">
                    <div class="bg-white p-8 shadow-sm sm:rounded-2xl border border-slate-100 mb-6">
                        <h4 class="text-lg font-bold text-slate-800 mb-4 pb-4 border-b border-slate-100">Informasi Kontak</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                            <div>
                                <p class="text-sm font-medium text-slate-500 mb-1">Nomor Telepon / WA</p>
                                <p class="text-base text-slate-800">{{ $customer->phone }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-500 mb-1">Alamat Email</p>
                                <p class="text-base text-slate-800">{{ $customer->email ?? '-' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm font-medium text-slate-500 mb-1">Alamat Lengkap</p>
                                <p class="text-base text-slate-800">{{ $customer->address ?? '-' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm font-medium text-slate-500 mb-1">Catatan</p>
                                <p class="text-base text-slate-800 bg-slate-50 p-3 rounded-lg border border-slate-100">{{ $customer->notes ?? 'Tidak ada catatan khusus.' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Kendaraan -->
                    <div class="bg-white p-8 shadow-sm sm:rounded-2xl border border-slate-100">
                        <div class="flex justify-between items-center mb-4 pb-4 border-b border-slate-100">
                            <h4 class="text-lg font-bold text-slate-800">Daftar Kendaraan</h4>
                            <a href="{{ route('admin.vehicle.create', ['customer_id' => $customer->id]) }}" class="text-sm font-medium text-[#FF6B00] hover:text-orange-700">+ Tambah Kendaraan</a>
                        </div>
                        
                        <!-- List Kendaraan -->
                        <div class="space-y-4">
                            @forelse ($customer->vehicles as $vehicle)
                                <div class="flex justify-between items-center p-4 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="font-bold text-slate-800 bg-slate-100 px-3 py-1.5 rounded border border-slate-200 uppercase">
                                            {{ $vehicle->license_plate }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                                            <p class="text-sm text-slate-500">{{ $vehicle->color }} &bull; {{ $vehicle->year }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.vehicle.show', $vehicle->id) }}" class="text-sm font-medium text-[#FF6B00] hover:bg-orange-50 px-3 py-1.5 rounded-lg transition-colors">
                                        Detail &rarr;
                                    </a>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-500 text-sm bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                    Pelanggan ini belum mendaftarkan kendaraan apa pun.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
