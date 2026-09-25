<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.mechanic.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Edit Data Mekanik') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-8">
                    <form action="{{ route('admin.mechanic.update', $mechanic->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Nama -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $mechanic->name) }}" required class="w-full px-4 py-2 border @error('name') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-orange-500/20 focus:border-[#FF6B00] @enderror rounded-lg focus:outline-none focus:ring-2 transition-colors text-sm" placeholder="Masukkan nama mekanik">
                            @error('name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Telepon -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Nomor Telepon/WA <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $mechanic->phone) }}" class="w-full px-4 py-2 border @error('phone') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-orange-500/20 focus:border-[#FF6B00] @enderror rounded-lg focus:outline-none focus:ring-2 transition-colors text-sm" placeholder="08xxxxxxxxx">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Spesialisasi -->
                            <div>
                                <label for="specialization" class="block text-sm font-medium text-slate-700 mb-1">Spesialisasi <span class="text-slate-400 font-normal">(Opsional)</span></label>
                                <input type="text" name="specialization" id="specialization" value="{{ old('specialization', $mechanic->specialization) }}" class="w-full px-4 py-2 border @error('specialization') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-orange-500/20 focus:border-[#FF6B00] @enderror rounded-lg focus:outline-none focus:ring-2 transition-colors text-sm" placeholder="Contoh: Mesin, Kelistrikan, Umum">
                                @error('specialization')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status Ketersediaan <span class="text-red-500">*</span></label>
                                <select name="status" id="status" required class="w-full px-4 py-2 border @error('status') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-orange-500/20 focus:border-[#FF6B00] @enderror rounded-lg focus:outline-none focus:ring-2 transition-colors text-sm bg-white">
                                    <option value="active" {{ old('status', $mechanic->status) == 'active' ? 'selected' : '' }}>Aktif Bekerja</option>
                                    <option value="on_leave" {{ old('status', $mechanic->status) == 'on_leave' ? 'selected' : '' }}>Cuti / Sakit</option>
                                    <option value="inactive" {{ old('status', $mechanic->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif / Resign</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                            <a href="{{ route('admin.mechanic.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition-colors font-medium text-sm">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2.5 bg-[#FF6B00] text-white rounded-lg hover:bg-orange-600 transition-colors font-medium text-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
