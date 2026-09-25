<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.service-order.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Ubah Status Order') }} #{{ $serviceOrder->order_number }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-8">
                    
                    @if(session('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                            <ul class="list-disc pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-8 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-slate-500 font-medium">Nomor Order</span>
                            <span class="font-bold text-[#FF6B00]">{{ $serviceOrder->order_number }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-500 font-medium">Total Tagihan</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($serviceOrder->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <form action="{{ route('admin.service-order.update', $serviceOrder->id) }}" method="POST" id="so-edit-form" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="mechanic_id" class="block text-sm font-medium text-slate-700 mb-1">Pilih / Ubah Mekanik Bertugas</label>
                            <select name="mechanic_id" id="mechanic_id" class="w-full px-4 py-2 border @error('mechanic_id') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-orange-500/20 focus:border-[#FF6B00] @enderror rounded-lg focus:outline-none focus:ring-2 transition-colors text-sm bg-white">
                                <option value="">-- Belum Ditentukan --</option>
                                @foreach($mechanics as $mechanic)
                                    <option value="{{ $mechanic->id }}" {{ old('mechanic_id', $serviceOrder->mechanic_id) == $mechanic->id ? 'selected' : '' }}>
                                        {{ $mechanic->name }} ({{ $mechanic->specialization ?? 'Umum' }})
                                        @if($mechanic->status != 'active') [{{ strtoupper($mechanic->status) }}] @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('mechanic_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status Progres Pekerjaan <span class="text-red-500">*</span></label>
                            <select name="status" id="status" required class="w-full px-4 py-2 border @error('status') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-orange-500/20 focus:border-[#FF6B00] @enderror rounded-lg focus:outline-none focus:ring-2 transition-colors text-sm bg-white">
                                <option value="pending" {{ old('status', $serviceOrder->status) == 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
                                <option value="in_progress" {{ old('status', $serviceOrder->status) == 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan (In Progress)</option>
                                <option value="completed" {{ old('status', $serviceOrder->status) == 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                                <option value="cancelled" {{ old('status', $serviceOrder->status) == 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
                            <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border @error('notes') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-orange-500/20 focus:border-[#FF6B00] @enderror rounded-lg focus:outline-none focus:ring-2 transition-colors text-sm" placeholder="Tambahkan catatan jika perlu...">{{ old('notes', $serviceOrder->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Item Pekerjaan (Jasa & Sparepart) -->
                        <div class="mt-8 border-t border-slate-100 pt-6">
                            <h3 class="text-md font-bold text-slate-800 mb-4">Rincian Pengerjaan & Suku Cadang</h3>
                            
                            <!-- Pilihan Jasa -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Tambahkan Jasa/Layanan</label>
                                <div class="flex gap-2">
                                    <select id="add_service_select" class="w-full md:w-2/3 px-4 py-2 border border-slate-200 rounded-lg text-sm bg-white">
                                        <option value="">-- Pilih Jasa --</option>
                                        @foreach($services as $srv)
                                            <option value="{{ $srv->id }}" data-price="{{ $srv->price }}" data-name="{{ $srv->name }}">{{ $srv->name }} - Rp {{ number_format($srv->price, 0, ',', '.') }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" id="btn_add_service" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 font-medium text-sm border border-blue-200 whitespace-nowrap">
                                        + Jasa
                                    </button>
                                </div>
                            </div>

                            <!-- Pilihan Sparepart -->
                            <div class="mb-6 pb-6 border-b border-slate-100">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Tambahkan Suku Cadang</label>
                                <div class="flex gap-2">
                                    <select id="add_sparepart_select" class="w-full md:w-2/3 px-4 py-2 border border-slate-200 rounded-lg text-sm bg-white">
                                        <option value="">-- Pilih Sparepart --</option>
                                        @foreach($spareparts as $sp)
                                            <!-- Calculate available stock: current stock + (quantity already in this order) -->
                                            @php
                                                $existingItemQty = $serviceOrder->items->where('type', 'sparepart')->where('sparepart_id', $sp->id)->first()->quantity ?? 0;
                                                $availableStock = $sp->stock + $existingItemQty;
                                            @endphp
                                            <option value="{{ $sp->id }}" data-price="{{ $sp->price }}" data-name="{{ $sp->name }}" data-stock="{{ $availableStock }}">{{ $sp->name }} (Stok: {{ $sp->stock }}) - Rp {{ number_format($sp->price, 0, ',', '.') }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" id="btn_add_sparepart" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 font-medium text-sm border border-blue-200 whitespace-nowrap">
                                        + Suku Cadang
                                    </button>
                                </div>
                            </div>

                            <!-- Tabel Keranjang -->
                            <div class="overflow-x-auto border border-slate-200 rounded-xl mb-4">
                                <table class="w-full text-left border-collapse" id="items_table">
                                    <thead>
                                        <tr class="bg-slate-50 border-b border-slate-200">
                                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Item</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-24">Tipe</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-32">Harga (Rp)</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-24">Qty</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-32">Subtotal (Rp)</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase text-center w-16">Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100" id="items_tbody">
                                        <tr id="empty_row" style="display: none;">
                                            <td colspan="6" class="px-4 py-8 text-center text-slate-500 text-sm">
                                                Belum ada item yang ditambahkan. Silakan pilih jasa atau suku cadang di atas.
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-slate-50 border-t border-slate-200">
                                            <td colspan="4" class="px-4 py-4 text-right font-bold text-slate-700">Total Estimasi Harga:</td>
                                            <td colspan="2" class="px-4 py-4 font-bold text-xl text-[#FF6B00]" id="grand_total_display">Rp 0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            @error('items')
                                <p class="text-sm text-red-500">Minimal harus ada 1 item (Jasa / Sparepart) untuk menyimpan.</p>
                            @enderror
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                            <a href="{{ route('admin.service-order.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition-colors font-medium text-sm">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2.5 bg-[#FF6B00] text-white rounded-lg hover:bg-orange-600 transition-colors font-medium text-sm">
                                Simpan Perubahan Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript untuk Dinamika Form -->
    <x-slot name="scripts">
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let itemIndex = 0;
                let grandTotal = 0;
                const tbody = document.getElementById('items_tbody');
                const emptyRow = document.getElementById('empty_row');
                const grandTotalDisplay = document.getElementById('grand_total_display');

                function formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID').format(number);
                }

                function updateGrandTotal() {
                    grandTotal = 0;
                    document.querySelectorAll('.item-subtotal-input').forEach(input => {
                        grandTotal += parseInt(input.value || 0);
                    });
                    grandTotalDisplay.innerText = 'Rp ' + formatRupiah(grandTotal);
                }

                function addItemToTable(type, id, name, price, qty = 1, maxStock = null) {
                    if (emptyRow) emptyRow.style.display = 'none';

                    const rowId = `item_row_${itemIndex}`;
                    const typeLabel = type === 'service' ? '<span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">Jasa</span>' : '<span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-xs">Sparepart</span>';
                    
                    let qtyInputHTML = '';
                    if (type === 'service') {
                        qtyInputHTML = `<input type="number" name="items[${itemIndex}][quantity]" value="${qty}" min="1" class="w-full px-2 py-1 border border-slate-300 rounded item-qty" data-price="${price}" data-target-subtotal="subtotal_${itemIndex}" data-target-input="input_subtotal_${itemIndex}">`;
                    } else {
                        qtyInputHTML = `<input type="number" name="items[${itemIndex}][quantity]" value="${qty}" min="1" max="${maxStock}" class="w-full px-2 py-1 border border-slate-300 rounded item-qty" data-price="${price}" data-target-subtotal="subtotal_${itemIndex}" data-target-input="input_subtotal_${itemIndex}">`;
                    }

                    const subtotal = qty * price;

                    const html = `
                        <tr id="${rowId}" class="bg-white">
                            <td class="px-4 py-3 text-sm font-medium text-slate-800">
                                ${name}
                                <input type="hidden" name="items[${itemIndex}][type]" value="${type}">
                                <input type="hidden" name="items[${itemIndex}][item_id]" value="${id}">
                            </td>
                            <td class="px-4 py-3">${typeLabel}</td>
                            <td class="px-4 py-3 text-sm">
                                Rp ${formatRupiah(price)}
                                <input type="hidden" name="items[${itemIndex}][price]" value="${price}">
                            </td>
                            <td class="px-4 py-3">
                                ${qtyInputHTML}
                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-slate-700">
                                <span id="subtotal_${itemIndex}">Rp ${formatRupiah(subtotal)}</span>
                                <input type="hidden" class="item-subtotal-input" id="input_subtotal_${itemIndex}" value="${subtotal}">
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" class="text-red-500 hover:text-red-700" onclick="document.getElementById('${rowId}').remove(); updateGrandTotal(); checkEmpty();">
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </td>
                        </tr>
                    `;

                    tbody.insertAdjacentHTML('beforeend', html);
                    itemIndex++;
                    updateGrandTotal();
                    
                    document.querySelectorAll('.item-qty').forEach(input => {
                        input.removeEventListener('input', updateRowSubtotal);
                        input.addEventListener('input', updateRowSubtotal);
                    });
                }

                function checkEmpty() {
                    if (tbody.children.length === 1 && tbody.children[0].id === 'empty_row') {
                        emptyRow.style.display = 'table-row';
                    } else if (tbody.children.length === 0) {
                        tbody.appendChild(emptyRow);
                        emptyRow.style.display = 'table-row';
                    }
                }

                function updateRowSubtotal(e) {
                    let qty = parseInt(e.target.value);
                    if (isNaN(qty) || qty < 1) qty = 1;
                    
                    let max = parseInt(e.target.getAttribute('max'));
                    if (!isNaN(max) && qty > max) {
                        alert('Kuantitas melebihi stok yang tersedia (' + max + ')!');
                        qty = max;
                        e.target.value = max;
                    }

                    const price = parseInt(e.target.getAttribute('data-price'));
                    const subtotal = qty * price;
                    
                    const textTargetId = e.target.getAttribute('data-target-subtotal');
                    const inputTargetId = e.target.getAttribute('data-target-input');
                    
                    document.getElementById(textTargetId).innerText = 'Rp ' + formatRupiah(subtotal);
                    document.getElementById(inputTargetId).value = subtotal;
                    
                    updateGrandTotal();
                }

                // Initial Load of Existing Items
                @php
                    $mappedItems = $serviceOrder->items->map(function($item) use ($spareparts) {
                        $maxStock = null;
                        if ($item->type === 'sparepart') {
                            $sp = $spareparts->where('id', $item->sparepart_id)->first();
                            $maxStock = $sp ? ($sp->stock + $item->quantity) : $item->quantity;
                        }
                        return [
                            'type' => $item->type,
                            'id' => $item->type === 'service' ? $item->service_id : $item->sparepart_id,
                            'name' => $item->item_name,
                            'price' => $item->price,
                            'quantity' => $item->quantity,
                            'maxStock' => $maxStock
                        ];
                    })->values();
                @endphp
                const existingItems = @json($mappedItems);

                existingItems.forEach(item => {
                    addItemToTable(item.type, item.id, item.name, item.price, item.quantity, item.maxStock);
                });

                if (existingItems.length === 0) {
                    checkEmpty();
                }

                // Button Listeners
                document.getElementById('btn_add_service').addEventListener('click', function() {
                    const sel = document.getElementById('add_service_select');
                    if (!sel.value) return;
                    const opt = sel.options[sel.selectedIndex];
                    addItemToTable('service', sel.value, opt.getAttribute('data-name'), opt.getAttribute('data-price'));
                    sel.value = '';
                });

                document.getElementById('btn_add_sparepart').addEventListener('click', function() {
                    const sel = document.getElementById('add_sparepart_select');
                    if (!sel.value) return;
                    
                    const opt = sel.options[sel.selectedIndex];
                    const stock = parseInt(opt.getAttribute('data-stock'));
                    
                    if (stock < 1) {
                        alert('Stok suku cadang ini habis!');
                        return;
                    }

                    addItemToTable('sparepart', sel.value, opt.getAttribute('data-name'), opt.getAttribute('data-price'), 1, stock);
                    sel.value = '';
                });

                document.getElementById('so-edit-form').addEventListener('submit', function(e) {
                    if (document.querySelectorAll('.item-subtotal-input').length === 0) {
                        e.preventDefault();
                        alert('Silakan tambahkan minimal 1 item (Jasa atau Suku Cadang) sebelum menyimpan.');
                    }
                });
            });
        </script>
    </x-slot>
</x-app-layout>
