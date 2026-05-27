<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;

class EnsureApiAuthenticated
{
    // Handle an incoming request.
    // Validates the API token by looking it up directly in the database via
    // Sanctum's PersonalAccessToken model — no internal HTTP dispatch that
    // would corrupt the URL generator under XAMPP subdirectories.
    public function handle(Request $request, Closure $next): Response
    {
        $loginUrl = $request->getSchemeAndHttpHost() . $request->getBasePath() . '/login';

        // 1. Try Authorization header first, then cookie fallback
        $tokenStr = $request->bearerToken() ?: $request->cookie('api_token');

        if (!$tokenStr) {
            return redirect($loginUrl);
        }

        // 2. Validate token directly via Sanctum DB lookup — no internal HTTP dispatch
        try {
            $token = PersonalAccessToken::findToken($tokenStr);

            if (!$token || !$token->tokenable) {
                return redirect($loginUrl)->withCookie(cookie()->forget('api_token'));
            }

            $user = $token->tokenable;

            if (!$user->is_active) {
                return redirect($loginUrl)->withCookie(cookie()->forget('api_token'));
            }

            // Set the authenticated user so auth()->user() works in Blade/Livewire
            $user->withAccessToken($token);
            \Illuminate\Support\Facades\Auth::setUser($user);

        } catch (\Exception $e) {
            return redirect($loginUrl)
                ->withCookie(cookie()->forget('api_token'))
                ->withErrors(['error' => 'Authentication failed. Please log in again.']);
        }

        return $next($request);
    }
}
