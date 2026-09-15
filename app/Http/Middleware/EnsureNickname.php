<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNickname
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && blank($user->nickname)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Nickname required.'], 403);
            }

            return redirect()->route('expenses.index');
        }

        return $next($request);
    }
}
