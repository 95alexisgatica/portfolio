<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! $this->googleIsConfigured()) {
            return redirect()
                ->route('expenses.index')
                ->with('auth_error', __('portfolio.expenses_auth_missing_google'));
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        if (! $this->googleIsConfigured()) {
            return redirect()
                ->route('expenses.index')
                ->with('auth_error', __('portfolio.expenses_auth_missing_google'));
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            return redirect()
                ->route('expenses.index')
                ->with('auth_error', __('portfolio.expenses_auth_google_failed'));
        }

        $email = strtolower((string) $googleUser->getEmail());

        if ($email === '') {
            return redirect()
                ->route('expenses.index')
                ->with('auth_error', __('portfolio.expenses_auth_google_failed'));
        }

        $adminEmail = strtolower((string) config('services.admin.email'));
        $isAdmin = $email === $adminEmail;

        $user = User::query()
            ->where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if (! $user) {
            $user = User::query()->create([
                'name' => $googleUser->getName() ?: $email,
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'password' => null,
                'role' => $isAdmin ? 'super_admin' : 'user',
                'email_verified_at' => now(),
                'nickname' => $isAdmin ? 'Alexis' : null,
            ]);
            $user->seedDefaultCategories();
        } else {
            $user->forceFill([
                'google_id' => $googleUser->getId(),
                'name' => $user->name ?: ($googleUser->getName() ?: $email),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);

            if ($isAdmin) {
                $user->role = 'super_admin';
                $user->nickname = $user->nickname ?: 'Alexis';
            }

            $user->save();

            if ($user->categories()->doesntExist()) {
                $user->seedDefaultCategories();
            }
        }

        Auth::login($user, true);

        return redirect()->route('expenses.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('expenses.index');
    }

    private function googleIsConfigured(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'));
    }
}
