<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('Dashboard') }} <span class="text-[#FF6B00]">Kasir</span>
        </h2>
    </x-slot>

    <div class="py-6">
        <!-- Stats Overview Cards -->
        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Overview Keuangan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Transaksi Hari Ini</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats['transaksi_hari_ini']) }}</h3>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
                <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Pendapatan Hari Ini</p>
                    <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($stats['pendapatan_hari_ini'], 0, ',', '.') }}</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
                <div class="p-3 bg-orange-50 text-[#FF6B00] rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Invoice Belum Dibayar</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats['invoice_belum_dibayar']) }}</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Invoice Lunas</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats['invoice_sudah_dibayar']) }}</h3>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="bg-white overflow-hidden shadow-sm border border-slate-100 sm:rounded-2xl">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Transaksi Terbaru</h3>
                    <p class="text-slate-500 text-sm mt-1">Daftar transaksi dan pembayaran yang baru saja terjadi.</p>
                </div>
                <a href="{{ route('kasir.transaction-history.index') }}" class="text-sm font-medium text-primary hover:underline">Lihat Semua</a>
            </div>
            
            <div class="p-8 bg-slate-50/50 flex flex-col items-center justify-center text-center border-t border-slate-100 min-h-[250px]">
                <div class="w-16 h-16 bg-white shadow-sm rounded-2xl flex items-center justify-center text-slate-300 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <h4 class="text-lg font-bold text-slate-700">Tidak ada transaksi</h4>
                <p class="text-slate-500 max-w-sm mt-2 text-sm">Belum ada service order yang sudah selesai dan diproses pembayarannya hari ini.</p>
            </div>
        </div>
    </div>
</x-app-layout>
