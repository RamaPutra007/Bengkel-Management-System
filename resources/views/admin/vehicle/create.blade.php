<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.vehicle.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Tambah Kendaraan Baru') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-8">
                    <form action="{{ route('admin.vehicle.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Pelanggan / Pemilik -->
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-slate-700 mb-1">Pemilik Kendaraan <span class="text-red-500">*</span></label>
                            <select name="customer_id" id="customer_id" required class="w-full px-4 py-2 border @error('customer_id') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-orange-500/20 focus:border-[#FF6B00] @enderror rounded-lg focus:outline-none focus:ring-2 transition-colors text-sm bg-white">
                                <option value="" disabled selected>-- Pilih Pelanggan --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', request('customer_id')) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->phone }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-slate-500">Belum ada di list? <a href="{{ route('admin.customer.create') }}" class="text-[#FF6B00] hover:underline">Tambah Pelanggan Baru</a></p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-slate-100 pt-6">
                            <!-- Nomor Polisi -->
                            <div>
                                <label for="license_plate" class="block text-sm font-medium text-slate-700 mb-1">Nomor Polisi <span class="text-red-500">*</span></label>
                                <input type="text" name="license_plate" id="license_plate" value="{{ old('license_plate') }}" required class="w-full px-4 py-2 border @error('license_plate') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-orange-500/20 focus:border-[#FF6B00] @enderror rounded-lg focus:outline-none focus:ring-2 transition-colors text-sm uppercase" placeholder="B 1234 ABC">
                                @error('license_plate')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Merk -->
                            <div>
                                <label for="brand" class="block text-sm font-medium text-slate-700 mb-1">Merek <span class="text-slate-400 font-normal">(Opsional)</span></label>
                                <input type="text" name="brand" id="brand" value="{{ old('brand') }}" class="w-full px-4 py-2 border @error('brand') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-orange-500/20 focus:border-[#FF6B00] @enderror rounded-lg focus:outline-none focus:ring-2 transition-colors text-sm" placeholder="Contoh: Honda, Toyota">
                                @error('brand')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Model -->
                            <div>
                                <label for="model" class="block text-sm font-medium text-slate-700 mb-1">Model / Tipe <span class="text-slate-400 font-normal">(Opsional)</span></label>
                                <input type="text" name="model" id="model" value="{{ old('model') }}" class="w-full px-4 py-2 border @error('model') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-orange-500/20 focus:border-[#FF6B00] @enderror rounded-lg focus:outline-none focus:ring-2 transition-colors text-sm" placeholder="Contoh: Vario 150, Avanza G">
                                @error('model')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tahun -->
                            <div>
                                <label for="year" class="block text-sm font-medium text-slate-700 mb-1">Tahun Perakitan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                                <input type="text" name="year" id="year" value="{{ old('year') }}" class="w-full px-4 py-2 border @error('year') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-orange-500/20 focus:border-[#FF6B00] @enderror rounded-lg focus:outline-none focus:ring-2 transition-colors text-sm" placeholder="Contoh: 2021">
                                @error('year')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Warna -->
                            <div>
                                <label for="color" class="block text-sm font-medium text-slate-700 mb-1">Warna <span class="text-slate-400 font-normal">(Opsional)</span></label>
                                <input type="text" name="color" id="color" value="{{ old('color') }}" class="w-full px-4 py-2 border @error('color') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-orange-500/20 focus:border-[#FF6B00] @enderror rounded-lg focus:outline-none focus:ring-2 transition-colors text-sm" placeholder="Contoh: Hitam, Putih">
                                @error('color')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-slate-100 pt-6">
                            <!-- Nomor Rangka -->
                            <div>
                                <label for="chassis_number" class="block text-sm font-medium text-slate-700 mb-1">Nomor Rangka <span class="text-slate-400 font-normal">(Opsional)</span></label>
                                <input type="text" name="chassis_number" id="chassis_number" value="{{ old('chassis_number') }}" class="w-full px-4 py-2 border @error('chassis_number') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-orange-500/20 focus:border-[#FF6B00] @enderror rounded-lg focus:outline-none focus:ring-2 transition-colors text-sm uppercase" placeholder="Nomor rangka kendaraan">
                                @error('chassis_number')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nomor Mesin -->
                            <div>
                                <label for="engine_number" class="block text-sm font-medium text-slate-700 mb-1">Nomor Mesin <span class="text-slate-400 font-normal">(Opsional)</span></label>
                                <input type="text" name="engine_number" id="engine_number" value="{{ old('engine_number') }}" class="w-full px-4 py-2 border @error('engine_number') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-orange-500/20 focus:border-[#FF6B00] @enderror rounded-lg focus:outline-none focus:ring-2 transition-colors text-sm uppercase" placeholder="Nomor mesin kendaraan">
                                @error('engine_number')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                            <a href="{{ route('admin.vehicle.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition-colors font-medium text-sm">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2.5 bg-[#FF6B00] text-white rounded-lg hover:bg-orange-600 transition-colors font-medium text-sm">
                                Simpan Kendaraan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
