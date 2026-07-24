@extends('layouts.app')

@section('title', 'Refresh API')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 max-w-2xl">
    <h2 class="font-bold text-xl">Refresh Ranking Global CoinGecko</h2>
    <p class="text-sm text-slate-500 mt-2">
        Tombol ini mengambil maksimal 10 cryptocurrency dari CoinGecko, menyimpan ke katalog, menyinkronkan anggota ranking global, lalu menghitung ulang SAW global.
    </p>

    <form method="POST" action="{{ route('admin.refresh.run') }}" class="mt-6" onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerText = 'Memproses...';">
        @csrf
        <button onclick="return confirm('Refresh ranking global sekarang?')" class="px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold disabled:opacity-60">
            Refresh 10 Coin
        </button>
    </form>
</div>
@endsection
