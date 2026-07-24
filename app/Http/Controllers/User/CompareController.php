<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Criterion;
use App\Models\Watchlist;
use App\Services\Crypto\SawService;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index(Request $request, SawService $sawService)
    {
        $watchlists = Watchlist::with('coin')
            ->where('user_id', $request->user()->id)
            ->get();

        $selectedIds = collect($request->input('coins', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->take(5)
            ->values()
            ->all();

        $selected = $watchlists
            ->filter(fn ($watchlist) => in_array($watchlist->crypto_coin_id, $selectedIds, true))
            ->values();

        $criteria = Criterion::where('is_active', true)->ordered()->get();

        $comparisonResultsByCoinId = $selected->count() >= 2
            ? $sawService->calculateForCoins($selected->pluck('coin')->filter())
                ->keyBy(fn (array $row) => $row['coin']->id)
            : collect();

        $excludedSelectedCount = max(0, $selected->count() - $comparisonResultsByCoinId->count());

        return view('user.compare.index', compact(
            'watchlists',
            'selected',
            'selectedIds',
            'criteria',
            'comparisonResultsByCoinId',
            'excludedSelectedCount'
        ));
    }
}
