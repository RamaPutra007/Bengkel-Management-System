<aside :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" class="fixed inset-y-0 left-0 z-50 w-72 bg-white text-slate-600 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 shadow-[4px_0_24px_rgba(0,0,0,0.02)] border-r border-slate-100 flex flex-col font-sans">
    <!-- Logo -->
    <div class="flex items-center justify-between px-8 h-20 border-b border-slate-100">
        <a href="/" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-gradient-to-br from-[#FF6B00] to-orange-400 rounded-xl flex items-center justify-center text-white font-bold shadow-sm shadow-orange-500/20">
                B
            </div>
            <span class="font-bold text-xl tracking-tight text-slate-800">Bengkel<span class="text-[#FF6B00]">Pro</span></span>
        </a>
        <!-- Close button for mobile -->
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600 bg-slate-50 p-2 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- Navigation Links -->
    <nav id="sidebar-nav" class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto scrollbar-hide">
        
        @role('OWNER')
            <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('owner.dashboard') ? 'bg-orange-50 text-[#FF6B00] font-semibold' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <svg class="w-5 h-5 {{ request()->routeIs('owner.dashboard') ? 'text-[#FF6B00]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Dashboard Utama</span>
            </a>
            
            <div class="px-4 py-3 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pantau Bisnis</div>
            <a href="{{ route('owner.operasional') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('owner.operasional') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('owner.operasional') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Operasional & Servis</span>
            </a>
            
            <a href="{{ route('owner.inventaris') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('owner.inventaris') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('owner.inventaris') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Inventaris Suku Cadang</span>
            </a>

            <a href="{{ route('owner.keuangan') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('owner.keuangan') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('owner.keuangan') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Keuangan & Tagihan</span>
            </a>

            <a href="{{ route('owner.laporan') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('owner.laporan') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('owner.laporan') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Laporan Kinerja</span>
            </a>

            <div class="px-4 py-3 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sistem</div>
            <a href="{{ route('owner.pengguna.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('owner.pengguna.*') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('owner.pengguna.*') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Manajemen Pengguna</span>
            </a>
        @endrole

        @role('ADMIN')
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-orange-50 text-[#FF6B00] font-semibold' : 'hover:bg-slate-50 hover:text-slate-900' }} transition-all duration-200">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-[#FF6B00]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Dashboard Utama</span>
            </a>
            
            <div class="px-4 py-3 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Master Data</div>
            <a href="{{ route('admin.customer.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.customer.*') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.customer.*') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Pelanggan</span>
            </a>
            <a href="{{ route('admin.vehicle.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.vehicle.*') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.vehicle.*') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Kendaraan</span>
            </a>
            <a href="{{ route('admin.mechanic.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.mechanic.*') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.mechanic.*') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Mekanik</span>
            </a>
            <a href="{{ route('admin.service.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.service.*') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.service.*') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Layanan</span>
            </a>

            <div class="px-4 py-3 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Operasional</div>
            <a href="{{ route('admin.service-order.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.service-order.*') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.service-order.*') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Order Servis</span>
            </a>
            <a href="{{ route('admin.inspection.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.inspection.*') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.inspection.*') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Inspection</span>
            </a>

            <div class="px-4 py-3 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Inventaris</div>
            <a href="{{ route('admin.sparepart.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.sparepart.*') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.sparepart.*') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Suku Cadang</span>
            </a>
            <a href="{{ route('admin.inventory-transaction.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.inventory-transaction.*') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.inventory-transaction.*') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Stok & Pergerakan</span>
            </a>
            
            <div class="px-4 py-3 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Laporan</div>
            <a href="{{ route('admin.invoice.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.invoice.*') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.invoice.*') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Invoice Tagihan</span>
            </a>
            <a href="{{ route('admin.laporan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.laporan.*') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.laporan.*') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Laporan & Kinerja</span>
            </a>

            <div class="px-4 py-3 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sistem & Integrasi</div>
            
            <div x-data="{ waOpen: {{ request()->routeIs('admin.whatsapp.*') ? 'true' : 'false' }} }">
                <button @click="waOpen = !waOpen" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.whatsapp.*') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 text-slate-500 hover:text-slate-900' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.whatsapp.*') ? 'text-[#FF6B00]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <span>WhatsApp Bot</span>
                    </div>
                    <svg :class="{'rotate-180': waOpen}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                
                <div x-show="waOpen" x-transition class="mt-2 ml-6 pl-4 border-l border-slate-200 space-y-1">
                    <a href="{{ route('admin.whatsapp.index') }}" class="relative block px-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.whatsapp.index') ? 'text-[#FF6B00] font-medium bg-orange-50/50' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                        <span class="absolute -left-4 top-1/2 -translate-y-1/2 w-4 border-t {{ request()->routeIs('admin.whatsapp.index') ? 'border-[#FF6B00]' : 'border-slate-200' }}"></span>
                        Connect
                    </a>
                    <a href="{{ route('admin.whatsapp.config') }}" class="relative block px-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.whatsapp.config') ? 'text-[#FF6B00] font-medium bg-orange-50/50' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                        <span class="absolute -left-4 top-1/2 -translate-y-1/2 w-4 border-t {{ request()->routeIs('admin.whatsapp.config') ? 'border-[#FF6B00]' : 'border-slate-200' }}"></span>
                        Config
                    </a>
                    <a href="{{ route('admin.whatsapp.template') }}" class="relative block px-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.whatsapp.template') ? 'text-[#FF6B00] font-medium bg-orange-50/50' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                        <span class="absolute -left-4 top-1/2 -translate-y-1/2 w-4 border-t {{ request()->routeIs('admin.whatsapp.template') ? 'border-[#FF6B00]' : 'border-slate-200' }}"></span>
                        Template Text
                    </a>
                </div>
            </div>
        @endrole

        @role('KASIR')
            <a href="{{ route('kasir.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('kasir.dashboard') ? 'bg-orange-50 text-[#FF6B00] font-semibold' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <svg class="w-5 h-5 {{ request()->routeIs('kasir.dashboard') ? 'text-[#FF6B00]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Dashboard Utama</span>
            </a>
            
            <div class="px-4 py-3 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Transaksi</div>
            <a href="{{ route('kasir.invoice.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('kasir.invoice.*') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('kasir.invoice.*') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Invoice</span>
            </a>
            <a href="{{ route('kasir.payment.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('kasir.payment.*') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('kasir.payment.*') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Pembayaran</span>
            </a>
            <a href="{{ route('kasir.transaction-history.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('kasir.transaction-history.*') ? 'bg-orange-50 text-[#FF6B00] font-medium' : 'hover:bg-slate-50 hover:text-slate-900 text-slate-500' }} transition-all duration-200">
                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('kasir.transaction-history.*') ? 'bg-[#FF6B00]' : 'bg-slate-300' }}"></div>
                <span>Riwayat Transaksi</span>
            </a>
        @endrole
    </nav>
    
    <!-- Footer Profile -->
    <div class="p-4 border-t border-slate-100">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center border border-slate-200 group-hover:border-slate-300">
                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-500 truncate capitalize">{{ strtolower(Auth::user()->roles->pluck('name')[0] ?? 'User') }}</p>
            </div>
        </a>
    </div>
</aside>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebarNav = document.getElementById("sidebar-nav");
        if (sidebarNav) {
            // Restore scroll position
            const scrollPos = localStorage.getItem("sidebar-scroll-pos");
            if (scrollPos) {
                sidebarNav.scrollTop = parseInt(scrollPos, 10);
            }
            
            // Save scroll position on scroll
            sidebarNav.addEventListener("scroll", function() {
                localStorage.setItem("sidebar-scroll-pos", sidebarNav.scrollTop);
            });
        }
    });
</script>
