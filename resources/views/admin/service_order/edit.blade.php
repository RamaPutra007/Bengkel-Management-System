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
                            <span class="text-sm text-slate-500 font-medium">Total Tagihan Awal</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($serviceOrder->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <form action="{{ route('admin.service-order.update', $serviceOrder->id) }}" method="POST" id="so-edit-form" onsubmit="return validateOrderForm(event)" class="space-y-6">
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
                                    <button type="button" id="btn_add_service" onclick="addServiceItem()" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 font-medium text-sm border border-blue-200 whitespace-nowrap cursor-pointer">
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
                                            @php
                                                $existingItemQty = $serviceOrder->items->where('type', 'sparepart')->where('sparepart_id', $sp->id)->first()->quantity ?? 0;
                                                $availableStock = $sp->stock + $existingItemQty;
                                            @endphp
                                            <option value="{{ $sp->id }}" data-price="{{ $sp->price }}" data-name="{{ $sp->name }}" data-stock="{{ $availableStock }}">{{ $sp->name }} (Stok: {{ $sp->stock }}) - Rp {{ number_format($sp->price, 0, ',', '.') }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" id="btn_add_sparepart" onclick="addSparepartItem()" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 font-medium text-sm border border-blue-200 whitespace-nowrap cursor-pointer">
                                        + Suku Cadang
                                    </button>
                                </div>
                            </div>

                            @php
                                $oldItems = old('items');
                                if ($oldItems && is_array($oldItems)) {
                                    $renderedItems = collect($oldItems)->map(function($item, $idx) use ($services, $spareparts) {
                                        $type = $item['type'] ?? 'service';
                                        $itemId = $item['item_id'] ?? null;
                                        $qty = (int)($item['quantity'] ?? 1);
                                        $price = (int)($item['price'] ?? 0);
                                        $name = '';
                                        $maxStock = null;
                                        if ($type === 'service') {
                                            $srv = $services->firstWhere('id', $itemId);
                                            $name = $srv ? $srv->name : 'Jasa';
                                        } else {
                                            $sp = $spareparts->firstWhere('id', $itemId);
                                            $name = $sp ? $sp->name : 'Suku Cadang';
                                            $maxStock = $sp ? $sp->stock : null;
                                        }
                                        return [
                                            'type' => $type,
                                            'item_id' => $itemId,
                                            'name' => $name,
                                            'price' => $price,
                                            'quantity' => $qty,
                                            'subtotal' => $price * $qty,
                                            'max_stock' => $maxStock
                                        ];
                                    });
                                } else {
                                    $renderedItems = $serviceOrder->items->map(function($item) use ($spareparts) {
                                        $maxStock = null;
                                        if ($item->type === 'sparepart') {
                                            $sp = $spareparts->firstWhere('id', $item->sparepart_id);
                                            $maxStock = $sp ? ($sp->stock + $item->quantity) : $item->quantity;
                                        }
                                        return [
                                            'type' => $item->type,
                                            'item_id' => $item->type === 'service' ? $item->service_id : $item->sparepart_id,
                                            'name' => $item->item_name,
                                            'price' => (int)$item->price,
                                            'quantity' => (int)$item->quantity,
                                            'subtotal' => (int)($item->price * $item->quantity),
                                            'max_stock' => $maxStock
                                        ];
                                    });
                                }
                                $initialGrandTotal = $renderedItems->sum('subtotal');
                            @endphp

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
                                        @forelse($renderedItems as $idx => $item)
                                            <tr id="item_row_{{ $idx }}" class="bg-white item-row border-b border-slate-100">
                                                <td class="px-4 py-3 text-sm font-medium text-slate-800">
                                                    {{ $item['name'] }}
                                                    <input type="hidden" name="items[{{ $idx }}][type]" value="{{ $item['type'] }}">
                                                    <input type="hidden" name="items[{{ $idx }}][item_id]" value="{{ $item['item_id'] }}">
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if($item['type'] === 'service')
                                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-semibold">Jasa</span>
                                                    @else
                                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-xs font-semibold">Sparepart</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    Rp {{ number_format($item['price'], 0, ',', '.') }}
                                                    <input type="hidden" name="items[{{ $idx }}][price]" value="{{ $item['price'] }}">
                                                </td>
                                                <td class="px-4 py-3 w-28">
                                                    <input type="number" name="items[{{ $idx }}][quantity]" value="{{ $item['quantity'] }}" min="1" {{ $item['max_stock'] !== null ? 'max=' . $item['max_stock'] : '' }} class="w-full px-2 py-1 border border-slate-300 rounded item-qty text-center" data-price="{{ $item['price'] }}" data-target-subtotal="subtotal_{{ $idx }}" data-target-input="input_subtotal_{{ $idx }}" oninput="updateRowSubtotal(this)">
                                                </td>
                                                <td class="px-4 py-3 text-sm font-bold text-slate-700">
                                                    <span id="subtotal_{{ $idx }}">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                                    <input type="hidden" class="item-subtotal-input" id="input_subtotal_{{ $idx }}" value="{{ $item['subtotal'] }}">
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" class="text-red-500 hover:text-red-700 p-1 cursor-pointer" onclick="removeItemRow('item_row_{{ $idx }}')" title="Hapus Item">
                                                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr id="empty_row">
                                                <td colspan="6" class="px-4 py-8 text-center text-slate-500 text-sm">
                                                    Belum ada item yang ditambahkan. Silakan pilih jasa atau suku cadang di atas.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-slate-50 border-t border-slate-200">
                                            <td colspan="4" class="px-4 py-4 text-right font-bold text-slate-700">Total Estimasi Harga:</td>
                                            <td colspan="2" class="px-4 py-4 font-bold text-xl text-[#FF6B00]" id="grand_total_display">Rp {{ number_format($initialGrandTotal, 0, ',', '.') }}</td>
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
                            <button type="submit" class="px-6 py-2.5 bg-[#FF6B00] text-white rounded-lg hover:bg-orange-600 transition-colors font-medium text-sm shadow-sm cursor-pointer">
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
            let itemIndex = {{ count($renderedItems) + 10 }};

            window.formatRupiah = function(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            };

            window.updateGrandTotal = function() {
                let grandTotal = 0;
                document.querySelectorAll('.item-subtotal-input').forEach(input => {
                    grandTotal += parseInt(input.value || 0);
                });
                const display = document.getElementById('grand_total_display');
                if (display) {
                    display.innerText = 'Rp ' + window.formatRupiah(grandTotal);
                }
            };

            window.checkEmpty = function() {
                const tbody = document.getElementById('items_tbody');
                if (!tbody) return;
                const rows = tbody.querySelectorAll('.item-row');
                const emptyRow = document.getElementById('empty_row');
                if (rows.length === 0) {
                    if (!emptyRow) {
                        tbody.insertAdjacentHTML('beforeend', `
                            <tr id="empty_row">
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500 text-sm">
                                    Belum ada item yang ditambahkan. Silakan pilih jasa atau suku cadang di atas.
                                </td>
                            </tr>
                        `);
                    } else {
                        emptyRow.style.display = 'table-row';
                    }
                } else {
                    if (emptyRow) {
                        emptyRow.style.display = 'none';
                    }
                }
            };

            window.updateRowSubtotal = function(targetInput) {
                let qty = parseInt(targetInput.value);
                if (isNaN(qty) || qty < 1) {
                    qty = 1;
                    targetInput.value = 1;
                }

                let max = parseInt(targetInput.getAttribute('max'));
                if (!isNaN(max) && qty > max) {
                    alert('Kuantitas melebihi stok yang tersedia (' + max + ')!');
                    qty = max;
                    targetInput.value = max;
                }

                const price = parseInt(targetInput.getAttribute('data-price')) || 0;
                const subtotal = qty * price;

                const textTargetId = targetInput.getAttribute('data-target-subtotal');
                const inputTargetId = targetInput.getAttribute('data-target-input');

                const textEl = document.getElementById(textTargetId);
                if (textEl) textEl.innerText = 'Rp ' + window.formatRupiah(subtotal);

                const inputEl = document.getElementById(inputTargetId);
                if (inputEl) inputEl.value = subtotal;

                window.updateGrandTotal();
            };

            window.removeItemRow = function(rowId) {
                const el = document.getElementById(rowId);
                if (el) el.remove();
                window.updateGrandTotal();
                window.checkEmpty();
            };

            window.addItemToTable = function(type, id, name, price, qty = 1, maxStock = null) {
                const tbody = document.getElementById('items_tbody');
                const emptyRow = document.getElementById('empty_row');
                if (!tbody) return;
                if (emptyRow) emptyRow.style.display = 'none';

                const rowId = 'item_row_' + itemIndex;
                const typeLabel = type === 'service'
                    ? '<span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-semibold">Jasa</span>'
                    : '<span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-xs font-semibold">Sparepart</span>';

                const maxAttr = (type === 'sparepart' && maxStock !== null) ? `max="${maxStock}"` : '';
                const numPrice = parseInt(price) || 0;
                const numQty = parseInt(qty) || 1;
                const subtotal = numPrice * numQty;

                const qtyInputHTML = `<input type="number" name="items[${itemIndex}][quantity]" value="${numQty}" min="1" ${maxAttr} class="w-full px-2 py-1 border border-slate-300 rounded item-qty text-center" data-price="${numPrice}" data-target-subtotal="subtotal_${itemIndex}" data-target-input="input_subtotal_${itemIndex}" oninput="updateRowSubtotal(this)">`;

                const html = `
                    <tr id="${rowId}" class="bg-white item-row border-b border-slate-100">
                        <td class="px-4 py-3 text-sm font-medium text-slate-800">
                            ${name}
                            <input type="hidden" name="items[${itemIndex}][type]" value="${type}">
                            <input type="hidden" name="items[${itemIndex}][item_id]" value="${id}">
                        </td>
                        <td class="px-4 py-3">${typeLabel}</td>
                        <td class="px-4 py-3 text-sm">
                            Rp ${window.formatRupiah(numPrice)}
                            <input type="hidden" name="items[${itemIndex}][price]" value="${numPrice}">
                        </td>
                        <td class="px-4 py-3 w-28">
                            ${qtyInputHTML}
                        </td>
                        <td class="px-4 py-3 text-sm font-bold text-slate-700">
                            <span id="subtotal_${itemIndex}">Rp ${window.formatRupiah(subtotal)}</span>
                            <input type="hidden" class="item-subtotal-input" id="input_subtotal_${itemIndex}" value="${subtotal}">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" class="text-red-500 hover:text-red-700 p-1 cursor-pointer" onclick="removeItemRow('${rowId}')" title="Hapus Item">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </td>
                    </tr>
                `;

                tbody.insertAdjacentHTML('beforeend', html);
                itemIndex++;
                window.updateGrandTotal();
                window.checkEmpty();
            };

            window.addServiceItem = function() {
                const sel = document.getElementById('add_service_select');
                if (!sel || !sel.value) {
                    alert('Silakan pilih jasa terlebih dahulu.');
                    return;
                }
                const opt = sel.options[sel.selectedIndex];
                const id = sel.value;
                const name = opt.getAttribute('data-name');
                const price = opt.getAttribute('data-price');
                window.addItemToTable('service', id, name, price, 1);
                sel.value = '';
            };

            window.addSparepartItem = function() {
                const sel = document.getElementById('add_sparepart_select');
                if (!sel || !sel.value) {
                    alert('Silakan pilih sparepart terlebih dahulu.');
                    return;
                }
                const opt = sel.options[sel.selectedIndex];
                const id = sel.value;
                const name = opt.getAttribute('data-name');
                const price = opt.getAttribute('data-price');
                const stock = parseInt(opt.getAttribute('data-stock') || 0);

                if (stock < 1) {
                    alert('Stok suku cadang ini habis!');
                    return;
                }

                window.addItemToTable('sparepart', id, name, price, 1, stock);
                sel.value = '';
            };

            window.validateOrderForm = function(e) {
                const count = document.querySelectorAll('.item-subtotal-input').length;
                if (count === 0) {
                    if (e) e.preventDefault();
                    alert('Silakan tambahkan minimal 1 item (Jasa atau Suku Cadang) sebelum menyimpan.');
                    return false;
                }
                return true;
            };
        </script>
    </x-slot>
</x-app-layout>
