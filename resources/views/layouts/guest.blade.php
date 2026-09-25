<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BengkelPro') }} - Login</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .glass-panel {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
        </style>
    </head>
    <body class="font-sans text-slate-300 antialiased bg-[#0B0F19] min-h-screen flex items-center justify-center relative overflow-hidden" style="font-family: 'Inter', sans-serif;">
        <!-- Background Elements -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-[#FF6B00]/20 rounded-full blur-3xl -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-blue-900/20 rounded-full blur-3xl translate-y-1/3"></div>

        <div class="w-full sm:max-w-md px-6 z-10">
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center gap-2">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#FF6B00] to-[#FFB800] flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-[#FF6B00]/30" style="font-family: 'Outfit', sans-serif;">
                        B
                    </div>
                    <span class="text-white font-bold text-3xl tracking-tight" style="font-family: 'Outfit', sans-serif;">Bengkel<span class="text-[#FF6B00]">Pro</span></span>
                </a>
                <p class="mt-3 text-slate-400">Silakan masuk ke akun Anda</p>
            </div>

            <div class="glass-panel p-8 shadow-2xl rounded-2xl border border-white/10 relative overflow-hidden">
                <!-- Inner glow -->
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-[#FF6B00]/10 rounded-full blur-2xl"></div>
                
                <div class="relative z-10">
                    {{ $slot }}
                </div>
            </div>
            
            <div class="text-center mt-6 text-sm text-slate-500">
                &copy; {{ date('Y') }} BengkelPro. All rights reserved.
            </div>
        </div>
    </body>
</html>
