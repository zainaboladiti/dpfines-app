<?php

namespace App\Http\Controllers;

use App\Models\GlobalFine;
use Illuminate\Http\Request;

class FineController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filters = $request->only(['regulator', 'sector', 'year', 'violation_type']);

        $fines = GlobalFine::search($search)
            ->filter($filters)
            ->paginate(20);

        return view('fines.index', compact('fines', 'search', 'filters'));
    }

    public function show($id)
    {
        $fine = GlobalFine::findOrFail($id);
        return view('fines.show', compact('fine'));
    }
}
