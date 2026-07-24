<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiLog;

class ApiLogController extends Controller
{
    public function index()
    {
        $logs = ApiLog::latest()->paginate(30);

        return view('admin.logs.index', compact('logs'));
    }
}
