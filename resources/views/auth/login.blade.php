<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-medium text-sm text-slate-300">Email Address</label>
            <input id="email" class="block mt-2 w-full bg-slate-800/50 border border-slate-700 text-white focus:border-[#FF6B00] focus:ring-[#FF6B00] rounded-xl shadow-sm px-4 py-3 transition-colors" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Masukkan email Anda" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-6">
            <label for="password" class="block font-medium text-sm text-slate-300">Password</label>
            <input id="password" class="block mt-2 w-full bg-slate-800/50 border border-slate-700 text-white focus:border-[#FF6B00] focus:ring-[#FF6B00] rounded-xl shadow-sm px-4 py-3 transition-colors"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="Masukkan password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-6">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded bg-slate-800 border-slate-700 text-[#FF6B00] shadow-sm focus:ring-[#FF6B00] focus:ring-offset-slate-900 w-5 h-5" name="remember">
                <span class="ms-3 text-sm text-slate-400">Ingat Saya</span>
            </label>
            
            @if (Route::has('password.request'))
                <a class="text-sm text-[#FF6B00] hover:text-[#FFB800] transition-colors" href="{{ route('password.request') }}">
                    Lupa Password?
                </a>
            @endif
        </div>

        <div class="mt-8">
            <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-3.5 bg-gradient-to-r from-[#FF6B00] to-[#FFB800] border border-transparent rounded-xl font-bold text-white uppercase tracking-widest hover:scale-[1.02] active:scale-[0.98] transition-all shadow-lg shadow-[#FF6B00]/25">
                Masuk ke Sistem
            </button>
        </div>
    </form>
</x-guest-layout>
