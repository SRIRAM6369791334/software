<?php

namespace App\Http\Controllers;

use App\Models\StockSummary;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $summaries = StockSummary::orderBy('item_name')->get();
        
        $from = $request->input('from', now()->subDays(30)->toDateString());
        $to = $request->input('to', now()->toDateString());
        
        $movements = StockMovement::whereBetween('date', [$from, $to])
                        ->orderBy('date', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate(20);

        return view('stock.index', compact('summaries', 'movements', 'from', 'to'));
    }
}
