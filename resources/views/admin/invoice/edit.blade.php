<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.invoice.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Proses Pembayaran Tagihan') }} #{{ $invoice->invoice_number }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-8">
                    
                    <div class="mb-8 p-6 bg-slate-50 border border-slate-200 rounded-xl text-center">
                        <p class="text-sm text-slate-500 font-medium mb-1">Total yang Harus Dibayar</p>
                        <h3 class="text-4xl font-black text-[#FF6B00]">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</h3>
                        
                        @if($invoice->payment_status === 'paid')
                            <div class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-800 text-sm font-bold border border-emerald-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                SUDAH LUNAS
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('admin.invoice.update', $invoice->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tindakan Pembayaran <span class="text-red-500">*</span></label>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer border p-4 rounded-xl hover:bg-slate-50 transition-colors {{ old('payment_action', 'unpaid') == 'unpaid' ? 'border-amber-400 bg-amber-50' : 'border-slate-200' }}" id="lbl_unpaid">
                                    <input type="radio" name="payment_action" value="unpaid" class="text-amber-500 focus:ring-amber-500" {{ old('payment_action', 'unpaid') == 'unpaid' ? 'checked' : '' }} onchange="toggleActionUI()">
                                    <div>
                                        <span class="font-bold text-slate-700 block">Belum Bayar / Tunda</span>
                                        <span class="text-xs text-slate-500">Biarkan tagihan tetap belum lunas.</span>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 cursor-pointer border p-4 rounded-xl hover:bg-slate-50 transition-colors {{ old('payment_action') == 'cash' ? 'border-emerald-400 bg-emerald-50' : 'border-slate-200' }}" id="lbl_cash">
                                    <input type="radio" name="payment_action" value="cash" class="text-emerald-500 focus:ring-emerald-500" {{ old('payment_action') == 'cash' ? 'checked' : '' }} onchange="toggleActionUI()">
                                    <div>
                                        <span class="font-bold text-slate-700 block">Lunas (Tunai / Cash)</span>
                                        <span class="text-xs text-slate-500">Tandai tagihan langsung lunas karena pelanggan membayar tunai.</span>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 cursor-pointer border p-4 rounded-xl hover:bg-slate-50 transition-colors {{ old('payment_action') == 'qris' ? 'border-[#FF6B00] bg-orange-50' : 'border-slate-200' }}" id="lbl_qris">
                                    <input type="radio" name="payment_action" value="qris" class="text-[#FF6B00] focus:ring-[#FF6B00]" {{ old('payment_action') == 'qris' ? 'checked' : '' }} onchange="toggleActionUI()">
                                    <div>
                                        <span class="font-bold text-slate-700 block">Kirim QRIS (Otomatis Lunas)</span>
                                        <span class="text-xs text-slate-500">Kirim kode QRIS ke WhatsApp pelanggan. Status akan otomatis berubah lunas ketika pelanggan selesai membayar.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div id="qris_preview_container" class="hidden mt-4 bg-slate-50 p-6 rounded-xl border border-slate-200 text-center">
                            <h4 class="font-bold text-slate-700 mb-2 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5 text-[#FF6B00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                Preview QRIS (Akan Dikirim ke WA)
                            </h4>
                            <div class="inline-block p-2 bg-white rounded-xl shadow-sm border border-slate-100 mb-2">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($dynamicQris) }}" alt="QRIS Dinamis" class="w-48 h-48 mx-auto">
                            </div>
                            <p class="text-sm text-slate-500">Nominal: <strong class="text-slate-700">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</strong></p>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Catatan (Opsional)</label>
                            <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-[#FF6B00] text-sm">{{ old('notes', $invoice->notes) }}</textarea>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                            <a href="{{ route('admin.invoice.show', $invoice->id) }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 font-medium text-sm">
                                Batal
                            </a>
                            <button type="submit" id="submit_btn" class="px-6 py-2.5 bg-[#FF6B00] text-white rounded-lg hover:bg-orange-600 font-bold text-sm">
                                Proses Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            function toggleActionUI() {
                const selected = document.querySelector('input[name="payment_action"]:checked').value;
                const lblUnpaid = document.getElementById('lbl_unpaid');
                const lblCash = document.getElementById('lbl_cash');
                const lblQris = document.getElementById('lbl_qris');
                const qrisContainer = document.getElementById('qris_preview_container');

                // Reset styles
                lblUnpaid.classList.remove('border-amber-400', 'bg-amber-50');
                lblUnpaid.classList.add('border-slate-200');
                lblCash.classList.remove('border-emerald-400', 'bg-emerald-50');
                lblCash.classList.add('border-slate-200');
                lblQris.classList.remove('border-[#FF6B00]', 'bg-orange-50');
                lblQris.classList.add('border-slate-200');

                const submitBtn = document.getElementById('submit_btn');

                if (selected === 'unpaid') {
                    lblUnpaid.classList.add('border-amber-400', 'bg-amber-50');
                    lblUnpaid.classList.remove('border-slate-200');
                    qrisContainer.classList.add('hidden');
                    submitBtn.innerHTML = 'Simpan Perubahan';
                } else if (selected === 'cash') {
                    lblCash.classList.add('border-emerald-400', 'bg-emerald-50');
                    lblCash.classList.remove('border-slate-200');
                    qrisContainer.classList.add('hidden');
                    submitBtn.innerHTML = 'Tandai Lunas';
                } else if (selected === 'qris') {
                    lblQris.classList.add('border-[#FF6B00]', 'bg-orange-50');
                    lblQris.classList.remove('border-slate-200');
                    qrisContainer.classList.remove('hidden');
                    submitBtn.innerHTML = '<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Kirim QRIS (via WA)';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                toggleActionUI();
            });
        </script>
    </x-slot>
</x-app-layout>
