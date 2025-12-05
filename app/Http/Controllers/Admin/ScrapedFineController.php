<?php

namespace App\Http\Controllers\Admin;

use App\Models\ScrapedFine;
use App\Models\GlobalFine;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreScrapedFineRequest;
use App\Http\Requests\Admin\ReviewScrapedFineRequest;
use Illuminate\Http\Request;

class ScrapedFineController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of scraped fines with filters
     */
    public function index(Request $request)
    {
        $query = ScrapedFine::with(['submittedBy', 'reviewedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            // Default to pending
            $query->pending();
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('organisation', 'like', "%{$search}%")
                  ->orWhere('sector', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        $scraped_fines = $query->latest()->paginate(20)->appends($request->query());
        $statuses = ['pending', 'approved', 'rejected'];

        return view('admin.scraped_fines.index', compact('scraped_fines', 'statuses'));
    }

    /**
     * Show the form for submitting a new scraped fine
     */
    public function create()
    {
        return view('admin.scraped_fines.create');
    }

    /**
     * Store a newly submitted scraped fine
     */
    public function store(StoreScrapedFineRequest $request)
    {
        $validated = $request->validated();
        $validated['submitted_by'] = auth()->id();
        $validated['status'] = 'pending';

        try {
            ScrapedFine::create($validated);
            return redirect('/admin/scraped-fines')->with('success', 'Scraped fine submitted for review.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to submit fine: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the specified scraped fine
     */
    public function show(ScrapedFine $scraped_fine)
    {
        $scraped_fine->load(['submittedBy', 'reviewedBy']);
        return view('admin.scraped_fines.show', compact('scraped_fine'));
    }

    /**
     * Show the review form for a scraped fine
     */
    public function review(ScrapedFine $scraped_fine)
    {
        // Can only review pending fines
        if ($scraped_fine->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending fines can be reviewed.']);
        }

        $scraped_fine->load(['submittedBy']);
        return view('admin.scraped_fines.review', compact('scraped_fine'));
    }

    /**
     * Store the review and approve/reject a scraped fine
     */
    public function storeReview(ReviewScrapedFineRequest $request, ScrapedFine $scraped_fine)
    {
        // Prevent reviewing already reviewed fines
        if ($scraped_fine->status !== 'pending') {
            return back()->withErrors(['error' => 'This fine has already been reviewed.']);
        }

        $validated = $request->validated();

        try {
            // Update the scraped fine with review data
            $scraped_fine->update([
                'status' => $validated['status'],
                'review_notes' => $validated['review_notes'],
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            // If approved, copy to global_fines table
            if ($validated['status'] === 'approved') {
                GlobalFine::create([
                    'organisation' => $scraped_fine->organisation,
                    'regulator' => $scraped_fine->regulator,
                    'sector' => $scraped_fine->sector,
                    'region' => $scraped_fine->region,
                    'fine_amount' => $scraped_fine->fine_amount,
                    'currency' => $scraped_fine->currency,
                    'fine_date' => $scraped_fine->fine_date,
                    'law' => $scraped_fine->law,
                    'articles_breached' => $scraped_fine->articles_breached,
                    'violation_type' => $scraped_fine->violation_type,
                    'summary' => $scraped_fine->summary,
                    'badges' => $scraped_fine->badges,
                    'link_to_case' => $scraped_fine->link_to_case,
                ]);

                return redirect("/admin/scraped-fines/{$scraped_fine->id}")
                    ->with('success', 'Fine approved and added to database.');
            }

            return redirect("/admin/scraped-fines/{$scraped_fine->id}")
                ->with('success', 'Fine rejected.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to review fine: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a scraped fine (soft delete)
     */
    public function destroy(ScrapedFine $scraped_fine)
    {
        try {
            $scraped_fine->delete();
            return redirect('/admin/scraped-fines')->with('success', 'Scraped fine deleted.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete fine: ' . $e->getMessage()]);
        }
    }
}
