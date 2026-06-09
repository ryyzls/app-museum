<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransactionLog;

class AdminTransactionLogController extends Controller
{
    public function index()
    {
        $logs = TransactionLog::with('transaction')
            ->latest('changed_at')
            ->paginate(15);

        return view('admin.transaction-logs.index', compact('logs'));
    }
}