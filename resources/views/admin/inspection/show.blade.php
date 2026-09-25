<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.inspection.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    {{ __('Detail Inspeksi Kendaraan') }}
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.inspection.edit', $inspection->id) }}" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors font-medium text-sm">
                    Ubah Data
                </a>
                <button onclick="window.print()" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700 transition-colors font-medium text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Laporan
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm border border-slate-200 printable-area" id="inspection-doc">
                
                <!-- Document Header (Print Only) -->
                <div class="hidden print:block p-8 border-b border-slate-200 text-center">
                    <h1 class="text-3xl font-black text-slate-800 tracking-tight mb-2">SI BENGKEL</h1>
                    <h2 class="text-xl font-bold text-slate-600">LEMBAR HASIL INSPEKSI KENDARAAN</h2>
                </div>

                <div class="p-8">
                    <!-- Info Meta -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 pb-8 border-b border-slate-100">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Kendaraan & Pelanggan</p>
                            <p class="font-bold text-slate-800 text-lg uppercase">{{ $inspection->serviceOrder->vehicle->license_plate ?? '-' }}</p>
                            <p class="text-slate-600 text-sm mt-1">{{ $inspection->serviceOrder->vehicle->brand ?? '' }} {{ $inspection->serviceOrder->vehicle->model ?? '' }}</p>
                            <p class="text-slate-500 text-sm mt-2">Milik: <span class="font-medium text-slate-700">{{ $inspection->serviceOrder->customer->name ?? '-' }}</span></p>
                        </div>
                        <div class="md:text-right">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Informasi Pekerjaan</p>
                            <p class="font-bold text-[#FF6B00] text-lg">{{ $inspection->serviceOrder->order_number }}</p>
                            <p class="text-slate-500 text-sm mt-1">Tgl Inspeksi: {{ $inspection->created_at->format('d M Y, H:i') }}</p>
                            <p class="text-slate-500 text-sm mt-2">Mekanik: <span class="font-medium text-slate-700">{{ $inspection->serviceOrder->mechanic->name ?? '-' }}</span></p>
                        </div>
                    </div>

                    <!-- Inspection Contents -->
                    <div class="space-y-6">
                        <!-- Customer Complaint -->
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                </div>
                                <h3 class="font-bold text-slate-800 text-lg">Keluhan Pelanggan</h3>
                            </div>
                            <div class="pl-10">
                                <div class="p-4 bg-slate-50 rounded-xl text-slate-700 border border-slate-100 whitespace-pre-wrap">{{ $inspection->customer_complaint ?: 'Tidak ada keluhan tercatat.' }}</div>
                            </div>
                        </div>

                        <!-- Mechanic Notes -->
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                </div>
                                <h3 class="font-bold text-slate-800 text-lg">Temuan & Catatan Mekanik</h3>
                            </div>
                            <div class="pl-10">
                                <div class="p-4 bg-amber-50/50 rounded-xl text-slate-800 font-medium border border-amber-100 whitespace-pre-wrap">{{ $inspection->mechanic_notes }}</div>
                            </div>
                        </div>

                        <!-- Recommendations -->
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <h3 class="font-bold text-slate-800 text-lg">Rekomendasi Tindak Lanjut</h3>
                            </div>
                            <div class="pl-10">
                                <div class="p-4 bg-blue-50/50 rounded-xl text-slate-800 border border-blue-100 whitespace-pre-wrap">{{ $inspection->recommendations ?: 'Tidak ada rekomendasi khusus.' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Signatures (Print Only) -->
                    <div class="hidden print:flex justify-between mt-16 pt-8">
                        <div class="text-center w-48">
                            <p class="text-sm text-slate-500 mb-16">Pelanggan,</p>
                            <div class="border-b border-slate-800"></div>
                            <p class="text-sm font-bold mt-2">{{ $inspection->serviceOrder->customer->name ?? '-' }}</p>
                        </div>
                        <div class="text-center w-48">
                            <p class="text-sm text-slate-500 mb-16">Mekanik Pemeriksa,</p>
                            <div class="border-b border-slate-800"></div>
                            <p class="text-sm font-bold mt-2">{{ $inspection->serviceOrder->mechanic->name ?? '-' }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .printable-area, .printable-area * {
                visibility: visible;
            }
            .printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: none !important;
                box-shadow: none !important;
            }
            @page { margin: 0; }
            body { margin: 1.6cm; }
        }
    </style>
</x-app-layout>
