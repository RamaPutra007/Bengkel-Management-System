<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.inspection.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Ubah Laporan Inspeksi') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-8">
                    
                    <div class="mb-8 flex justify-between items-center bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <div>
                            <p class="text-sm text-slate-500 font-medium mb-1">Service Order</p>
                            <h3 class="text-xl font-bold text-[#FF6B00]">{{ $inspection->serviceOrder->order_number }}</h3>
                        </div>
                    </div>

                    <form action="{{ route('admin.inspection.update', $inspection->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="customer_complaint" class="block text-sm font-medium text-slate-700 mb-1">Keluhan Pelanggan</label>
                            <textarea name="customer_complaint" id="customer_complaint" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-[#FF6B00] focus:border-[#FF6B00] text-sm">{{ old('customer_complaint', $inspection->customer_complaint) }}</textarea>
                            @error('customer_complaint')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mechanic_notes" class="block text-sm font-medium text-slate-700 mb-1">Catatan & Temuan Mekanik <span class="text-red-500">*</span></label>
                            <textarea name="mechanic_notes" id="mechanic_notes" rows="4" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-[#FF6B00] focus:border-[#FF6B00] text-sm bg-amber-50/30">{{ old('mechanic_notes', $inspection->mechanic_notes) }}</textarea>
                            @error('mechanic_notes')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="recommendations" class="block text-sm font-medium text-slate-700 mb-1">Rekomendasi Servis Selanjutnya</label>
                            <textarea name="recommendations" id="recommendations" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-[#FF6B00] focus:border-[#FF6B00] text-sm bg-blue-50/30">{{ old('recommendations', $inspection->recommendations) }}</textarea>
                            @error('recommendations')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                            <a href="{{ route('admin.inspection.show', $inspection->id) }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 font-medium text-sm">
                                Batal
                            </a>
                            <button type="submit" class="px-8 py-2.5 bg-[#FF6B00] text-white rounded-lg hover:bg-orange-600 font-bold text-sm shadow-md">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
