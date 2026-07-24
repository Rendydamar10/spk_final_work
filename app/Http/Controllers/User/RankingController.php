<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Criterion;
use App\Models\CryptoCoin;
use App\Models\RankingSet;
use App\Services\Crypto\CoinGeckoService;
use App\Services\Crypto\CryptoRefreshService;
use App\Services\Crypto\SawService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class RankingController extends Controller
{
    public function index(Request $request, CoinGeckoService $coinGeckoService)
    {
        $rankingSet = RankingSet::userSet($request->user()->id);

        $results = $rankingSet->results()
            ->with('coin')
            ->orderBy('rank')
            ->paginate(20);

        $criteria = Criterion::where('is_active', true)->ordered()->get();
        $rankingCoinCount = $rankingSet->coins()->count();
        $excludedCount = max(0, $rankingCoinCount - $results->total());
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

        return view('user.ranking.index', compact('results', 'searchResults', 'rankingSet', 'criteria', 'rankingCoinCount', 'excludedCount'));
    }

    public function store(Request $request, CryptoRefreshService $refreshService)
    {
        $validated = $request->validate([
            'coingecko_id' => ['required', 'string', 'max:120'],
        ]);

        try {
            $coin = $refreshService->addCoinToUserRanking($validated['coingecko_id'], $request->user());

            if (!$coin) {
                return back()->with('error', 'Coin tidak ditemukan dari CoinGecko.');
            }

            return back()->with('success', $coin->name.' berhasil ditambahkan ke Ranking Saya.');
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal menambahkan cryptocurrency ke ranking. Silakan coba kembali.');
        }
    }

    public function destroy(Request $request, CryptoCoin $cryptoCoin, SawService $sawService)
    {
        $rankingSet = RankingSet::userSet($request->user()->id);

        abort_unless($rankingSet->coins()->whereKey($cryptoCoin->id)->exists(), 404);

        DB::transaction(function () use ($rankingSet, $cryptoCoin, $sawService) {
            $rankingSet->coins()->detach($cryptoCoin->id);
            $sawService->calculateForRankingSet($rankingSet);
        });

        return back()->with('success', $cryptoCoin->name.' berhasil dihapus dari Ranking Saya.');
    }

    public function recalculate(Request $request, CryptoRefreshService $refreshService)
    {
        try {
            $refreshService->refreshUserRanking($request->user());

            return back()->with('success', 'Data pasar dan Ranking Saya berhasil diperbarui.');
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal memperbarui data ranking. Silakan coba kembali.');
        }
    }
}
