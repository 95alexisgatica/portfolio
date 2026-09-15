<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ExpensePageController extends Controller
{
    public function show(): View
    {
        if (! auth()->check()) {
            return view('expenses.login', [
                'authError' => session('auth_error'),
                'googleConfigured' => filled(config('services.google.client_id'))
                    && filled(config('services.google.client_secret')),
            ]);
        }

        $user = auth()->user();

        if (blank($user->nickname)) {
            return view('expenses.onboarding', ['user' => $user]);
        }

        return view('expenses.index', [
            'user' => $user,
            'isAdmin' => $user->isSuperAdmin(),
        ]);
    }
}
