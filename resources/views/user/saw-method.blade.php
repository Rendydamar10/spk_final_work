@extends('layouts.app')

@section('title', 'Metode SAW')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
    <div>
        <h2 class="font-bold text-xl">Metode Simple Additive Weighting</h2>
        <p class="text-sm text-slate-600 mt-2">
            SAW digunakan untuk menentukan ranking cryptocurrency berdasarkan beberapa kriteria. Setiap coin menjadi alternatif, setiap data pasar menjadi kriteria, lalu sistem menghitung skor akhir berdasarkan bobot.
        </p>
    </div>

    <div>
        <h3 class="font-bold mb-3">Kriteria Aktif</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-slate-200 rounded-xl overflow-hidden">
                <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="p-4 text-left">Kriteria</th>
                    <th class="p-4 text-left">Tipe</th>
                    <th class="p-4 text-right">Bobot</th>
                </tr>
                </thead>
                <tbody>
                @foreach($criteria as $criterion)
                    <tr class="border-t border-slate-100">
                        <td class="p-4 font-semibold">{{ $criterion->name }}</td>
                        <td class="p-4">{{ ucfirst($criterion->type) }}</td>
                        <td class="p-4 text-right">{{ number_format((float) $criterion->weight * 100, 2) }}%</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
            <h3 class="font-bold">Benefit</h3>
            <p class="text-sm text-slate-600 mt-2">Semakin besar nilai kriteria, semakin baik. Contoh: market cap, volume, perubahan harga positif.</p>
        </div>
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
            <h3 class="font-bold">Cost</h3>
            <p class="text-sm text-slate-600 mt-2">Semakin kecil nilai kriteria, semakin baik. Pada model ini kriteria cost adalah volatilitas historis 30 hari.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
            <h3 class="font-bold">Volatilitas 30 Hari</h3>
            <p class="text-sm text-slate-600 mt-2">Dihitung dari sample standard deviation return harian selama 30 hari. Nilai yang lebih rendah memperoleh preferensi lebih baik karena bertipe cost.</p>
        </div>
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
            <h3 class="font-bold">Kualitas Data</h3>
            <p class="text-sm text-slate-600 mt-2">Coin dengan salah satu nilai kriteria aktif yang kosong tidak dimasukkan ke hasil SAW dan tidak lagi dianggap bernilai nol.</p>
        </div>
    </div>

    <div class="p-5 rounded-2xl bg-indigo-50 border border-indigo-200">
        <h3 class="font-bold text-indigo-900">Catatan</h3>
        <p class="text-sm text-indigo-800 mt-2">
            Sistem ini bukan prediksi harga atau sinyal beli/jual. Sistem hanya membantu membandingkan cryptocurrency berdasarkan data pasar dan kriteria yang sudah ditentukan.
        </p>
    </div>
</div>
@endsection
