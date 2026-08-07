<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HasStockKitchenReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockKitchenReportController extends Controller
{
    use HasStockKitchenReport;

    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', now()->subDays(6)->toDateString());
        $endDate   = $request->input('end_date', now()->toDateString());

        $data = $this->buildStockKitchenReport($startDate, $endDate);

        return view('admin.stock-kitchen.index', $data);
    }
}
