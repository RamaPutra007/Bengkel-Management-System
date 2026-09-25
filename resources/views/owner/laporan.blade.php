<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('Laporan Kinerja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100 p-8 mb-8">
                <form method="GET" action="{{ route('owner.laporan') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                    <div class="w-full sm:w-1/3">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Bulan</label>
                        <select name="month" class="w-full rounded-xl border-slate-200 focus:border-[#FF6B00] focus:ring-[#FF6B00]">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="w-full sm:w-1/3">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tahun</label>
                        <select name="year" class="w-full rounded-xl border-slate-200 focus:border-[#FF6B00] focus:ring-[#FF6B00]">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="w-full sm:w-1/3 flex gap-2">
                        <button type="submit" class="w-full bg-[#FF6B00] hover:bg-orange-600 text-white font-bold py-2.5 px-4 rounded-xl transition-colors">
                            Filter
                        </button>
                        <button type="submit" name="export" value="excel" class="w-full border border-emerald-500 text-emerald-600 bg-emerald-50 hover:bg-emerald-100 font-bold py-2.5 px-4 rounded-xl transition-colors flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Excel
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                    <p class="text-slate-500 text-sm font-medium mb-1">Total Pendapatan (Bulan Ini)</p>
                    <h3 class="text-3xl font-bold text-emerald-600 mb-4">Rp {{ number_format($stats['total_income'], 0, ',', '.') }}</h3>
                    
                    <div class="space-y-2 border-t border-slate-100 pt-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500 font-medium">Tunai / Cash</span>
                            <span class="font-bold text-slate-700">Rp {{ number_format($stats['cash_income'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500 font-medium">QRIS</span>
                            <span class="font-bold text-[#FF6B00]">Rp {{ number_format($stats['qris_income'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                    <p class="text-slate-500 text-sm font-medium mb-1">Servis Selesai</p>
                    <h3 class="text-3xl font-bold text-slate-800">{{ number_format($stats['total_services']) }} Unit</h3>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                    <p class="text-slate-500 text-sm font-medium mb-1">Suku Cadang Terpakai</p>
                    <h3 class="text-3xl font-bold text-slate-800">{{ number_format($stats['sparepart_usage']) }} Item</h3>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
