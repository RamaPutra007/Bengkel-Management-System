<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.service-order.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    {{ __('Buat Service Order Baru') }}
                </h2>
            </div>
            <!-- Quick badge indicator for device context -->
            <div class="text-xs text-slate-400 hidden sm:block">
                Form Transaksi Bengkel
            </div>
        </div>
    </x-slot>

    <div class="w-full max-w-5xl mx-auto space-y-6 pb-20 sm:pb-0">
        
        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-medium flex items-center gap-3 shadow-sm" role="alert">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('admin.service-order.store') }}" method="POST" id="so-form" onsubmit="return validateOrderForm(event)">
            @csrf
            
            <!-- Section 1: Informasi Pelanggan & Kendaraan -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 mb-6 overflow-hidden">
                <div class="bg-slate-50/80 px-4 sm:px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-[#FF6B00] text-white text-xs font-bold flex items-center justify-center">1</span>
                        <span>Data Pelanggan & Kendaraan</span>
                    </h3>
                </div>
                <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-slate-700 mb-1">Pelanggan <span class="text-red-500">*</span></label>
                        <select name="customer_id" id="customer_id" onchange="loadCustomerVehicles(this.value)" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] text-sm bg-white transition-all">
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
                        <select name="vehicle_id" id="vehicle_id" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] text-sm bg-white transition-all" disabled>
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
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 mb-6 overflow-hidden">
                <div class="bg-slate-50/80 px-4 sm:px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-[#FF6B00] text-white text-xs font-bold flex items-center justify-center">2</span>
                        <span>Penugasan & Status Pengerjaan</span>
                    </h3>
                </div>
                <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="mechanic_id" class="block text-sm font-medium text-slate-700 mb-1">Mekanik <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <select name="mechanic_id" id="mechanic_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] text-sm bg-white transition-all">
                            <option value="">-- Belum Ditentukan --</option>
                            @foreach($mechanics as $mechanic)
                                <option value="{{ $mechanic->id }}" {{ old('mechanic_id') == $mechanic->id ? 'selected' : '' }}>{{ $mechanic->name }} ({{ $mechanic->specialization ?? 'Umum' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status Order <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] text-sm bg-white transition-all">
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
                            <option value="in_progress" {{ old('status', 'in_progress') == 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan (In Progress)</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 3: Item Pekerjaan (Jasa & Sparepart) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 mb-6 overflow-hidden">
                <div class="bg-slate-50/80 px-4 sm:px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-[#FF6B00] text-white text-xs font-bold flex items-center justify-center">3</span>
                        <span>Rincian Pengerjaan & Suku Cadang</span>
                    </h3>
                </div>
                <div class="p-4 sm:p-6">
                    
                    <!-- Pilihan Jasa -->
                    <div class="mb-4">
                        <label class="block text-xs sm:text-sm font-bold text-slate-700 mb-1.5">Tambahkan Jasa / Layanan</label>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <select id="add_service_select" class="w-full sm:flex-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="">-- Pilih Jasa --</option>
                                @foreach($services as $srv)
                                    <option value="{{ $srv->id }}" data-price="{{ $srv->price }}" data-name="{{ $srv->name }}">{{ $srv->name }} - Rp {{ number_format($srv->price, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="btn_add_service" onclick="addServiceItem()" class="w-full sm:w-auto px-5 py-2.5 bg-blue-50 hover:bg-blue-100 active:bg-blue-200 text-blue-700 rounded-xl font-bold text-sm border border-blue-200 transition-all flex items-center justify-center gap-1.5 cursor-pointer shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>+ Tambah Jasa</span>
                            </button>
                        </div>
                    </div>

                    <!-- Pilihan Sparepart -->
                    <div class="mb-6 pb-6 border-b border-slate-100">
                        <label class="block text-xs sm:text-sm font-bold text-slate-700 mb-1.5">Tambahkan Suku Cadang / Sparepart</label>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <select id="add_sparepart_select" class="w-full sm:flex-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all">
                                <option value="">-- Pilih Suku Cadang --</option>
                                @foreach($spareparts as $sp)
                                    <option value="{{ $sp->id }}" data-price="{{ $sp->price }}" data-name="{{ $sp->name }}" data-stock="{{ $sp->stock }}">{{ $sp->name }} (Stok: {{ $sp->stock }}) - Rp {{ number_format($sp->price, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="btn_add_sparepart" onclick="addSparepartItem()" class="w-full sm:w-auto px-5 py-2.5 bg-amber-50 hover:bg-amber-100 active:bg-amber-200 text-amber-700 rounded-xl font-bold text-sm border border-amber-200 transition-all flex items-center justify-center gap-1.5 cursor-pointer shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>+ Tambah Suku Cadang</span>
                            </button>
                        </div>
                    </div>

                    @php
                        $oldItems = old('items');
                        $renderedItems = collect();
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
                        }
                        $initialGrandTotal = $renderedItems->sum('subtotal');
                    @endphp

                    <!-- Responsive Items Container (Table di PC, Cards di HP) -->
                    <div class="border border-slate-200/80 rounded-2xl mb-4 overflow-hidden bg-slate-50/40 md:bg-transparent p-2 md:p-0">
                        <table class="w-full text-left border-collapse block md:table" id="items_table">
                            <!-- Desktop Table Header -->
                            <thead class="hidden md:table-header-group">
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Item</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase w-24">Tipe</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase w-32">Harga (Rp)</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase w-28">Qty</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase w-36">Subtotal (Rp)</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase text-center w-16">Hapus</th>
                                </tr>
                            </thead>
                            <tbody class="block md:table-row-group divide-y-0 md:divide-y md:divide-slate-100" id="items_tbody">
                                @forelse($renderedItems as $idx => $item)
                                    <tr id="item_row_{{ $idx }}" class="item-row bg-white hover:bg-slate-50/80 border border-slate-200 md:border-0 md:border-b md:border-slate-100 rounded-xl md:rounded-none block md:table-row p-4 md:p-0 mb-3 md:mb-0 shadow-sm md:shadow-none space-y-3 md:space-y-0">
                                        <td class="block md:table-cell px-0 md:px-4 py-0 md:py-3.5">
                                            <div class="flex items-start justify-between gap-2 md:block">
                                                <div>
                                                    <span class="text-sm font-bold text-slate-800 block">{{ $item['name'] }}</span>
                                                    <span class="inline-block md:hidden mt-1">
                                                        @if($item['type'] === 'service')
                                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-semibold">Jasa</span>
                                                        @else
                                                            <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-xs font-semibold">Sparepart</span>
                                                        @endif
                                                    </span>
                                                </div>
                                                <button type="button" class="md:hidden text-rose-500 hover:text-rose-700 p-1.5 rounded-lg bg-rose-50 border border-rose-100" onclick="removeItemRow('item_row_{{ $idx }}')" title="Hapus Item">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                            <input type="hidden" name="items[{{ $idx }}][type]" value="{{ $item['type'] }}">
                                            <input type="hidden" name="items[{{ $idx }}][item_id]" value="{{ $item['item_id'] }}">
                                        </td>
                                        <td class="hidden md:table-cell px-4 py-3.5">
                                            @if($item['type'] === 'service')
                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-semibold">Jasa</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-xs font-semibold">Sparepart</span>
                                            @endif
                                        </td>
                                        <td class="block md:table-cell px-0 md:px-4 py-0 md:py-3.5 text-xs md:text-sm text-slate-500 md:text-slate-700">
                                            <span class="md:hidden text-slate-400 font-medium">Harga: </span>
                                            <span>Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                            <input type="hidden" name="items[{{ $idx }}][price]" value="{{ $item['price'] }}">
                                        </td>
                                        <td class="block md:table-cell px-0 md:px-4 py-0 md:py-3.5">
                                            <div class="flex items-center justify-between md:justify-center gap-2">
                                                <span class="md:hidden text-xs text-slate-500 font-medium">Qty:</span>
                                                <div class="w-28">
                                                    <input type="number" name="items[{{ $idx }}][quantity]" value="{{ $item['quantity'] }}" min="1" {{ $item['max_stock'] !== null ? 'max=' . $item['max_stock'] : '' }} class="w-full px-2 py-1.5 border border-slate-300 rounded-lg item-qty text-center text-sm font-semibold" data-price="{{ $item['price'] }}" data-target-subtotal="subtotal_{{ $idx }}" data-target-input="input_subtotal_{{ $idx }}" oninput="updateRowSubtotal(this)">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="block md:table-cell px-0 md:px-4 py-0 md:py-3.5 text-sm font-bold text-slate-800">
                                            <div class="flex items-center justify-between md:justify-start gap-2 pt-2 md:pt-0 border-t border-slate-100 md:border-0">
                                                <span class="md:hidden text-xs text-slate-500 font-medium">Subtotal:</span>
                                                <span id="subtotal_{{ $idx }}" class="text-[#FF6B00] md:text-slate-800 font-black md:font-bold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                            </div>
                                            <input type="hidden" class="item-subtotal-input" id="input_subtotal_{{ $idx }}" value="{{ $item['subtotal'] }}">
                                        </td>
                                        <td class="hidden md:table-cell px-4 py-3.5 text-center">
                                            <button type="button" class="text-rose-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors" onclick="removeItemRow('item_row_{{ $idx }}')" title="Hapus Item">
                                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
                            <!-- Desktop Footer Total -->
                            <tfoot class="hidden md:table-footer-group">
                                <tr class="bg-slate-50 border-t border-slate-200">
                                    <td colspan="4" class="px-4 py-4 text-right font-bold text-slate-700">Total Estimasi Harga:</td>
                                    <td colspan="2" class="px-4 py-4 font-black text-xl text-[#FF6B00]" id="grand_total_display">Rp {{ number_format($initialGrandTotal, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Mobile Subtotal Card (< 768px) -->
                    <div class="md:hidden p-4 bg-orange-50/70 rounded-xl border border-orange-200/80 flex items-center justify-between mb-4">
                        <span class="text-xs font-bold text-slate-600 uppercase">Total Estimasi:</span>
                        <span class="font-black text-lg text-[#FF6B00] mobile-grand-total" id="grand_total_display_mobile">Rp {{ number_format($initialGrandTotal, 0, ',', '.') }}</span>
                    </div>
                    
                    @error('items')
                        <p class="text-sm text-red-500 mb-4">Minimal harus ada 1 item (Jasa / Sparepart) untuk membuat Service Order.</p>
                    @enderror

                    <div>
                        <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Catatan Tambahan Keluhan Pelanggan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] transition-all text-sm" placeholder="Contoh: Rem depan bunyi berdecit saat ditarik">{{ old('notes') }}</textarea>
                    </div>

                </div>
            </div>

            <!-- Form Action Buttons (Desktop Standard) -->
            <div class="hidden sm:flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.service-order.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-colors font-bold text-sm">
                    Batal
                </a>
                <button type="submit" class="px-7 py-2.5 bg-[#FF6B00] hover:bg-orange-600 text-white rounded-xl transition-all font-bold text-sm shadow-md shadow-orange-500/20 cursor-pointer">
                    Simpan & Buat Service Order
                </button>
            </div>

            <!-- Form Action Buttons (Mobile Full-Width) -->
            <div class="flex sm:hidden flex-col gap-2 pt-2">
                <button type="submit" class="w-full py-3 bg-[#FF6B00] hover:bg-orange-600 text-white rounded-xl font-bold text-sm shadow-md shadow-orange-500/20 cursor-pointer text-center">
                    Simpan & Buat Service Order
                </button>
                <a href="{{ route('admin.service-order.index') }}" class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-sm text-center">
                    Batal
                </a>
            </div>

            <!-- Mobile Sticky Bottom Bar (< 640px) -->
            <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 px-4 py-3 sm:hidden flex items-center justify-between shadow-lg">
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase block">Total Order</span>
                    <span class="font-black text-base text-[#FF6B00] font-mono mobile-grand-total" id="grand_total_display_sticky">Rp {{ number_format($initialGrandTotal, 0, ',', '.') }}</span>
                </div>
                <button type="submit" class="px-5 py-2.5 bg-[#FF6B00] hover:bg-orange-600 text-white rounded-xl font-bold text-xs shadow-md shadow-orange-500/25">
                    Simpan Order ↗
                </button>
            </div>
        </form>
    </div>

    <!-- JavaScript untuk Dinamika Form -->
    <x-slot name="scripts">
        <script>
            let itemIndex = {{ count($renderedItems) + 10 }};

            window.loadCustomerVehicles = function(customerId) {
                const vehicleSelect = document.getElementById('vehicle_id');
                const vehicleHelp = document.getElementById('vehicle-help');
                if (!vehicleSelect) return;

                vehicleSelect.innerHTML = '<option value="">-- Memuat Kendaraan... --</option>';
                vehicleSelect.disabled = true;

                if (!customerId) {
                    vehicleSelect.innerHTML = '<option value="">-- Pilih Pelanggan Terlebih Dahulu --</option>';
                    if (vehicleHelp) vehicleHelp.innerText = 'Otomatis dimuat setelah memilih pelanggan.';
                    return;
                }

                const fetchUrl = `/admin/customer-vehicles/${customerId}`;

                fetch(fetchUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    vehicleSelect.disabled = false;
                    if (data.length === 0) {
                        vehicleSelect.innerHTML = '<option value="">-- Pelanggan Tidak Memiliki Kendaraan --</option>';
                        if (vehicleHelp) vehicleHelp.innerHTML = '<span class="text-red-500">Silakan tambahkan kendaraan untuk pelanggan ini di menu Master Data terlebih dahulu.</span>';
                    } else {
                        vehicleSelect.innerHTML = '<option value="">-- Pilih Kendaraan --</option>';
                        const oldVehicleId = "{{ old('vehicle_id') }}";
                        data.forEach(vehicle => {
                            const selected = oldVehicleId == vehicle.id ? 'selected' : '';
                            vehicleSelect.innerHTML += `<option value="${vehicle.id}" ${selected}>${vehicle.brand} ${vehicle.model} (${vehicle.license_plate})</option>`;
                        });
                        if (vehicleHelp) vehicleHelp.innerText = 'Pilih kendaraan yang akan diservis.';
                    }
                })
                .catch(err => {
                    vehicleSelect.innerHTML = '<option value="">-- Gagal memuat data --</option>';
                });
            };

            window.formatRupiah = function(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            };

            window.updateGrandTotal = function() {
                let grandTotal = 0;
                document.querySelectorAll('.item-subtotal-input').forEach(input => {
                    grandTotal += parseInt(input.value || 0);
                });
                const formatted = 'Rp ' + window.formatRupiah(grandTotal);
                const display = document.getElementById('grand_total_display');
                if (display) display.innerText = formatted;
                const displayMobile = document.getElementById('grand_total_display_mobile');
                if (displayMobile) displayMobile.innerText = formatted;
                const displaySticky = document.getElementById('grand_total_display_sticky');
                if (displaySticky) displaySticky.innerText = formatted;
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
                        emptyRow.style.display = '';
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

            window.addItemToTable = function(type, id, name, price, maxStock = null) {
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

                const qtyInputHTML = `<input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" ${maxAttr} class="w-full px-2 py-1.5 border border-slate-300 rounded-lg item-qty text-center text-sm font-semibold" data-price="${numPrice}" data-target-subtotal="subtotal_${itemIndex}" data-target-input="input_subtotal_${itemIndex}" oninput="updateRowSubtotal(this)">`;

                const html = `
                    <tr id="${rowId}" class="item-row bg-white hover:bg-slate-50/80 border border-slate-200 md:border-0 md:border-b md:border-slate-100 rounded-xl md:rounded-none block md:table-row p-4 md:p-0 mb-3 md:mb-0 shadow-sm md:shadow-none space-y-3 md:space-y-0">
                        <td class="block md:table-cell px-0 md:px-4 py-0 md:py-3.5">
                            <div class="flex items-start justify-between gap-2 md:block">
                                <div>
                                    <span class="text-sm font-bold text-slate-800 block">${name}</span>
                                    <span class="inline-block md:hidden mt-1">${typeLabel}</span>
                                </div>
                                <button type="button" class="md:hidden text-rose-500 hover:text-rose-700 p-1.5 rounded-lg bg-rose-50 border border-rose-100" onclick="removeItemRow('${rowId}')" title="Hapus Item">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                            <input type="hidden" name="items[${itemIndex}][type]" value="${type}">
                            <input type="hidden" name="items[${itemIndex}][item_id]" value="${id}">
                        </td>
                        <td class="hidden md:table-cell px-4 py-3.5">${typeLabel}</td>
                        <td class="block md:table-cell px-0 md:px-4 py-0 md:py-3.5 text-xs md:text-sm text-slate-500 md:text-slate-700">
                            <span class="md:hidden text-slate-400 font-medium">Harga: </span>
                            <span>Rp ${window.formatRupiah(numPrice)}</span>
                            <input type="hidden" name="items[${itemIndex}][price]" value="${numPrice}">
                        </td>
                        <td class="block md:table-cell px-0 md:px-4 py-0 md:py-3.5">
                            <div class="flex items-center justify-between md:justify-center gap-2">
                                <span class="md:hidden text-xs text-slate-500 font-medium">Qty:</span>
                                <div class="w-28">${qtyInputHTML}</div>
                            </div>
                        </td>
                        <td class="block md:table-cell px-0 md:px-4 py-0 md:py-3.5 text-sm font-bold text-slate-800">
                            <div class="flex items-center justify-between md:justify-start gap-2 pt-2 md:pt-0 border-t border-slate-100 md:border-0">
                                <span class="md:hidden text-xs text-slate-500 font-medium">Subtotal:</span>
                                <span id="subtotal_${itemIndex}" class="text-[#FF6B00] md:text-slate-800 font-black md:font-bold">Rp ${window.formatRupiah(numPrice)}</span>
                            </div>
                            <input type="hidden" class="item-subtotal-input" id="input_subtotal_${itemIndex}" value="${numPrice}">
                        </td>
                        <td class="hidden md:table-cell px-4 py-3.5 text-center">
                            <button type="button" class="text-rose-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors" onclick="removeItemRow('${rowId}')" title="Hapus Item">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
                window.addItemToTable('service', id, name, price);
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

                window.addItemToTable('sparepart', id, name, price, stock);
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

            // Auto-load customer vehicles if already selected
            const customerEl = document.getElementById('customer_id');
            if (customerEl && customerEl.value) {
                window.loadCustomerVehicles(customerEl.value);
            }
        </script>
    </x-slot>
</x-app-layout>
