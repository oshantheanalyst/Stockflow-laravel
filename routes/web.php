<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes — StockFlow
|--------------------------------------------------------------------------
|
| ONLY TWO THINGS ARE ALLOWED HERE:
|   1. The login page (unauthenticated view, no business logic)
|   2. A catch-all that serves the app shell for all authenticated pages
|
| ALL business logic lives exclusively in routes/api.php.
| Removing api.php = the entire application stops working.
|
*/

// ── Public: Landing / redirect ────────────────────────────────────────────────
Route::get('/', function () {
    return redirect('/dashboard');
});

// ── Public: Login & Password Reset ────────────────────────────────────────────
// No 'guest' middleware — session-based auth checks would create redirect loops
// since our auth is API-token-based. The login page JS handles redirect if already logged in.
Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

$limiter = config('fortify.limiters.login');

Route::post('/forgot-password', [\Laravel\Fortify\Http\Controllers\PasswordResetLinkController::class, 'store'])
    ->middleware(['guest:'.config('fortify.guard')])
    ->name('password.email');

Route::get('/reset-password/{token}', [\Laravel\Fortify\Http\Controllers\NewPasswordController::class, 'create'])
    ->middleware(['guest:'.config('fortify.guard')])
    ->name('password.reset');

Route::post('/reset-password', [\Laravel\Fortify\Http\Controllers\NewPasswordController::class, 'store'])
    ->middleware(['guest:'.config('fortify.guard')])
    ->name('password.update');

// Override POST login and logout on the web side to force strict API-driven auth
Route::post('/login', function () {
    abort(404, 'Direct web login is disabled. Please authenticate via API.');
});
Route::post('/logout', function () {
    abort(404, 'Direct web logout is disabled. Please authenticate via API.');
});
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// ── Authenticated app shell — serves all dashboard pages ─────────────────────
Route::middleware(['web', 'api.auth', \App\Http\Middleware\EnsureApiActive::class])->group(function () {
    $pages = [
        'products'  => 'products.index',
        'customers' => 'customers.index',
        'suppliers' => 'suppliers.index',
        'sales'     => 'sales.index',
        'expenses'  => 'expenses.index',
        'reminders' => 'reminders.index',
        'reports'   => 'reports.index',
        'users'     => 'users.index',
        'dashboard' => 'dashboard',
    ];

    foreach ($pages as $segment => $name) {
        Route::get('/' . $segment, function () use ($segment) {
            return view('pages.' . $segment);
        })->name($name);
    }

    // Sales: New Sale page
    Route::get('/sales/create', function () {
        return view('pages.sales-create');
    })->name('sales.create');

    // Profile page
    Route::get('/profile', function () {
        // Serve the full Jetstream/Livewire-based account area which
        // includes two-factor, active sessions, and account deletion.
        return view('profile.show');
    })->name('app.profile.show');

    // Explicitly override the Livewire update route here inside the API-auth group
    // so that it receives the EnsureApiAuthenticated middleware reliably, preventing
    // the "Attempt to read property 'username' on null" error in Jetstream components.
    Route::post('/livewire/update', [\Livewire\Mechanisms\HandleRequests\HandleRequests::class, 'handleUpdate'])
        ->name('livewire.update');

    // Also protect Livewire's file upload routes (e.g., for profile photos)
    Route::post('/livewire/upload-file', [\Livewire\Features\SupportFileUploads\FileUploadController::class, 'handle'])
        ->name('livewire.upload-file');
    Route::get('/livewire/preview-file/{filename}', [\Livewire\Features\SupportFileUploads\FilePreviewController::class, 'handle'])
        ->name('livewire.preview-file');
});
