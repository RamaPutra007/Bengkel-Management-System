<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ $title ?? 'Fitur' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-12 text-slate-900 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 bg-orange-50 text-[#FF6B00] rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-2">Modul Sedang Dikembangkan</h3>
                    <p class="text-slate-500 max-w-md">Fitur manajemen <span class="font-bold">{{ $title ?? '' }}</span> sedang dalam tahap pengembangan. Silakan periksa kembali nanti.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
