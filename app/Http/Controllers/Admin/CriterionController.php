<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Criterion;
use App\Models\RankingSet;
use App\Services\Crypto\SawService;
use Illuminate\Http\Request;

class CriterionController extends Controller
{
    public function index()
    {
        $criteria = Criterion::ordered()->get();

        return view('admin.criteria.index', compact('criteria'));
    }

    public function update(Request $request, SawService $sawService)
    {
        $validated = $request->validate([
            'criteria' => ['required', 'array'],
            'criteria.*.weight' => ['required', 'numeric', 'min:0', 'max:1'],
            'criteria.*.type' => ['required', 'in:benefit,cost'],
            'criteria.*.is_active' => ['nullable', 'boolean'],
        ]);

        $totalWeight = collect($validated['criteria'])
            ->filter(fn ($item) => isset($item['is_active']))
            ->sum(fn ($item) => (float) $item['weight']);

        if (round($totalWeight, 4) !== 1.0000) {
            return back()->with('error', 'Total bobot harus 1.0000 atau 100%. Total saat ini: '.number_format($totalWeight, 4))->withInput();
        }

        foreach ($validated['criteria'] as $id => $item) {
            Criterion::whereKey($id)->update([
                'weight' => $item['weight'],
                'type' => $item['type'],
                'is_active' => isset($item['is_active']),
            ]);
        }

        RankingSet::query()->each(fn (RankingSet $rankingSet) => $sawService->calculateForRankingSet($rankingSet));

        return back()->with('success', 'Kriteria dan bobot berhasil diperbarui. Ranking SAW dihitung ulang per ranking set.');
    }
}
