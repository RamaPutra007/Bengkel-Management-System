<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.mechanic.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Profil Mekanik') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-8">
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-6 border-b border-slate-100 pb-8 mb-8 text-center md:text-left">
                        <div class="h-24 w-24 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-4xl shrink-0">
                            {{ strtoupper(substr($mechanic->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow">
                            <div class="flex flex-col md:flex-row md:items-center gap-3 mb-2">
                                <h3 class="text-2xl font-bold text-slate-800">{{ $mechanic->name }}</h3>
                                <div>
                                    @if($mechanic->status === 'active')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    @elseif($mechanic->status === 'inactive')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Tidak Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Cuti / Sakit
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <p class="text-slate-500 font-medium mb-4">{{ $mechanic->specialization ?? 'Mekanik Umum' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div>
                            <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Informasi Kontak</h4>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-500 mb-1">Nomor Telepon / WA</p>
                                    <p class="text-base text-slate-800">{{ $mechanic->phone ?? 'Tidak ada data telepon' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-500 mb-1">Terdaftar Sejak</p>
                                    <p class="text-base text-slate-800">{{ $mechanic->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Kinerja / Riwayat</h4>
                            <div class="bg-slate-50 p-4 rounded-xl border border-dashed border-slate-200 text-center text-sm text-slate-500">
                                Modul Service Order sedang dikembangkan. Histori penugasan mekanik akan muncul di sini.
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                        <a href="{{ route('admin.mechanic.edit', $mechanic->id) }}" class="px-6 py-2.5 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors font-medium text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Profil
                        </a>
                        <form action="{{ route('admin.mechanic.destroy', $mechanic->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data mekanik ini?');">
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
