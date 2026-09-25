<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('Inventaris') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Low Stock Alert -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-red-100">
                    <div class="p-6 border-b border-red-100 bg-red-50/30">
                        <h3 class="text-lg font-bold text-red-700 font-heading flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Peringatan Stok Menipis
                        </h3>
                    </div>
                    <div class="p-0">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-y border-slate-100">
                                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Item</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Stok</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Batas Minimum</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($lowStockSpareparts as $sparepart)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-800">{{ $sparepart->name }}</div>
                                        <div class="text-xs text-slate-500">PN: {{ $sparepart->part_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="font-bold text-red-600 bg-red-100 px-2.5 py-1 rounded-md">{{ $sparepart->stock }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-slate-600 font-medium">
                                        {{ $sparepart->reorder_level }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-slate-500">
                                        Tidak ada peringatan. Stok dalam kondisi aman.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800 font-heading">Pergerakan Suku Cadang Terakhir</h3>
                    </div>
                    <div class="p-0">
                        <ul class="divide-y divide-slate-100">
                            @forelse($recentTransactions as $transaction)
                            <li class="p-4 hover:bg-slate-50 transition-colors flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm {{ $transaction->type === 'IN' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $transaction->type === 'IN' ? '+' : '-' }}{{ $transaction->quantity }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">{{ $transaction->sparepart->name ?? 'Terhapus' }}</p>
                                        <p class="text-xs text-slate-500">{{ $transaction->reference_number ?? 'Manual' }} • {{ $transaction->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-xs font-medium text-slate-400 bg-white px-2 py-1 rounded border border-slate-200">
                                    Oleh: {{ explode(' ', $transaction->user->name)[0] ?? 'Sistem' }}
                                </div>
                            </li>
                            @empty
                            <li class="p-8 text-center text-slate-500">Belum ada pergerakan stok.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
