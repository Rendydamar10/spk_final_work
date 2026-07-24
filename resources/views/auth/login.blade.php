<x-guest-layout>
    <div class="mb-7">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-600">Selamat datang kembali</p>
        <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950">Masuk ke akun Anda</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Lanjutkan analisis dan kelola ranking cryptocurrency Anda.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-2 block w-full rounded-xl px-4 py-3" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between gap-4">
                <x-input-label for="password" value="Password" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-cyan-700 hover:text-cyan-800" href="{{ route('password.request') }}">Lupa password?</a>
                @endif
            </div>
            <x-text-input id="password" class="mt-2 block w-full rounded-xl px-4 py-3" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
            <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-cyan-600 shadow-sm focus:ring-cyan-500" name="remember">
            Ingat saya
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-950 px-5 py-3.5 text-sm font-bold text-white shadow-lg transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
            Masuk
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m7.5 5 5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-slate-500">
        Belum memiliki akun?
        <a href="{{ route('register') }}" class="font-bold text-cyan-700 hover:text-cyan-800">Daftar sekarang</a>
    </p>
</x-guest-layout>
