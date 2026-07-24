<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Criterion;
use App\Models\RankingSet;
use App\Services\Crypto\SawService;

class RankingController extends Controller
{
    public function index()
    {
        $globalRankingSet = RankingSet::globalSet();

        $results = $globalRankingSet->results()
            ->with('coin')
            ->orderBy('rank')
            ->paginate(20);

        $criteria = Criterion::where('is_active', true)->ordered()->get();

        return view('admin.ranking.index', compact('results', 'criteria', 'globalRankingSet'));
    }

    public function recalculate(SawService $sawService)
    {
        $sawService->calculateForRankingSet(RankingSet::globalSet());

        return back()->with('success', 'Ranking global SAW berhasil dihitung ulang.');
    }
}
