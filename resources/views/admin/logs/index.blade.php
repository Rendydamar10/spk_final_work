@extends('layouts.app')

@section('title', 'Log API')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-200">
        <h2 class="font-bold text-lg">Log API</h2>
        <p class="text-sm text-slate-500">Riwayat refresh data, pencarian coin, error limit, dan status integrasi.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="p-4 text-left">Waktu</th>
                <th class="p-4 text-left">Provider</th>
                <th class="p-4 text-left">Action</th>
                <th class="p-4 text-left">Status</th>
                <th class="p-4 text-left">Message</th>
            </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr class="border-t border-slate-100">
                    <td class="p-4">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                    <td class="p-4">{{ $log->provider }}</td>
                    <td class="p-4">{{ $log->action }}</td>
                    <td class="p-4">
                        <span class="px-2 py-1 rounded-lg text-xs {{ $log->status === 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ $log->status }}</span>
                    </td>
                    <td class="p-4 max-w-xl truncate">{{ $log->message }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-6 text-center text-slate-500">Belum ada log.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4">{{ $logs->links() }}</div>
</div>
@endsection
