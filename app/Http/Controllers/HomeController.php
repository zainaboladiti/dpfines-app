<?php

namespace App\Http\Controllers;

use App\Models\GlobalFine;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Latest fines
        $latestFines = GlobalFine::orderBy('fine_date', 'desc')
            ->limit(10)
            ->get();

        // Top regulators
        $regulatorStats = GlobalFine::select('regulator', DB::raw('COUNT(*) as count'))
            ->groupBy('regulator')
            ->orderBy('count', 'desc')
            ->take(6)
            ->get();

        // Top sectors
        $sectorStats = GlobalFine::select('sector', DB::raw('COUNT(*) as count'))
            ->groupBy('sector')
            ->orderBy('count', 'desc')
            ->take(6)
            ->get();

        return view('home', compact('latestFines', 'regulatorStats', 'sectorStats'));
    }
}
