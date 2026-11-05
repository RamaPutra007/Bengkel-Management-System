<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.service-order.index') }}" class="text-slate-400 hover:text-[#FF6B00] transition-colors p-1 rounded-lg hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="font-bold text-lg sm:text-xl text-slate-800 leading-tight">
                        {{ __('Edit Service Order') }} <span class="text-[#FF6B00]">#{{ $serviceOrder->order_number }}</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5 sm:hidden">Perbarui status pengerjaan, mekanik, atau rincian item</p>
                </div>
            </div>
            <!-- Indicator for desktop -->
            <div class="hidden sm:flex items-center gap-2">
                <span class="text-xs font-medium text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                    Status: <strong class="uppercase text-slate-700">{{ $serviceOrder->status }}</strong>
                </span>
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

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm shadow-sm">
                <div class="font-bold mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>Mohon periksa kesalahan input berikut:</span>
                </div>
                <ul class="list-disc pl-5 space-y-0.5 text-xs text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Rangkuman Order Singkat (PC & Mobile Optimized) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-4 sm:p-5">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="border-r border-slate-100 pr-2">
                    <span class="text-xs text-slate-400 block font-medium">Nomor Order</span>
                    <span class="font-bold text-sm sm:text-base text-[#FF6B00]">{{ $serviceOrder->order_number }}</span>
                </div>
                <div class="border-r border-slate-100 pr-2">
                    <span class="text-xs text-slate-400 block font-medium">Pelanggan</span>
                    <span class="font-bold text-sm sm:text-base text-slate-800 truncate block">{{ $serviceOrder->customer->name ?? '-' }}</span>
                    <span class="text-xs text-slate-500 truncate block">{{ $serviceOrder->vehicle->license_plate ?? '-' }} ({{ $serviceOrder->vehicle->model ?? '' }})</span>
                </div>
                <div class="border-r border-slate-100 pr-2">
                    <span class="text-xs text-slate-400 block font-medium">Status Saat Ini</span>
                    @php
                        $statusClasses = [
                            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'in_progress' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                        ];
                        $badgeClass = $statusClasses[$serviceOrder->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                    @endphp
                    <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeClass }}">
                        {{ strtoupper($serviceOrder->status) }}
                    </span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-medium">Total Tagihan Awal</span>
                    <span class="font-black text-sm sm:text-base text-slate-800">Rp {{ number_format($serviceOrder->total_price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.service-order.update', $serviceOrder->id) }}" method="POST" id="so-edit-form" onsubmit="return validateOrderForm(event)">
            @csrf
            @method('PUT')

            <!-- Section 1: Informasi Mekanik & Status Pengerjaan -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 mb-6 overflow-hidden">
                <div class="bg-slate-50/80 px-4 sm:px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-[#FF6B00] text-white text-xs font-bold flex items-center justify-center">1</span>
                        <span>Penugasan Mekanik & Status Progres</span>
                    </h3>
                </div>
                <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="mechanic_id" class="block text-sm font-medium text-slate-700 mb-1">Mekanik Bertugas</label>
                        <select name="mechanic_id" id="mechanic_id" class="w-full px-4 py-2.5 border @error('mechanic_id') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] @enderror rounded-xl text-sm bg-white transition-all">
                            <option value="">-- Belum Ditentukan / Tidak Ada --</option>
                            @foreach($mechanics as $mechanic)
                                <option value="{{ $mechanic->id }}" {{ old('mechanic_id', $serviceOrder->mechanic_id) == $mechanic->id ? 'selected' : '' }}>
                                    {{ $mechanic->name }} ({{ $mechanic->specialization ?? 'Umum' }})
                                    @if($mechanic->status != 'active') [{{ strtoupper($mechanic->status) }}] @endif
                                </option>
                            @endforeach
                        </select>
                        @error('mechanic_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status Progres Pekerjaan <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required class="w-full px-4 py-2.5 border @error('status') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] @enderror rounded-xl text-sm bg-white transition-all font-medium">
                            <option value="pending" {{ old('status', $serviceOrder->status) == 'pending' ? 'selected' : '' }}>🟡 Menunggu (Pending)</option>
                            <option value="in_progress" {{ old('status', $serviceOrder->status) == 'in_progress' ? 'selected' : '' }}>🔵 Sedang Dikerjakan (In Progress)</option>
                            <option value="completed" {{ old('status', $serviceOrder->status) == 'completed' ? 'selected' : '' }}>🟢 Selesai (Completed)</option>
                            <option value="cancelled" {{ old('status', $serviceOrder->status) == 'cancelled' ? 'selected' : '' }}>🔴 Dibatalkan (Cancelled)</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Catatan Pengerjaan / Keluhan Pelanggan</label>
                        <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2.5 border @error('notes') border-red-300 focus:ring-red-500/20 focus:border-red-500 @else border-slate-200 focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] @enderror rounded-xl text-sm transition-all" placeholder="Tambahkan catatan jika ada keluhan tambahan atau catatan teknisi...">{{ old('notes', $serviceOrder->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Rincian Pengerjaan & Suku Cadang (Dual Table on PC vs Cards on Mobile) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 mb-6 overflow-hidden">
                <div class="bg-slate-50/80 px-4 sm:px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-[#FF6B00] text-white text-xs font-bold flex items-center justify-center">2</span>
                        <span>Rincian Jasa & Suku Cadang</span>
                    </h3>
                    <span class="text-xs text-slate-400 hidden sm:inline">Opsional (Wajib jika status Selesai)</span>
                </div>
                
                <div class="p-4 sm:p-6 space-y-6">
                    <!-- Quick Selectors: Dual PC vs HP layout -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-3.5 sm:p-4 rounded-xl border border-slate-200/60">
                        <!-- Jasa -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                Tambah Jasa Servis
                            </label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <select id="add_service_select" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                    <option value="">-- Pilih Jasa Layanan --</option>
                                    @foreach($services as $srv)
                                        <option value="{{ $srv->id }}" data-price="{{ $srv->price }}" data-name="{{ $srv->name }}">{{ $srv->name }} - Rp {{ number_format($srv->price, 0, ',', '.') }}</option>
                                    @endforeach
                                </select>
                                <button type="button" id="btn_add_service" onclick="addServiceItem()" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold text-xs transition-colors shrink-0 cursor-pointer shadow-sm flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    <span>+ Jasa</span>
                                </button>
                            </div>
                        </div>

                        <!-- Sparepart -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Tambah Suku Cadang (Sparepart)
                            </label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <select id="add_sparepart_select" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                                    <option value="">-- Pilih Suku Cadang --</option>
                                    @foreach($spareparts as $sp)
                                        @php
                                            $existingItemQty = $serviceOrder->items->where('type', 'sparepart')->where('sparepart_id', $sp->id)->first()->quantity ?? 0;
                                            $availableStock = $sp->stock + $existingItemQty;
                                        @endphp
                                        <option value="{{ $sp->id }}" data-price="{{ $sp->price }}" data-name="{{ $sp->name }}" data-stock="{{ $availableStock }}">
                                            {{ $sp->name }} (Stok: {{ $sp->stock }}) - Rp {{ number_format($sp->price, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" id="btn_add_sparepart" onclick="addSparepartItem()" class="w-full sm:w-auto px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 font-semibold text-xs transition-colors shrink-0 cursor-pointer shadow-sm flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    <span>+ Sparepart</span>
                                </button>
                            </div>
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

                    <!-- Responsive Dual Table / Mobile Cards View -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-left border-collapse block md:table" id="items_table">
                            <!-- Desktop Header: Hidden on Mobile -->
                            <thead class="hidden md:table-header-group">
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Item</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">Tipe</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider w-32">Harga</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider w-28 text-center">Qty</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider w-36">Subtotal</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center w-16">Hapus</th>
                                </tr>
                            </thead>
                            
                            <!-- Body: Table rows on PC, Touch Cards on Mobile -->
                            <tbody class="block md:table-row-group p-3 md:p-0 space-y-3 md:space-y-0 divide-y-0 md:divide-y md:divide-slate-100" id="items_tbody">
                                @forelse($renderedItems as $idx => $item)
                                    <tr id="item_row_{{ $idx }}" class="item-row block md:table-row bg-slate-50/70 md:bg-transparent p-3.5 md:p-0 rounded-xl md:rounded-none border md:border-b border-slate-200/80 md:border-slate-100 mb-3 md:mb-0 space-y-2 md:space-y-0">
                                        <!-- Item Title & Hidden Inputs -->
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

                                        <!-- Type (Desktop) -->
                                        <td class="hidden md:table-cell px-4 py-3.5">
                                            @if($item['type'] === 'service')
                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-semibold">Jasa</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-xs font-semibold">Sparepart</span>
                                            @endif
                                        </td>

                                        <!-- Unit Price -->
                                        <td class="block md:table-cell px-0 md:px-4 py-0 md:py-3.5 text-xs md:text-sm text-slate-500 md:text-slate-700">
                                            <span class="md:hidden text-slate-400 font-medium">Harga: </span>
                                            <span>Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                            <input type="hidden" name="items[{{ $idx }}][price]" value="{{ $item['price'] }}">
                                        </td>

                                        <!-- Quantity -->
                                        <td class="block md:table-cell px-0 md:px-4 py-0 md:py-3.5">
                                            <div class="flex items-center justify-between md:justify-center gap-2">
                                                <span class="md:hidden text-xs text-slate-500 font-medium">Qty:</span>
                                                <div class="w-28">
                                                    <input type="number" name="items[{{ $idx }}][quantity]" value="{{ $item['quantity'] }}" min="1" {{ $item['max_stock'] !== null ? 'max=' . $item['max_stock'] : '' }} class="w-full px-2 py-1.5 border border-slate-300 rounded-lg item-qty text-center font-bold text-sm bg-white" data-price="{{ $item['price'] }}" data-target-subtotal="subtotal_{{ $idx }}" data-target-input="input_subtotal_{{ $idx }}" oninput="updateRowSubtotal(this)">
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Subtotal -->
                                        <td class="block md:table-cell px-0 md:px-4 py-0 md:py-3.5 text-sm font-bold text-slate-800">
                                            <div class="flex items-center justify-between md:justify-start gap-2 pt-2 md:pt-0 border-t border-slate-100 md:border-0">
                                                <span class="md:hidden text-xs text-slate-500 font-medium">Subtotal:</span>
                                                <span id="subtotal_{{ $idx }}" class="text-[#FF6B00] md:text-slate-800 font-black md:font-bold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                            </div>
                                            <input type="hidden" class="item-subtotal-input" id="input_subtotal_{{ $idx }}" value="{{ $item['subtotal'] }}">
                                        </td>

                                        <!-- Delete Button (Desktop) -->
                                        <td class="hidden md:table-cell px-4 py-3.5 text-center">
                                            <button type="button" class="text-rose-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer" onclick="removeItemRow('item_row_{{ $idx }}')" title="Hapus Item">
                                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty_row" class="block md:table-row">
                                        <td colspan="6" class="block md:table-cell px-4 py-8 text-center text-slate-400 text-sm italic">
                                            Belum ada item yang ditambahkan. Silakan pilih jasa atau suku cadang di atas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            <!-- Desktop Table Footer: Hidden on Mobile -->
                            <tfoot class="hidden md:table-footer-group">
                                <tr class="bg-slate-50 border-t border-slate-200">
                                    <td colspan="4" class="px-4 py-4 text-right font-bold text-slate-700">Total Estimasi Biaya:</td>
                                    <td colspan="2" class="px-4 py-4 font-black text-xl text-[#FF6B00]" id="grand_total_display">Rp {{ number_format($initialGrandTotal, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Mobile Subtotal Card (Visible only on < md) -->
                    <div class="md:hidden bg-slate-50 p-4 rounded-xl border border-slate-200 flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-600 uppercase">Total Estimasi Biaya:</span>
                        <span class="text-lg font-black text-[#FF6B00] mobile-grand-total">Rp {{ number_format($initialGrandTotal, 0, ',', '.') }}</span>
                    </div>

                    @error('items')
                        <p class="text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Desktop Form Action Buttons: Hidden on Mobile Sticky Bottom -->
            <div class="hidden sm:flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.service-order.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 font-semibold text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#FF6B00] text-white rounded-xl hover:bg-orange-600 font-bold text-sm shadow-md transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan Perubahan Order</span>
                </button>
            </div>

            <!-- Mobile Sticky Bottom Bar (Visible only on Mobile) -->
            <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 p-3 sm:hidden shadow-[0_-4px_12px_rgba(0,0,0,0.06)] flex items-center justify-between gap-3">
                <div>
                    <span class="text-[11px] text-slate-500 block uppercase font-medium">Total Tagihan</span>
                    <span class="text-base font-black text-[#FF6B00] mobile-grand-total">Rp {{ number_format($initialGrandTotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.service-order.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold">
                        Batal
                    </a>
                    <button type="submit" form="so-edit-form" class="px-5 py-2.5 bg-[#FF6B00] text-white rounded-xl font-bold text-sm shadow-md hover:bg-orange-600 transition-all flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </div>

        </form>
    </div>

    <!-- JavaScript untuk Dinamika Form Responsif -->
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
                
                // Update PC display
                const display = document.getElementById('grand_total_display');
                if (display) {
                    display.innerText = 'Rp ' + window.formatRupiah(grandTotal);
                }
                
                // Update Mobile displays (subtotal card & sticky bar)
                document.querySelectorAll('.mobile-grand-total').forEach(el => {
                    el.innerText = 'Rp ' + window.formatRupiah(grandTotal);
                });
            };

            window.checkEmpty = function() {
                const tbody = document.getElementById('items_tbody');
                if (!tbody) return;
                const rows = tbody.querySelectorAll('.item-row');
                const emptyRow = document.getElementById('empty_row');
                if (rows.length === 0) {
                    if (!emptyRow) {
                        tbody.insertAdjacentHTML('beforeend', `
                            <tr id="empty_row" class="block md:table-row">
                                <td colspan="6" class="block md:table-cell px-4 py-8 text-center text-slate-400 text-sm italic">
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

                const qtyInputHTML = `<input type="number" name="items[${itemIndex}][quantity]" value="${numQty}" min="1" ${maxAttr} class="w-full px-2 py-1.5 border border-slate-300 rounded-lg item-qty text-center font-bold text-sm bg-white" data-price="${numPrice}" data-target-subtotal="subtotal_${itemIndex}" data-target-input="input_subtotal_${itemIndex}" oninput="updateRowSubtotal(this)">`;

                const html = `
                    <tr id="${rowId}" class="item-row block md:table-row bg-slate-50/70 md:bg-transparent p-3.5 md:p-0 rounded-xl md:rounded-none border md:border-b border-slate-200/80 md:border-slate-100 mb-3 md:mb-0 space-y-2 md:space-y-0">
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
                                <span id="subtotal_${itemIndex}" class="text-[#FF6B00] md:text-slate-800 font-black md:font-bold">Rp ${window.formatRupiah(subtotal)}</span>
                            </div>
                            <input type="hidden" class="item-subtotal-input" id="input_subtotal_${itemIndex}" value="${subtotal}">
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
                const statusEl = document.getElementById('status');
                const status = statusEl ? statusEl.value : '';
                const count = document.querySelectorAll('.item-subtotal-input').length;
                if (status === 'completed' && count === 0) {
                    if (e) e.preventDefault();
                    alert('Ketika status pengerjaan diset "Selesai (Completed)", Anda wajib menambahkan minimal 1 item (Jasa atau Suku Cadang).');
                    return false;
                }
                return true;
            };
        </script>
    </x-slot>
</x-app-layout>