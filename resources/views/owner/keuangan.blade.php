<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('Keuangan & Pendapatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-center">
                    <p class="text-slate-500 text-sm font-medium mb-2">Total Pendapatan (Lunas)</p>
                    <h3 class="text-4xl font-bold text-slate-800 font-heading text-emerald-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-center md:col-span-2">
                    <h3 class="text-lg font-bold text-slate-800 font-heading mb-2">Pantau Arus Kas</h3>
                    <p class="text-slate-500 text-sm">Semua riwayat transaksi dan pembayaran otomatis tercatat dari modul Invoice. Pantau kesehatan keuangan bengkel Anda dari sini.</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 font-heading">Invoice Terbaru</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-y border-slate-200">
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">No. Invoice</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Pelanggan</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentInvoices as $invoice)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-800">{{ $invoice->invoice_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-800">{{ $invoice->serviceOrder->vehicle->customer->name ?? '-' }}</div>
                                        <div class="text-xs text-slate-500">{{ $invoice->serviceOrder->vehicle->license_plate ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-800">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($invoice->payment_status === 'paid')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Lunas</span>
                                        @elseif($invoice->payment_status === 'unpaid')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Belum Bayar</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">{{ $invoice->payment_status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $invoice->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada invoice yang tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
