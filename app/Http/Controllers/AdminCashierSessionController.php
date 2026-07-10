<?php

namespace App\Http\Controllers;

use App\Domains\Account\Models\CashierSession;
use Illuminate\View\View;

class AdminCashierSessionController extends Controller
{
    public function index(): View
    {
        $sessions = CashierSession::query()
            ->with('user')
            ->latest('started_at')
            ->limit(50)
            ->get();

        $statistics = [
            'total_sesi' => CashierSession::count(),
            'sesi_aktif' => CashierSession::whereNull('ended_at')->count(),
            'kasir_tercatat' => CashierSession::distinct('user_id')->count('user_id'),
        ];

        return view('admin.cashier-sessions', [
            'sessions' => $sessions,
            'statistics' => $statistics,
        ]);
    }
}
