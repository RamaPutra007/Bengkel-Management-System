<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.inventory-transaction.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Catat Pergerakan Stok Manual') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-8">
                    
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-xl text-blue-800 text-sm">
                        <p class="font-bold mb-1">Informasi:</p>
                        <p>Catatan stok keluar akibat <strong>Service Order (Transaksi Kasir)</strong> akan dicatat secara otomatis oleh sistem, Anda tidak perlu mencatatnya di sini kecuali untuk keperluan retur atau penyesuaian (opname).</p>
                    </div>

                    <form action="{{ route('admin.inventory-transaction.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <label for="sparepart_id" class="block text-sm font-medium text-slate-700 mb-1">Pilih Suku Cadang <span class="text-red-500">*</span></label>
                            <select name="sparepart_id" id="sparepart_id" required class="w-full px-4 py-2 border @error('sparepart_id') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-orange-500/20 focus:border-[#FF6B00] @enderror rounded-lg focus:outline-none focus:ring-2 transition-colors text-sm bg-white">
                                <option value="">-- Pilih Barang --</option>
                                @foreach($spareparts as $part)
                                    <option value="{{ $part->id }}" {{ old('sparepart_id') == $part->id ? 'selected' : '' }}>
                                        [{{ $part->part_number }}] {{ $part->name }} (Stok saat ini: {{ $part->stock }})
                                    </option>
                                @endforeach
                            </select>
                            @error('sparepart_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-slate-700 mb-1">Jenis Pergerakan <span class="text-red-500">*</span></label>
                                <select name="type" id="type" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-[#FF6B00] bg-white text-sm">
                                    <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Stok Masuk (+)</option>
                                    <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Stok Keluar (-)</option>
                                    <option value="adjustment" {{ old('type') == 'adjustment' ? 'selected' : '' }}>Penyesuaian Hilang/Rusak (-)</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="quantity" class="block text-sm font-medium text-slate-700 mb-1">Kuantitas <span class="text-red-500">*</span></label>
                                <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-[#FF6B00] text-sm">
                                @error('quantity')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Catatan / Alasan</label>
                            <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-[#FF6B00] text-sm" placeholder="Contoh: Pembelian dari Supplier A / Barang cacat produksi">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                            <a href="{{ route('admin.inventory-transaction.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 font-medium text-sm">
                                Batal
                            </a>
                            <button type="submit" class="px-8 py-2.5 bg-[#FF6B00] text-white rounded-lg hover:bg-orange-600 font-bold text-sm shadow-md">
                                Simpan Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
