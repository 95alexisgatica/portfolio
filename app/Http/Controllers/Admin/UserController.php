<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('expenses.admin-users', [
            'users' => User::query()->latest()->get(),
        ]);
    }
}
