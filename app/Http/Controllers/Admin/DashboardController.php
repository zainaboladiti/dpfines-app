<?php

namespace App\Http\Controllers\Admin;

use App\Models\GlobalFine;
use App\Models\ScrapedFine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        // Monthly approvals (last 6 months)
        $months = [];
        $monthlyApproved = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $label = $date->format('M');
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $count = ScrapedFine::where('status', 'approved')
                ->whereBetween('reviewed_at', [$start, $end])
                ->count();

            $months[] = $label;
            $monthlyApproved[] = $count;
        }

        // Top regulators (from published global fines)
        $top_regulators = GlobalFine::select('regulator', DB::raw('count(*) as total'))
            ->groupBy('regulator')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recent_scraped',
            'pending_reviews',
            'months',
            'monthlyApproved',
            'top_regulators'
        ));
    }
}
