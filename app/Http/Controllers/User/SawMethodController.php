<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Criterion;

class SawMethodController extends Controller
{
    public function __invoke()
    {
        $criteria = Criterion::where('is_active', true)->ordered()->get();

        return view('user.saw-method', compact('criteria'));
    }
}
