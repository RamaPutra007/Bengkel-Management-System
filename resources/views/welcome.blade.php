<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BengkelPro - Solusi Servis Kendaraan Anda</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Fallback Tailwind CSS (CDN) jika Vite sedang tidak berjalan -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: '#FF6B00',
                        secondary: '#1E293B',
                    }
                }
            }
        }
    </script>
    
    <!-- AlpineJS for mobile menu -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- AOS CSS for animations -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased selection:bg-primary selection:text-white" x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">

    <!-- Navbar -->
    <nav :class="{ 'bg-white shadow-md' : scrolled, 'bg-transparent' : !scrolled }" class="fixed w-full z-50 transition-all duration-300 py-4 top-0 left-0">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-primary/30 font-heading">
                    B
                </div>
                <span class="font-heading font-bold text-2xl tracking-tight text-secondary">Bengkel<span class="text-primary">Pro</span></span>
            </div>
            
            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                <a href="#" class="hover:text-primary transition-colors">Beranda</a>
                <a href="#layanan" class="hover:text-primary transition-colors">Layanan</a>
                <a href="#booking" class="hover:text-primary transition-colors">Booking Servis</a>
                <a href="#tentang" class="hover:text-primary transition-colors">Tentang Kami</a>
            </div>

            <div class="hidden md:flex items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-secondary font-medium hover:text-primary transition-colors">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-full bg-secondary text-white font-semibold hover:bg-gray-800 transition-all shadow-md">Login Sistem</a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" class="md:hidden bg-white shadow-xl absolute top-full left-0 w-full flex flex-col py-4 px-6 gap-4 border-t border-gray-100" style="display: none;">
            <a href="#" class="text-gray-600 font-medium">Beranda</a>
            <a href="#layanan" class="text-gray-600 font-medium">Layanan</a>
            <a href="#booking" class="text-gray-600 font-medium">Booking Servis</a>
            <a href="#tentang" class="text-gray-600 font-medium">Tentang Kami</a>
            <hr class="border-gray-100">
            @auth
                <a href="{{ url('/dashboard') }}" class="text-primary font-medium">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-primary font-medium">Login Sistem</a>
            @endauth
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden bg-white">
        <!-- Abstract Shape -->
        <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3">
            <div class="w-[600px] h-[600px] bg-primary/5 rounded-full blur-3xl"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
            <h1 data-aos="fade-down" data-aos-duration="1000" class="font-heading text-4xl md:text-6xl lg:text-7xl font-extrabold text-secondary tracking-tight leading-tight mb-6 max-w-4xl mx-auto">
                Solusi Servis Kendaraan Anda, <br/>
                <span class="text-primary">Cepat dan Terpercaya</span>
            </h1>
            
            <p data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000" class="text-lg text-gray-500 max-w-2xl mx-auto mb-10 leading-relaxed">
                Kami menyediakan layanan perbaikan dan perawatan kendaraan terbaik dengan mekanik profesional, suku cadang berkualitas, dan harga transparan.
            </p>
            
            <div data-aos="zoom-in" data-aos-delay="400" data-aos-duration="800" class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#booking" class="w-full sm:w-auto px-8 py-4 rounded-full bg-primary text-white font-semibold text-lg hover:bg-[#E66000] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-primary/30">
                    Booking Servis
                </a>
                <a href="#layanan" class="w-full sm:w-auto px-8 py-4 rounded-full bg-white border border-gray-200 text-secondary font-semibold text-lg hover:bg-gray-50 transition-all">
                    Lihat Layanan
                </a>
            </div>
        </div>
    </section>

    <!-- Keunggulan Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8 text-center">
                <div data-aos="fade-up" data-aos-delay="100" class="flex flex-col items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-colors duration-300"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></div>
                    <span class="font-medium text-sm text-secondary">Mekanik Berpengalaman</span>
                </div>
                <div data-aos="fade-up" data-aos-delay="200" class="flex flex-col items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-colors duration-300"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></div>
                    <span class="font-medium text-sm text-secondary">Sparepart Berkualitas</span>
                </div>
                <div data-aos="fade-up" data-aos-delay="300" class="flex flex-col items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-colors duration-300"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    <span class="font-medium text-sm text-secondary">Harga Transparan</span>
                </div>
                <div data-aos="fade-up" data-aos-delay="400" class="flex flex-col items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-colors duration-300"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    <span class="font-medium text-sm text-secondary">Pengerjaan Profesional</span>
                </div>
                <div data-aos="fade-up" data-aos-delay="500" class="flex flex-col items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-colors duration-300"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg></div>
                    <span class="font-medium text-sm text-secondary">Riwayat Servis Jelas</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan Section -->
    <section id="layanan" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="font-heading text-3xl md:text-4xl font-bold text-secondary mb-4">Layanan Kami</h2>
                <p class="text-gray-500 max-w-2xl mx-auto text-lg">Berbagai solusi perawatan dan perbaikan untuk memastikan kendaraan Anda selalu dalam performa optimal.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card -->
                <div data-aos="zoom-in" data-aos-delay="100" class="p-8 rounded-2xl bg-gray-50 hover:bg-white hover:shadow-xl hover:shadow-primary/5 transition-all border border-gray-100 group">
                    <div class="w-14 h-14 rounded-xl bg-white shadow-sm flex items-center justify-center mb-6 text-primary group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-3 font-heading">Servis Berkala</h3>
                    <p class="text-gray-500 text-sm">Pengecekan menyeluruh sesuai dengan kilometer kendaraan Anda.</p>
                </div>
                <!-- Card -->
                <div data-aos="zoom-in" data-aos-delay="200" class="p-8 rounded-2xl bg-gray-50 hover:bg-white hover:shadow-xl hover:shadow-primary/5 transition-all border border-gray-100 group">
                    <div class="w-14 h-14 rounded-xl bg-white shadow-sm flex items-center justify-center mb-6 text-primary group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-3 font-heading">Ganti Oli</h3>
                    <p class="text-gray-500 text-sm">Penggantian oli mesin, transmisi, dan gardan menggunakan oli original.</p>
                </div>
                <!-- Card -->
                <div data-aos="zoom-in" data-aos-delay="300" class="p-8 rounded-2xl bg-gray-50 hover:bg-white hover:shadow-xl hover:shadow-primary/5 transition-all border border-gray-100 group">
                    <div class="w-14 h-14 rounded-xl bg-white shadow-sm flex items-center justify-center mb-6 text-primary group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-3 font-heading">Servis Mesin</h3>
                    <p class="text-gray-500 text-sm">Perbaikan mesin ringan hingga turun mesin berat dengan garansi.</p>
                </div>
                <!-- Card -->
                <div data-aos="zoom-in" data-aos-delay="400" class="p-8 rounded-2xl bg-gray-50 hover:bg-white hover:shadow-xl hover:shadow-primary/5 transition-all border border-gray-100 group">
                    <div class="w-14 h-14 rounded-xl bg-white shadow-sm flex items-center justify-center mb-6 text-primary group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-3 font-heading">Kelistrikan</h3>
                    <p class="text-gray-500 text-sm">Diagnosa dan perbaikan masalah kelistrikan, ECU, dan sistem injeksi.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cara Kerja -->
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="font-heading text-3xl md:text-4xl font-bold text-secondary mb-4">Cara Kerja BengkelPro</h2>
                <p class="text-gray-500 max-w-2xl mx-auto text-lg">Proses pelayanan yang transparan, mudah, dan terstruktur.</p>
            </div>
            
            <div class="flex flex-col md:flex-row justify-between items-start relative">
                <!-- Connecting line -->
                <div class="hidden md:block absolute top-8 left-10 right-10 h-0.5 bg-gray-200 z-0"></div>
                
                <div data-aos="fade-right" data-aos-delay="100" class="relative z-10 flex flex-col items-center text-center w-full md:w-1/5 mb-8 md:mb-0">
                    <div class="w-16 h-16 rounded-full bg-white border-4 border-gray-50 shadow-md flex items-center justify-center font-bold text-xl text-primary mb-4 transition-transform hover:scale-110">1</div>
                    <h4 class="font-bold text-secondary mb-2">Booking</h4>
                    <p class="text-sm text-gray-500">Pelanggan datang atau melakukan reservasi servis.</p>
                </div>
                <div data-aos="fade-right" data-aos-delay="200" class="relative z-10 flex flex-col items-center text-center w-full md:w-1/5 mb-8 md:mb-0">
                    <div class="w-16 h-16 rounded-full bg-white border-4 border-gray-50 shadow-md flex items-center justify-center font-bold text-xl text-primary mb-4 transition-transform hover:scale-110">2</div>
                    <h4 class="font-bold text-secondary mb-2">Pemeriksaan</h4>
                    <p class="text-sm text-gray-500">Kendaraan diperiksa dan estimasi biaya dihitung.</p>
                </div>
                <div data-aos="fade-right" data-aos-delay="300" class="relative z-10 flex flex-col items-center text-center w-full md:w-1/5 mb-8 md:mb-0">
                    <div class="w-16 h-16 rounded-full bg-primary border-4 border-gray-50 shadow-md shadow-primary/30 flex items-center justify-center font-bold text-xl text-white mb-4 transition-transform hover:scale-110">3</div>
                    <h4 class="font-bold text-secondary mb-2">Pengerjaan</h4>
                    <p class="text-sm text-gray-500">Mekanik profesional mulai melakukan servis.</p>
                </div>
                <div data-aos="fade-right" data-aos-delay="400" class="relative z-10 flex flex-col items-center text-center w-full md:w-1/5 mb-8 md:mb-0">
                    <div class="w-16 h-16 rounded-full bg-white border-4 border-gray-50 shadow-md flex items-center justify-center font-bold text-xl text-primary mb-4 transition-transform hover:scale-110">4</div>
                    <h4 class="font-bold text-secondary mb-2">Pembayaran</h4>
                    <p class="text-sm text-gray-500">Kasir memproses pembayaran dengan nota resmi.</p>
                </div>
                <div data-aos="fade-right" data-aos-delay="500" class="relative z-10 flex flex-col items-center text-center w-full md:w-1/5">
                    <div class="w-16 h-16 rounded-full bg-white border-4 border-gray-50 shadow-md flex items-center justify-center font-bold text-xl text-primary mb-4 transition-transform hover:scale-110">5</div>
                    <h4 class="font-bold text-secondary mb-2">Selesai</h4>
                    <p class="text-sm text-gray-500">Kendaraan diserahkan dalam kondisi prima.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Section -->
    <section id="booking" class="py-24 bg-white">
        <div class="max-w-4xl mx-auto px-6">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="font-heading text-3xl md:text-4xl font-bold text-secondary mb-4">Booking Servis</h2>
                <p class="text-gray-500 max-w-2xl mx-auto text-lg">Isi form di bawah ini untuk melakukan reservasi servis kendaraan Anda tanpa perlu antre panjang.</p>
            </div>
            
            @if (session('success'))
                <div class="mb-8 bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl relative flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <form data-aos="fade-up" data-aos-delay="200" action="{{ route('booking.store') }}" method="POST" class="bg-gray-50 p-8 md:p-10 rounded-3xl border border-gray-100 shadow-sm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Data Pelanggan -->
                    <div class="md:col-span-2 border-b border-gray-200 pb-4 mb-2">
                        <h3 class="font-heading font-bold text-secondary text-xl">1. Data Pelanggan</h3>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3" placeholder="Masukkan nama Anda">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon / WA</label>
                        <input type="text" name="phone" required class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3" placeholder="Contoh: 081234567890">
                        @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Data Kendaraan -->
                    <div class="md:col-span-2 border-b border-gray-200 pb-4 mt-4 mb-2">
                        <h3 class="font-heading font-bold text-secondary text-xl">2. Data Kendaraan</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Polisi (Plat)</label>
                        <input type="text" name="license_plate" required class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 uppercase" placeholder="B 1234 ABC">
                        @error('license_plate') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Merk Kendaraan</label>
                        <input type="text" name="brand" required class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3" placeholder="Contoh: Toyota, Honda">
                        @error('brand') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe / Model Kendaraan</label>
                        <input type="text" name="model" required class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3" placeholder="Contoh: Avanza 1.5 G MT">
                        @error('model') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jadwal & Keluhan -->
                    <div class="md:col-span-2 border-b border-gray-200 pb-4 mt-4 mb-2">
                        <h3 class="font-heading font-bold text-secondary text-xl">3. Jadwal & Keluhan</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Booking</label>
                        <input type="date" name="booking_date" required min="{{ date('Y-m-d') }}" class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3">
                        @error('booking_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jam Booking</label>
                        <input type="time" name="booking_time" required class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3">
                        @error('booking_time') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keluhan Kendaraan</label>
                        <textarea name="complaints" required rows="4" class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3" placeholder="Ceritakan keluhan atau jenis servis yang diinginkan (Misal: Ganti oli dan rem bunyi)"></textarea>
                        @error('complaints') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full bg-primary hover:bg-[#E66000] text-white font-bold text-lg py-4 rounded-xl transition-colors shadow-lg shadow-primary/30">
                        Kirim Permintaan Booking
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Tentang & Kontak -->
    <section id="tentang" class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-16 items-center">
            <div data-aos="fade-right">
                <h2 class="font-heading text-3xl md:text-4xl font-bold text-secondary mb-6">Tentang BengkelPro</h2>
                <p class="text-gray-500 mb-6 leading-relaxed">
                    Berdiri sejak tahun 2015, BengkelPro telah menjadi mitra terpercaya bagi ribuan pemilik kendaraan. Kami berkomitmen untuk memberikan layanan otomotif terbaik dengan mengedepankan kualitas, kejujuran, dan transparansi.
                </p>
                <p class="text-gray-500 leading-relaxed mb-8">
                    Visi kami adalah menjadi pusat perawatan kendaraan nomor satu yang memberikan rasa aman dan nyaman bagi setiap pelanggan setiap kali mereka berkendara.
                </p>
                <a href="#kontak" class="text-primary font-semibold hover:underline">Hubungi kami sekarang &rarr;</a>
            </div>
            <div id="kontak" data-aos="fade-left" class="bg-gray-50 p-8 rounded-3xl border border-gray-100 shadow-sm transition-transform hover:-translate-y-2 duration-300">
                <h3 class="font-heading text-2xl font-bold text-secondary mb-6">Informasi Kontak</h3>
                <ul class="space-y-6">
                    <li class="flex items-start gap-4">
                        <div class="p-2 bg-white rounded-lg shadow-sm text-primary">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-secondary">Alamat</h4>
                            <p class="text-gray-500 text-sm mt-1">Jl. Otomotif Raya No. 123<br/>Jakarta Selatan, 12345</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-4">
                        <div class="p-2 bg-white rounded-lg shadow-sm text-primary">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-secondary">Telepon / WhatsApp</h4>
                            <p class="text-gray-500 text-sm mt-1">0812-3456-7890</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-4">
                        <div class="p-2 bg-white rounded-lg shadow-sm text-primary">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-secondary">Jam Operasional</h4>
                            <p class="text-gray-500 text-sm mt-1">Senin - Sabtu: 08:00 - 17:00<br/>Minggu: Tutup</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-secondary text-gray-400 py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-3 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded border border-gray-700 flex items-center justify-center text-white font-bold font-heading text-sm">
                        B
                    </div>
                    <span class="text-white font-heading font-semibold text-lg">BengkelPro</span>
                </div>
                <p class="text-sm leading-relaxed max-w-sm">Solusi terbaik untuk perawatan dan perbaikan kendaraan Anda.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Navigasi</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors">Beranda</a></li>
                    <li><a href="#layanan" class="hover:text-white transition-colors">Layanan</a></li>
                    <li><a href="#booking" class="hover:text-white transition-colors">Booking Servis</a></li>
                    <li><a href="#tentang" class="hover:text-white transition-colors">Tentang Kami</a></li>
                    <li><a href="#kontak" class="hover:text-white transition-colors">Kontak</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Lainnya</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Login Karyawan</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 border-t border-gray-800 pt-8 text-sm text-center md:text-left flex flex-col md:flex-row justify-between items-center">
            <p>&copy; {{ date('Y') }} BengkelPro. All rights reserved.</p>
        </div>
    </footer>

    <!-- AOS JS for animations -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
            offset: 50,
            duration: 800,
            easing: 'ease-out-cubic',
        });
    </script>
</body>
</html>
