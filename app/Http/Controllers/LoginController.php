<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    // Show the login form.
    // This is a pure Blade view — no data from the server.
    // The form submits via JavaScript fetch() to POST /api/auth/login.
    public function showForm()
    {
        return view('auth.login');
    }

    // Logout: clear the session token and redirect to login.
    // The actual Sanctum token is revoked on the client side via POST /api/auth/logout.
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect($request->getSchemeAndHttpHost() . $request->getBasePath() . '/login');
    }
}
