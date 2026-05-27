<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class EnsureApiActive
{
    // Handle an incoming request.
    public function handle(Request $request, Closure $next)
    {
        // If the API routes are not loaded in the router (e.g. commented out in bootstrap/app.php),
        // we prevent the request from proceeding, ensuring the system strictly relies on the API.
        if (! Route::has('api.products.index')) {
            abort(503, 'The API layer is disabled. The system cannot function without the API routes.');
        }

        return $next($request);
    }
}
