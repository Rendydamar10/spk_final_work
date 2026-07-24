<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CryptoCoin;
use Illuminate\Http\Request;

class CryptoController extends Controller
{
    public function index(Request $request)
    {
        $coins = CryptoCoin::with('globalSawResult')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('symbol', 'like', "%{$search}%")
                        ->orWhere('coingecko_id', 'like', "%{$search}%");
                });
            })
            ->where('is_stablecoin', false)
            ->orderByRaw('market_cap_rank IS NULL, market_cap_rank ASC')
            ->paginate(20)
            ->withQueryString();

        return view('admin.crypto.index', compact('coins'));
    }
}
