<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard untuk role Admin.
     */
    public function admin(): View
    {
        return view('dashboard.admin', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Dashboard untuk role Kasir (Front).
     */
    public function kasir(): View
    {
        return view('dashboard.kasir', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Dashboard untuk role Kitchen (Dapur).
     */
    public function kitchen(): View
    {
        return view('dashboard.kitchen', [
            'user' => Auth::user(),
        ]);
    }
}
