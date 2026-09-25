<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.sparepart.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Detail Suku Cadang') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-8">
                    <!-- Title Area -->
                    <div class="flex flex-col md:flex-row items-start justify-between mb-8 pb-6 border-b border-slate-100 gap-6">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-3xl font-bold text-slate-800">{{ $sparepart->name }}</h3>
                                @if($sparepart->stock <= $sparepart->reorder_level)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 uppercase tracking-widest border border-red-200">
                                        Stok Rendah!
                                    </span>
                                @endif
                            </div>
                            <p class="text-slate-500 font-mono text-lg mb-2">Part Number: <span class="font-bold text-slate-700">{{ $sparepart->part_number }}</span></p>
                            <p class="text-sm font-medium px-3 py-1 bg-slate-100 text-slate-600 rounded-lg inline-block">{{ $sparepart->brand ?? 'Generic/Unbranded' }}</p>
                        </div>
                        <div class="text-left md:text-right shrink-0">
                            <p class="text-sm text-slate-500 font-medium mb-1">Harga Jual</p>
                            <p class="text-3xl font-bold text-[#FF6B00]">Rp {{ number_format($sparepart->price, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-8">
                        <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Deskripsi Suku Cadang</h4>
                        <p class="text-slate-700 bg-slate-50 p-4 rounded-xl border border-slate-100">
                            {{ $sparepart->description ?? 'Tidak ada deskripsi yang ditambahkan untuk produk ini.' }}
                        </p>
                    </div>

                    <!-- Inventory Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div class="flex items-center gap-4 p-5 rounded-xl border {{ $sparepart->stock <= $sparepart->reorder_level ? 'bg-red-50 border-red-200' : 'bg-emerald-50 border-emerald-200' }}">
                            <div class="h-14 w-14 rounded-full bg-white flex items-center justify-center {{ $sparepart->stock <= $sparepart->reorder_level ? 'text-red-500' : 'text-emerald-500' }} shadow-sm">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium {{ $sparepart->stock <= $sparepart->reorder_level ? 'text-red-800' : 'text-emerald-800' }}">Stok Fisik Tersedia</p>
                                <p class="text-3xl font-bold {{ $sparepart->stock <= $sparepart->reorder_level ? 'text-red-900' : 'text-emerald-900' }}">{{ $sparepart->stock }} <span class="text-sm font-normal">Pcs</span></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 p-5 rounded-xl bg-amber-50 border border-amber-200">
                            <div class="h-14 w-14 rounded-full bg-white flex items-center justify-center text-amber-500 shadow-sm">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-amber-800">Batas Minimal Peringatan (Reorder)</p>
                                <p class="text-3xl font-bold text-amber-900">{{ $sparepart->reorder_level }} <span class="text-sm font-normal">Pcs</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                        <a href="{{ route('admin.sparepart.edit', $sparepart->id) }}" class="px-6 py-2.5 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors font-medium text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Data
                        </a>
                        <form action="{{ route('admin.sparepart.destroy', $sparepart->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus suku cadang ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-6 py-2.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors font-medium text-sm flex items-center gap-2 border border-red-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
