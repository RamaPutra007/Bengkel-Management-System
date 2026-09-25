<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Template WhatsApp') }}
            </h2>
            <p class="text-sm text-slate-500">Kelola template pesan WhatsApp untuk berbagai keperluan pelanggan bengkel</p>
        </div>
    </x-slot>

    <div class="py-12" x-data="templateManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Search & Filter -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-8">
                <form action="{{ route('admin.whatsapp.template') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <label class="sr-only">Cari Template</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode template..." class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl focus:ring-[#FF6B00] focus:border-[#FF6B00] sm:text-sm bg-slate-50">
                        </div>
                    </div>
                    <div class="w-full md:w-48">
                        <select name="status" class="block w-full pl-3 pr-10 py-2 border border-slate-200 rounded-xl focus:ring-[#FF6B00] focus:border-[#FF6B00] sm:text-sm bg-slate-50" onchange="this.form.submit()">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-2 bg-slate-800 text-white rounded-xl hover:bg-slate-700 transition-colors text-sm font-medium">Cari</button>
                    @if(request()->has('search') || request()->has('status'))
                    <a href="{{ route('admin.whatsapp.template') }}" class="px-6 py-2 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-colors text-sm font-medium text-center">Reset</a>
                    @endif
                </form>
            </div>

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($templates as $template)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm flex flex-col overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-5 border-b border-slate-50 flex items-start justify-between bg-slate-50/50">
                            <div>
                                <h3 class="font-bold text-slate-800 text-lg leading-tight mb-1">{{ $template->name }}</h3>
                                <p class="text-xs font-mono text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200 inline-block">{{ $template->code }}</p>
                            </div>
                            <div>
                                @if($template->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                        Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="mb-4 flex-1">
                                <p class="text-sm text-slate-600 whitespace-pre-line line-clamp-4 font-mono text-xs">{{ $template->content }}</p>
                            </div>
                            
                            <div class="mt-auto">
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Variabel Tersedia</p>
                                <div class="flex flex-wrap gap-1.5 mb-4">
                                    @php
                                        $vars = is_array($template->variables) ? $template->variables : json_decode($template->variables, true) ?? [];
                                    @endphp
                                    @foreach($vars as $var)
                                        <span class="inline-block px-1.5 py-0.5 bg-orange-50 text-[#FF6B00] text-[10px] font-mono rounded border border-orange-100">
                                            { {{$var}} }
                                        </span>
                                    @endforeach
                                </div>
                                
                                <button type="button" @click='openEditModal(@json($template))' class="w-full py-2 bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors text-sm font-medium flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit Template
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $templates->links() }}
            </div>

            <!-- Edit Modal -->
            <div x-show="isEditing" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    
                    <div x-show="isEditing" x-transition.opacity class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" @click="isEditing = false" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="isEditing" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                        <form :action="`{{ url('admin/whatsapp/template') }}/${editData.id}`" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-slate-100">
                                <div class="flex justify-between items-center mb-5">
                                    <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">
                                        Edit Template WhatsApp
                                    </h3>
                                    <button type="button" @click="isEditing = false" class="text-slate-400 hover:text-slate-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Editor Side -->
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Template <span class="text-red-500">*</span></label>
                                            <input type="text" name="name" x-model="editData.name" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-[#FF6B00] focus:border-[#FF6B00] text-sm" required>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Kode Template (Read-only)</label>
                                            <input type="text" name="code" x-model="editData.code" class="w-full px-3 py-2 border border-slate-200 bg-slate-100 text-slate-500 rounded-lg text-sm font-mono" readonly>
                                            <p class="mt-1 text-xs text-slate-500">Kode digunakan oleh sistem secara otomatis dan tidak dapat diubah.</p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Isi Pesan <span class="text-red-500">*</span></label>
                                            <textarea name="content" x-model="editData.content" @input="updatePreview()" rows="10" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-[#FF6B00] focus:border-[#FF6B00] text-sm font-mono resize-none" required></textarea>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <input type="checkbox" name="is_active" value="1" x-model="editData.is_active" class="h-4 w-4 text-[#FF6B00] focus:ring-[#FF6B00] border-gray-300 rounded">
                                            <label class="ml-2 block text-sm text-gray-900 font-medium">Aktifkan Template Ini</label>
                                        </div>
                                    </div>
                                    
                                    <!-- Preview Side -->
                                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col">
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">Live Preview</p>
                                        
                                        <div class="flex-1 bg-white p-4 rounded-xl border border-slate-100 shadow-sm relative overflow-hidden">
                                            <!-- WA Chat Background Pattern -->
                                            <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'1\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'3\'/%3E%3Ccircle cx=\'13\' cy=\'13\' r=\'3\'/%3E%3C/g%3E%3C/svg%3E');"></div>
                                            
                                            <!-- Chat Bubble -->
                                            <div class="relative bg-emerald-50 border border-emerald-100 rounded-tr-xl rounded-b-xl p-3 inline-block max-w-full">
                                                <p class="text-sm text-slate-800 whitespace-pre-wrap font-sans break-words" x-html="previewContent"></p>
                                                <div class="text-right mt-1">
                                                    <span class="text-[10px] text-slate-400">12:00</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 p-3 bg-white border border-orange-100 rounded-lg">
                                            <p class="text-xs font-bold text-slate-700 mb-2">Variabel yang diizinkan:</p>
                                            <div class="flex flex-wrap gap-1.5">
                                                <template x-for="v in getVariablesArray()" :key="v">
                                                    <span class="inline-block px-1.5 py-0.5 bg-orange-50 text-[#FF6B00] text-[10px] font-mono rounded border border-orange-100" x-text="'{' + v + '}'"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl">
                                <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2.5 bg-[#FF6B00] text-base font-medium text-white hover:bg-orange-600 sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan Perubahan
                                </button>
                                <button type="button" @click="isEditing = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-6 py-2.5 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <x-slot name="scripts">
        <script>
            function templateManager() {
                return {
                    isEditing: false,
                    editData: {
                        id: null,
                        name: '',
                        code: '',
                        content: '',
                        is_active: false,
                        variables: []
                    },
                    previewContent: '',
                    
                    // Contoh data untuk preview
                    dummyData: {
                        nama: 'Budi Santoso',
                        nama_bengkel: 'SI BENGKEL',
                        nomor_order: 'SO-202609-001',
                        kendaraan: 'Honda Vario 150',
                        plat_nomor: 'B 1234 ABC',
                        tanggal: '25 Sep 2026',
                        keluhan: 'Mesin brebet saat digas',
                        hasil_pemeriksaan: 'Busi mati, filter udara kotor',
                        rekomendasi_service: 'Ganti busi, bersihkan filter, ganti oli',
                        estimasi_biaya: '150.000',
                        layanan: 'Servis Ringan + Ganti Oli',
                        estimasi_selesai: '14:30 WIB',
                        total: '150.000',
                        alamat_bengkel: 'Jl. Contoh No. 123',
                        jam_operasional: '08:00 - 17:00',
                        nomor_invoice: 'INV-202609-001',
                        detail_layanan: '1x Servis CVT',
                        subtotal: '150.000',
                        diskon: '0',
                        status_pembayaran: 'LUNAS',
                        metode_pembayaran: 'QRIS',
                        tanggal_pembayaran: '25 Sep 2026 13:00',
                        dibayar: '50.000',
                        sisa_tagihan: '100.000',
                        tanggal_service_terakhir: '25 Jul 2026',
                        qris_url: 'https://api.qrserver.com/.../QRIS',
                        nomor_polisi: 'B 1234 ABC'
                    },

                    getVariablesArray() {
                        if (!this.editData.variables) return [];
                        if (Array.isArray(this.editData.variables)) return this.editData.variables;
                        try {
                            return JSON.parse(this.editData.variables) || [];
                        } catch (e) {
                            return [];
                        }
                    },

                    openEditModal(template) {
                        this.editData = { ...template };
                        this.editData.is_active = template.is_active == 1;
                        this.isEditing = true;
                        this.updatePreview();
                    },

                    updatePreview() {
                        let text = this.editData.content || '';
                        
                        // Replace simple markdown
                        text = text.replace(/\*(.*?)\*/g, '<strong>$1</strong>');
                        text = text.replace(/_(.*?)_/g, '<em>$1</em>');
                        text = text.replace(/~(.*?)~/g, '<del>$1</del>');
                        
                        // Replace variables
                        const vars = this.getVariablesArray();
                        vars.forEach(v => {
                            const val = this.dummyData[v] || `[${v}]`;
                            const regex = new RegExp(`\\{${v}\\}`, 'g');
                            text = text.replace(regex, `<span class="bg-amber-100 text-amber-800 px-1 rounded text-xs">${val}</span>`);
                        });

                        // Show unknown variables
                        text = text.replace(/\{([a-zA-Z0-9_]+)\}/g, '<span class="bg-red-100 text-red-800 px-1 rounded text-xs">[$1] (Tidak valid)</span>');

                        this.previewContent = text;
                    }
                }
            }
        </script>
    </x-slot>
</x-app-layout>
