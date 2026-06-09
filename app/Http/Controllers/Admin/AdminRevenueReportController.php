<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminRevenueReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date
            ?? now()->subDays(30)->format('Y-m-d');

        $endDate = $request->end_date
            ?? now()->format('Y-m-d');

        $reports = collect(
            DB::select(
                'CALL sp_generate_revenue_report(?, ?)',
                [$startDate, $endDate]
            )
        );

        $totalRevenue = $reports->sum('daily_revenue');

        $totalTransactions = $reports->sum('total_transactions');

        $totalTickets = $reports->sum('tickets_sold');

        return view('admin.revenue-report.index', compact(
            'reports',
            'startDate',
            'endDate',
            'totalRevenue',
            'totalTransactions',
            'totalTickets'
        ));
    }
}