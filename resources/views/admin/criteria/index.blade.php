@extends('layouts.app')

@section('title', 'Kriteria & Bobot')

@section('content')
<form method="POST" action="{{ route('admin.criteria.update') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    @csrf
    @method('PUT')

    <div class="p-5 border-b border-slate-200">
        <h2 class="font-bold text-lg">Kriteria & Bobot</h2>
        <p class="text-sm text-slate-500">Konfigurasi default: 25% Market Cap, 20% Volume, 5% perubahan 24 jam, 10% perubahan 7 hari, 15% perubahan 30 hari, dan 25% volatilitas 30 hari.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="p-4 text-left">Kriteria</th>
                <th class="p-4 text-left">Field Data</th>
                <th class="p-4 text-left">Tipe</th>
                <th class="p-4 text-left">Bobot</th>
                <th class="p-4 text-left">Aktif</th>
            </tr>
            </thead>
            <tbody>
            @foreach($criteria as $criterion)
                <tr class="border-t border-slate-100">
                    <td class="p-4 font-semibold">{{ $criterion->name }}</td>
                    <td class="p-4 text-slate-500">{{ $criterion->source_field }}</td>
                    <td class="p-4">
                        <select name="criteria[{{ $criterion->id }}][type]" class="px-3 py-2 rounded-xl border border-slate-300">
                            <option value="benefit" @selected($criterion->type === 'benefit')>Benefit</option>
                            <option value="cost" @selected($criterion->type === 'cost')>Cost</option>
                        </select>
                    </td>
                    <td class="p-4">
                        <input type="number" step="0.0001" min="0" max="1" name="criteria[{{ $criterion->id }}][weight]" value="{{ $criterion->weight }}" class="w-32 px-3 py-2 rounded-xl border border-slate-300">
                    </td>
                    <td class="p-4">
                        <input type="checkbox" name="criteria[{{ $criterion->id }}][is_active]" value="1" @checked($criterion->is_active)>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="p-5 border-t border-slate-200 flex items-center justify-between">
        <div class="text-sm text-slate-500">Total bobot aktif harus 1.0000 (100%). Market Cap Rank dinonaktifkan untuk mencegah pengaruh ganda dengan Market Cap.</div>
        <button class="px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold">Simpan</button>
    </div>
</form>
@endsection
