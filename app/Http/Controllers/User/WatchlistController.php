<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\RankingSet;
use App\Models\Watchlist;
use App\Services\Crypto\CoinGeckoService;
use App\Services\Crypto\CryptoRefreshService;
use Illuminate\Http\Request;
use Throwable;

class WatchlistController extends Controller
{
    public function index(Request $request, CoinGeckoService $coinGeckoService)
    {
        $rankingSet = RankingSet::userSet($request->user()->id);

        $watchlists = Watchlist::with('coin')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $userResultsByCoinId = $rankingSet->results()
            ->get()
            ->keyBy('crypto_coin_id');

        $searchResults = [];

        if ($request->filled('q')) {
            try {
                $searchResults = collect($coinGeckoService->search($request->q))
                    ->take(10)
                    ->values()
                    ->all();
            } catch (Throwable $e) {
                report($e);

                return back()->with('error', 'Pencarian cryptocurrency gagal. Silakan coba kembali.');
            }
        }

        return view('user.watchlist.index', compact('watchlists', 'searchResults', 'userResultsByCoinId'));
    }

    public function store(Request $request, CryptoRefreshService $refreshService)
    {
        $validated = $request->validate([
            'coingecko_id' => ['required', 'string', 'max:120'],
        ]);

        try {
            $coin = $refreshService->addCoinToWatchlist($validated['coingecko_id'], $request->user());

            if (!$coin) {
                return back()->with('error', 'Coin tidak ditemukan dari CoinGecko.');
            }

            return back()->with('success', $coin->name.' berhasil ditambahkan ke Watchlist.');
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal menambahkan cryptocurrency ke watchlist. Silakan coba kembali.');
        }
    }

    public function destroy(Request $request, Watchlist $watchlist)
    {
        abort_unless($watchlist->user_id === $request->user()->id, 403);

        $watchlist->delete();

        return back()->with('success', 'Coin berhasil dihapus dari Watchlist. Ranking Saya tidak berubah.');
    }
}
