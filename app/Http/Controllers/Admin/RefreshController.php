<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Crypto\CryptoRefreshService;
use Throwable;

class RefreshController extends Controller
{
    public function index()
    {
        return view('admin.refresh.index');
    }

    public function refresh(CryptoRefreshService $refreshService)
    {
        try {
            $coins = $refreshService->refreshTopCoins(10, request()->user());

            return back()->with('success', 'Refresh ranking global berhasil. '.$coins->count().' coin diproses dan ranking SAW dihitung ulang.');
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Refresh data pasar gagal. Periksa Log API untuk detail teknis.');
        }
    }
}
