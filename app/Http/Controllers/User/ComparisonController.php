<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class ComparisonController extends Controller
{
    public function index()
    {
        $watchlists = auth()->user()
            ->watchlists()
            ->with('cryptoCoin')
            ->latest()
            ->get();

        return view('user.comparison.index', compact('watchlists'));
    }
}