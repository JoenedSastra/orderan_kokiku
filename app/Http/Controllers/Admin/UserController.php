<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('role')
            ->where('email', 'not like', '%.test')
            ->orderBy('role_id')
            ->get();
            
        return view('admin.users.index', compact('users'));
    }
}
