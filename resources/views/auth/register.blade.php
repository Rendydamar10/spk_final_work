<x-guest-layout>
    <div class="mb-7">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-600">Buat akun baru</p>
        <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950">Mulai analisis crypto</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Daftar untuk membuat ranking pribadi, watchlist, dan perbandingan coin.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" value="Nama lengkap" />
            <x-text-input id="name" class="mt-2 block w-full rounded-xl px-4 py-3" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama Anda" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-2 block w-full rounded-xl px-4 py-3" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="mt-2 block w-full rounded-xl px-4 py-3" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi password" />
            <x-text-input id="password_confirmation" class="mt-2 block w-full rounded-xl px-4 py-3" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-cyan-500/15 transition hover:from-cyan-400 hover:to-blue-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
            Buat Akun
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m7.5 5 5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-slate-500">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="font-bold text-cyan-700 hover:text-cyan-800">Masuk</a>
    </p>
</x-guest-layout>
