<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.service-order.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Buat Service Order Baru') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.service-order.store') }}" method="POST" id="so-form">
                @csrf
                
                <!-- Section 1: Informasi Pelanggan & Kendaraan -->
                <div class="bg-white shadow-sm sm:rounded-2xl border border-slate-100 mb-6 overflow-hidden">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800">1. Data Pelanggan & Kendaraan</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-slate-700 mb-1">Pelanggan <span class="text-red-500">*</span></label>
                            <select name="customer_id" id="customer_id" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-[#FF6B00] text-sm bg-white">
                                <option value="">-- Pilih Pelanggan --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }} ({{ $customer->phone }})</option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="vehicle_id" class="block text-sm font-medium text-slate-700 mb-1">Kendaraan <span class="text-red-500">*</span></label>
                            <select name="vehicle_id" id="vehicle_id" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-[#FF6B00] text-sm bg-white" disabled>
                                <option value="">-- Pilih Pelanggan Terlebih Dahulu --</option>
                            </select>
                            <p class="mt-1 text-xs text-slate-500" id="vehicle-help">Otomatis dimuat setelah memilih pelanggan.</p>
                            @error('vehicle_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Penugasan -->
                <div class="bg-white shadow-sm sm:rounded-2xl border border-slate-100 mb-6 overflow-hidden">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800">2. Penugasan</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="mechanic_id" class="block text-sm font-medium text-slate-700 mb-1">Mekanik <span class="text-slate-400 font-normal">(Opsional, bisa diatur nanti)</span></label>
                            <select name="mechanic_id" id="mechanic_id" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-[#FF6B00] text-sm bg-white">
                                <option value="">-- Belum Ditentukan --</option>
                                @foreach($mechanics as $mechanic)
                                    <option value="{{ $mechanic->id }}" {{ old('mechanic_id') == $mechanic->id ? 'selected' : '' }}>{{ $mechanic->name }} ({{ $mechanic->specialization ?? 'Umum' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status Order <span class="text-red-500">*</span></label>
                            <select name="status" id="status" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-[#FF6B00] text-sm bg-white">
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan (In Progress)</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Item Pekerjaan (Jasa & Sparepart) -->
                <div class="bg-white shadow-sm sm:rounded-2xl border border-slate-100 mb-6 overflow-hidden">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-800">3. Rincian Pengerjaan & Suku Cadang</h3>
                    </div>
                    <div class="p-6">
                        
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
                                        <option value="{{ $sp->id }}" data-price="{{ $sp->price }}" data-name="{{ $sp->name }}" data-stock="{{ $sp->stock }}">{{ $sp->name }} (Stok: {{ $sp->stock }}) - Rp {{ number_format($sp->price, 0, ',', '.') }}</option>
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
                                    <tr id="empty_row">
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
                            <p class="text-sm text-red-500">Minimal harus ada 1 item (Jasa / Sparepart) untuk membuat Service Order.</p>
                        @enderror

                        <div>
                            <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Catatan Tambahan Keluhan Pelanggan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#FF6B00] transition-colors text-sm" placeholder="Contoh: Rem depan bunyi berdecit saat ditarik">{{ old('notes') }}</textarea>
                        </div>

                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.service-order.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition-colors font-medium">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-3 bg-[#FF6B00] text-white rounded-lg hover:bg-orange-600 transition-colors font-medium shadow-sm">
                        Simpan & Buat Service Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript untuk Dinamika Form -->
    <x-slot name="scripts">
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const customerSelect = document.getElementById('customer_id');
                const vehicleSelect = document.getElementById('vehicle_id');
                const vehicleHelp = document.getElementById('vehicle-help');

                // Dynamic Vehicles based on Customer
                customerSelect.addEventListener('change', function() {
                    const customerId = this.value;
                    vehicleSelect.innerHTML = '<option value="">-- Memuat Kendaraan... --</option>';
                    vehicleSelect.disabled = true;

                    if (!customerId) {
                        vehicleSelect.innerHTML = '<option value="">-- Pilih Pelanggan Terlebih Dahulu --</option>';
                        vehicleHelp.innerText = 'Otomatis dimuat setelah memilih pelanggan.';
                        return;
                    }

                    let fetchUrl = "{{ route('admin.api.customer.vehicles', ['customer' => 'ID_PLACEHOLDER']) }}";
                    fetchUrl = fetchUrl.replace('ID_PLACEHOLDER', customerId);

                    fetch(fetchUrl)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            vehicleSelect.disabled = false;
                            if (data.length === 0) {
                                vehicleSelect.innerHTML = '<option value="">-- Pelanggan Tidak Memiliki Kendaraan --</option>';
                                vehicleHelp.innerHTML = '<span class="text-red-500">Silakan tambahkan kendaraan untuk pelanggan ini di menu Master Data terlebih dahulu.</span>';
                            } else {
                                vehicleSelect.innerHTML = '<option value="">-- Pilih Kendaraan --</option>';
                                data.forEach(vehicle => {
                                    // if old('vehicle_id') matches, select it
                                    const oldVehicleId = "{{ old('vehicle_id') }}";
                                    const selected = oldVehicleId == vehicle.id ? 'selected' : '';
                                    vehicleSelect.innerHTML += `<option value="${vehicle.id}" ${selected}>${vehicle.brand} ${vehicle.model} (${vehicle.license_plate})</option>`;
                                });
                                vehicleHelp.innerText = 'Pilih kendaraan yang akan diservis.';
                            }
                        })
                        .catch(err => {
                            vehicleSelect.innerHTML = '<option value="">-- Gagal memuat data --</option>';
                        });
                });

                // Trigger change if customer is already selected (e.g. from validation old data)
                if (customerSelect.value) {
                    customerSelect.dispatchEvent(new Event('change'));
                }

                // Dynamic Items Cart
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

                function addItemToTable(type, id, name, price, maxStock = null) {
                    if (emptyRow) emptyRow.style.display = 'none';

                    const rowId = `item_row_${itemIndex}`;
                    const typeLabel = type === 'service' ? '<span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">Jasa</span>' : '<span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-xs">Sparepart</span>';
                    
                    let qtyInputHTML = '';
                    if (type === 'service') {
                        qtyInputHTML = `<input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" class="w-full px-2 py-1 border border-slate-300 rounded item-qty" data-price="${price}" data-target-subtotal="subtotal_${itemIndex}" data-target-input="input_subtotal_${itemIndex}">`;
                    } else {
                        qtyInputHTML = `<input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" max="${maxStock}" class="w-full px-2 py-1 border border-slate-300 rounded item-qty" data-price="${price}" data-target-subtotal="subtotal_${itemIndex}" data-target-input="input_subtotal_${itemIndex}">`;
                    }

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
                                <span id="subtotal_${itemIndex}">Rp ${formatRupiah(price)}</span>
                                <input type="hidden" class="item-subtotal-input" id="input_subtotal_${itemIndex}" value="${price}">
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
                    
                    // Attach event listener to new qty input
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
                    
                    // Check max stock
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

                // Button Listeners
                document.getElementById('btn_add_service').addEventListener('click', function() {
                    const sel = document.getElementById('add_service_select');
                    if (!sel.value) return;
                    
                    const opt = sel.options[sel.selectedIndex];
                    addItemToTable('service', sel.value, opt.getAttribute('data-name'), opt.getAttribute('data-price'));
                    sel.value = ''; // reset
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

                    addItemToTable('sparepart', sel.value, opt.getAttribute('data-name'), opt.getAttribute('data-price'), stock);
                    sel.value = ''; // reset
                });

                // Form submission validation
                document.getElementById('so-form').addEventListener('submit', function(e) {
                    if (document.querySelectorAll('.item-subtotal-input').length === 0) {
                        e.preventDefault();
                        alert('Silakan tambahkan minimal 1 item (Jasa atau Suku Cadang) sebelum menyimpan.');
                    }
                });
            });
        </script>
    </x-slot>
</x-app-layout>
