<?php

namespace App\Http\Controllers\Admin;

use App\Models\GlobalFine;
use App\Models\ScrapedFine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $stats = [
            'total_fines' => GlobalFine::count(),
            'total_scraped' => ScrapedFine::count(),
            'pending_reviews' => ScrapedFine::pending()->count(),
            'approved_fines' => ScrapedFine::approved()->count(),
            'rejected_fines' => ScrapedFine::rejected()->count(),
        ];

        $recent_scraped = ScrapedFine::with(['submittedBy', 'reviewedBy'])
            ->latest()
            ->limit(5)
            ->get();

        $pending_reviews = ScrapedFine::pending()
            ->with('submittedBy')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_scraped', 'pending_reviews'));
    }
}
