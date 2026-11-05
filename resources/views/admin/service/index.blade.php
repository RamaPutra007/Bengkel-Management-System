<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Katalog Layanan Service') }}
            </h2>
            <div class="hidden sm:block text-xs text-slate-400 font-medium">
                Master Data Jasa & Servis
            </div>
        </div>
    </x-slot>

    <div class="w-full max-w-7xl mx-auto space-y-6">
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2 shadow-sm" role="alert">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="p-4 sm:p-6">
                <!-- Search & Actions: Stack on mobile, inline on desktop -->
                <div class="mb-6 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <form method="GET" action="{{ route('admin.service.index') }}" class="relative w-full sm:max-w-md">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama layanan..." class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] transition-colors text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </form>
                    <a href="{{ route('admin.service.create') }}" class="w-full sm:w-auto px-5 py-2.5 bg-[#FF6B00] text-white rounded-xl hover:bg-orange-600 transition-all font-bold text-sm flex items-center justify-center gap-2 shadow-sm shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Tambah Layanan</span>
                    </a>
                </div>

                <!-- Desktop Table View (Hidden on Mobile) -->
                <div class="hidden md:block overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Layanan</th>
                                <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Harga</th>
                                <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Estimasi Waktu</th>
                                <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($services as $service)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-slate-800 text-sm">{{ $service->name }}</div>
                                        <div class="text-xs text-slate-500 truncate max-w-sm" title="{{ $service->description }}">{{ $service->description ?? '-' }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-black text-slate-800 text-sm">Rp {{ number_format($service->price, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                        @if($service->estimated_time)
                                            <div class="flex items-center gap-1.5 text-xs font-medium">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $service->estimated_time }} Menit
                                            </div>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right text-sm">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.service.edit', $service->id) }}" class="p-1.5 text-slate-400 hover:text-[#FF6B00] hover:bg-orange-50 rounded-lg transition-colors" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <form action="{{ route('admin.service.destroy', $service->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <p class="text-sm font-semibold text-slate-600">Belum ada layanan yang ditambahkan</p>
                                            <p class="text-xs mt-1">Gunakan tombol "Tambah Layanan" untuk memasukkan katalog jasa service.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards View (Visible only on < md) -->
                <div class="md:hidden space-y-3">
                    @forelse ($services as $service)
                        <div class="p-4 bg-slate-50/70 rounded-xl border border-slate-200/80 space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm leading-tight">{{ $service->name }}</h4>
                                    @if($service->description)
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $service->description }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <a href="{{ route('admin.service.edit', $service->id) }}" class="p-1.5 text-slate-500 hover:text-[#FF6B00] bg-white border border-slate-200 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('admin.service.destroy', $service->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-500 bg-white border border-rose-200 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 text-xs">
                                <div class="text-slate-500">
                                    @if($service->estimated_time)
                                        ⏱️ {{ $service->estimated_time }} Menit
                                    @else
                                        ⏱️ -
                                    @endif
                                </div>
                                <div class="font-black text-[#FF6B00] text-sm">
                                    Rp {{ number_format($service->price, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs italic bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            Belum ada layanan yang ditambahkan.
                        </div>
                    @endforelse
                </div>

                @if($services->hasPages())
                    <div class="mt-6 border-t border-slate-100 pt-4">
                        {{ $services->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>