<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.service-order.show', $serviceOrder->id) }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Terbitkan Tagihan (Invoice)') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-8">
                    
                    <div class="mb-8 flex justify-between items-center bg-slate-50 p-6 rounded-xl border border-slate-200">
                        <div>
                            <p class="text-sm text-slate-500 font-medium mb-1">Berdasarkan Order Number</p>
                            <h3 class="text-2xl font-bold text-slate-800">{{ $serviceOrder->order_number }}</h3>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-slate-500 font-medium mb-1">Subtotal Jasa & Part</p>
                            <p class="text-2xl font-black text-[#FF6B00]">Rp <span id="display_subtotal">{{ number_format($serviceOrder->total_price, 0, ',', '.') }}</span></p>
                        </div>
                    </div>

                    <form action="{{ route('admin.invoice.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="service_order_id" value="{{ $serviceOrder->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="font-bold text-slate-700 mb-4 border-b border-slate-100 pb-2">Penyesuaian (Opsional)</h4>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="tax" class="block text-sm font-medium text-slate-700 mb-1">Pajak (%)</label>
                                        <input type="number" name="tax" id="tax" value="{{ old('tax', 0) }}" min="0" max="100" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-[#FF6B00] focus:border-[#FF6B00] text-sm calc-trigger">
                                    </div>
                                    <div>
                                        <label for="discount" class="block text-sm font-medium text-slate-700 mb-1">Diskon Potongan (%)</label>
                                        <input type="number" name="discount" id="discount" value="{{ old('discount', 0) }}" min="0" max="100" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-[#FF6B00] focus:border-[#FF6B00] text-sm calc-trigger">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 class="font-bold text-slate-700 mb-4 border-b border-slate-100 pb-2">Kalkulasi Akhir</h4>
                                
                                <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-slate-600">Subtotal Dasar:</span>
                                        <span class="font-medium">Rp {{ number_format($serviceOrder->total_price, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center mb-2 text-red-600">
                                        <span>+ Pajak Tambahan:</span>
                                        <span class="font-medium" id="display_tax">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between items-center mb-4 text-emerald-600">
                                        <span>- Potongan Diskon:</span>
                                        <span class="font-medium" id="display_discount">Rp 0</span>
                                    </div>
                                    <div class="pt-4 border-t border-blue-200 flex justify-between items-center">
                                        <span class="font-black text-slate-800 text-lg">GRAND TOTAL:</span>
                                        <span class="font-black text-blue-700 text-2xl" id="display_grand_total">Rp {{ number_format($serviceOrder->total_price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Catatan Tambahan untuk Invoice</label>
                            <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-[#FF6B00] focus:border-[#FF6B00] text-sm" placeholder="Contoh: Pembayaran tempo 14 hari..."></textarea>
                        </div>

                        <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                            <a href="{{ route('admin.service-order.show', $serviceOrder->id) }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 font-medium">
                                Batal
                            </a>
                            <button type="submit" class="px-8 py-2.5 bg-[#FF6B00] text-white rounded-lg hover:bg-orange-600 font-bold shadow-md">
                                Terbitkan Invoice Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const subtotal = {{ $serviceOrder->total_price }};
                const taxInput = document.getElementById('tax');
                const discountInput = document.getElementById('discount');
                
                const dispTax = document.getElementById('display_tax');
                const dispDiscount = document.getElementById('display_discount');
                const dispGrand = document.getElementById('display_grand_total');

                function formatRupiah(num) {
                    return new Intl.NumberFormat('id-ID').format(num);
                }

                function calculate() {
                    let taxPercent = parseFloat(taxInput.value) || 0;
                    let discountPercent = parseFloat(discountInput.value) || 0;
                    
                    if(taxPercent > 100) { taxPercent = 100; taxInput.value = 100; }
                    if(discountPercent > 100) { discountPercent = 100; discountInput.value = 100; }

                    const taxAmount = subtotal * (taxPercent / 100);
                    const discountAmount = subtotal * (discountPercent / 100);
                    const grand = subtotal + taxAmount - discountAmount;

                    dispTax.innerText = 'Rp ' + formatRupiah(taxAmount);
                    dispDiscount.innerText = 'Rp ' + formatRupiah(discountAmount);
                    dispGrand.innerText = 'Rp ' + formatRupiah(grand);
                }

                document.querySelectorAll('.calc-trigger').forEach(el => {
                    el.addEventListener('input', calculate);
                });
            });
        </script>
    </x-slot>
</x-app-layout>
