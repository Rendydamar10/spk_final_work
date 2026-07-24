<?php

namespace App\Http\Controllers;

use App\Models\ApiLog;
use App\Models\RankingSet;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $globalRankingSet = RankingSet::globalSet();

        $topResults = $globalRankingSet->results()
            ->with('coin')
            ->orderBy('rank')
            ->take(10)
            ->get();

        $data = [
            'totalCoins' => $globalRankingSet->coins()->count(),
            'bestCoin' => $topResults->first(),
            'lastLog' => ApiLog::where('action', 'refresh_global_ranking')->latest()->first(),
            'topResults' => $topResults,
            'globalRankingSet' => $globalRankingSet,
        ];

        return view('dashboard', $data);
    }
}
