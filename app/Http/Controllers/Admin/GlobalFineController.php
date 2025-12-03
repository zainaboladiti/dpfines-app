<?php

namespace App\Http\Controllers\Admin;

use App\Models\GlobalFine;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGlobalFineRequest;
use Illuminate\Http\Request;

class GlobalFineController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of global fines
     */
    public function index(Request $request)
    {
        $query = GlobalFine::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('organisation', 'like', "%{$search}%")
                  ->orWhere('sector', 'like', "%{$search}%")
                  ->orWhere('regulator', 'like', "%{$search}%");
            });
        }

        // Filter by regulator
        if ($request->filled('regulator')) {
            $query->where('regulator', $request->input('regulator'));
        }

        // Filter by sector
        if ($request->filled('sector')) {
            $query->where('sector', $request->input('sector'));
        }

        $fines = $query->paginate(20)->appends($request->query());

        return view('admin.fines.index', compact('fines'));
    }

    /**
     * Show the form for creating a new fine
     */
    public function create()
    {
        return view('admin.fines.create');
    }

    /**
     * Store a newly created fine
     */
    public function store(StoreGlobalFineRequest $request)
    {
        $validated = $request->validated();

        try {
            GlobalFine::create($validated);
            return redirect('/admin/fines')->with('success', 'Fine added successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create fine: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the specified fine
     */
    public function show(GlobalFine $fine)
    {
        return view('admin.fines.show', compact('fine'));
    }

    /**
     * Show the form for editing the fine
     */
    public function edit(GlobalFine $fine)
    {
        return view('admin.fines.edit', compact('fine'));
    }

    /**
     * Update the specified fine
     */
    public function update(StoreGlobalFineRequest $request, GlobalFine $fine)
    {
        $validated = $request->validated();

        try {
            $fine->update($validated);
            return redirect("/admin/fines/{$fine->id}")->with('success', 'Fine updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update fine: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete the specified fine
     */
    public function destroy(GlobalFine $fine)
    {
        try {
            $fine->delete();
            return redirect('/admin/fines')->with('success', 'Fine deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete fine: ' . $e->getMessage()]);
        }
    }
}
