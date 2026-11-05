<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.service.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors p-1 rounded-lg hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="font-bold text-lg sm:text-xl text-slate-800 leading-tight">
                        {{ __('Edit Layanan Service') }}: <span class="text-[#FF6B00]">{{ $service->name }}</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5 sm:hidden">Perbarui informasi tarif atau estimasi waktu pengerjaan</p>
                </div>
            </div>
            <div class="hidden sm:block text-xs text-slate-400 font-medium">
                Master Data Layanan
            </div>
        </div>
    </x-slot>

    <div class="w-full max-w-3xl mx-auto space-y-6 pb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="bg-slate-50/80 px-4 sm:px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base sm:text-lg font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-[#FF6B00] text-white text-xs font-bold flex items-center justify-center">1</span>
                    <span>Informasi Jasa Layanan</span>
                </h3>
            </div>
            <div class="p-4 sm:p-8">
                <form action="{{ route('admin.service.update', $service->id) }}" method="POST" class="space-y-5 sm:space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nama Layanan -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Layanan <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $service->name) }}" required class="w-full px-4 py-2.5 border @error('name') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] @enderror rounded-xl text-sm transition-all" placeholder="Contoh: Tune Up Mesin, Ganti Oli, Servis Rem, dll">
                        @error('name')
                            <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi / Rincian Pekerjaan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <textarea name="description" id="description" rows="3" class="w-full px-4 py-2.5 border @error('description') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] @enderror rounded-xl text-sm transition-all" placeholder="Jelaskan apa saja yang dikerjakan pada layanan ini...">{{ old('description', $service->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Grid: 1 col on mobile, 2 col on PC -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Harga -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-slate-700 mb-1.5">Harga Dasar (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <span class="text-slate-500 font-medium text-sm">Rp</span>
                                </div>
                                <input type="number" name="price" id="price" value="{{ old('price', $service->price) }}" required min="0" step="1000" class="w-full pl-11 pr-4 py-2.5 border @error('price') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] @enderror rounded-xl text-sm font-semibold text-slate-800 transition-all" placeholder="50000">
                            </div>
                            @error('price')
                                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Estimasi Waktu -->
                        <div>
                            <label for="estimated_time" class="block text-sm font-medium text-slate-700 mb-1.5">Estimasi Pengerjaan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <div class="relative">
                                <input type="number" name="estimated_time" id="estimated_time" value="{{ old('estimated_time', $service->estimated_time) }}" min="1" class="w-full pr-16 pl-4 py-2.5 border @error('estimated_time') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] @enderror rounded-xl text-sm transition-all" placeholder="30">
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                    <span class="text-slate-400 text-xs font-medium">Menit</span>
                                </div>
                            </div>
                            @error('estimated_time')
                                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons: Responsive stacked on mobile, right-aligned on PC -->
                    <div class="pt-5 border-t border-slate-100 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <a href="{{ route('admin.service.index') }}" class="w-full sm:w-auto px-6 py-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-colors font-semibold text-sm text-center">
                            Batal
                        </a>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-[#FF6B00] text-white rounded-xl hover:bg-orange-600 transition-all font-bold text-sm shadow-md flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>