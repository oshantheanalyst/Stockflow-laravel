<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controller imports
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;
use App\Http\Controllers\Api\CustomerController as ApiCustomerController;
use App\Http\Controllers\Api\SupplierController as ApiSupplierController;
use App\Http\Controllers\Api\ExpenseController as ApiExpenseController;
use App\Http\Controllers\Api\SalesController as ApiSalesController;
use App\Http\Controllers\Api\ReminderController as ApiReminderController;
use App\Http\Controllers\Api\UserController as ApiUserController;
use App\Http\Controllers\Api\CurrencyController as ApiCurrencyController;
use App\Http\Controllers\Api\ReportController as ApiReportController;

// Public: Authentication

// Google OAuth Redirect & Callback
Route::get('/auth/google/redirect', [\App\Http\Controllers\Api\GoogleAuthController::class, 'redirectToGoogle'])->name('api.auth.google.redirect');
Route::get('/auth/google/callback', [\App\Http\Controllers\Api\GoogleAuthController::class, 'handleGoogleCallback'])->name('api.auth.google.callback');

Route::post('/auth/login', function (Request $request) {
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    $user = \App\Models\User::where('username', $request->username)->first();

    if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password) || !$user->is_active) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials or account is deactivated.',
        ], 401);
    }

    \App\Models\SessionHistory::where('user_id', $user->id)
        ->where('status', 'active')
        ->update(['status' => 'revoked', 'logged_out_at' => now()]);

    $user->tokens()->delete();

    $userAgent = $request->header('User-Agent');
    $tokenName = $userAgent ? substr($userAgent, 0, 255) : 'stockflow-api-token';

    $newToken = $user->createToken($tokenName, ['*']);
    $token = $newToken->plainTextToken;

    $agent = tap(new \Laravel\Jetstream\Agent(), fn($a) => $a->setUserAgent($userAgent ?? ''));
    $deviceName = ($agent->platform() ?: 'Unknown') . ' - ' . ($agent->browser() ?: 'Unknown');

    \App\Models\SessionHistory::create([
        'user_id' => $user->id,
        'token_id' => $newToken->accessToken->id,
        'device_name' => $deviceName,
        'user_agent' => $userAgent ? substr($userAgent, 0, 512) : null,
        'ip_address' => $request->ip(),
        'status' => 'active',
        'logged_in_at' => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Authenticated successfully.',
        'user' => [
            'id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'profile_photo_url' => $user->profile_photo_url,
        ],
        'token' => $token,
        'token_type' => 'Bearer',
    ], 200);
})->middleware('throttle:10,1')->name('api.auth.login');

Route::post('/auth/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users,username',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = \App\Models\User::create([
        'name' => $request->name,
        'username' => strtolower($request->username),
        'email' => strtolower($request->email),
        'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        'role' => 'Admin', // All self-registered users are Admins
        'is_active' => true,
    ]);

    $userAgent = $request->header('User-Agent');
    $tokenName = $userAgent ? substr($userAgent, 0, 255) : 'stockflow-api-token';
    $token = $user->createToken($tokenName, ['*'])->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Registered successfully.',
        'user' => [
            'id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'profile_photo_url' => $user->profile_photo_url,
        ],
        'token' => $token,
        'token_type' => 'Bearer',
    ], 201);
})->middleware('throttle:10,1')->name('api.auth.register');

// Protected: requires a valid Sanctum Bearer token

Route::middleware(['auth:sanctum', 'active.user'])->name('api.')->group(function () {

    Route::post('/auth/logout', function (Request $request) {
        $token = $request->user()->currentAccessToken();
        if ($token && method_exists($token, 'delete')) {
            \App\Models\SessionHistory::where('token_id', $token->id)
                ->where('status', 'active')
                ->update(['status' => 'revoked', 'logged_out_at' => now()]);
            $token->delete();
        }
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully. Token revoked.',
        ], 200);
    })->name('auth.logout');

    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'profile_photo_url' => $user->profile_photo_url,
                'is_admin' => $user->isAdmin(),
                'can_edit' => $user->canEdit(),
                'can_delete' => $user->canDelete(),
            ],
        ]);
    })->name('user');

    // Core resource endpoints

    Route::apiResource('products', ApiProductController::class);
    Route::apiResource('categories', ApiCategoryController::class);
    Route::apiResource('orders', ApiOrderController::class);
    Route::apiResource('customers', ApiCustomerController::class);
    Route::apiResource('expenses', ApiExpenseController::class);
    Route::apiResource('reminders', ApiReminderController::class);

    // Suppliers with nested payments
    Route::apiResource('suppliers', ApiSupplierController::class);
    Route::get('/suppliers/{id}/payments', [ApiSupplierController::class, 'payments'])->name('suppliers.payments');
    Route::post('/suppliers/{id}/payments', [ApiSupplierController::class, 'storePayment'])->name('suppliers.storePayment');
    Route::put('/supplier-payments/{id}/mark-paid', [ApiSupplierController::class, 'markPaid'])->name('supplier-payments.markPaid');
    Route::delete('/supplier-payments/{id}', [ApiSupplierController::class, 'destroyPayment'])->name('supplier-payments.destroy');

    // Sales (Invoices)
    Route::get('/sales/create-form', [ApiSalesController::class, 'createForm'])->name('sales.create-form');
    Route::apiResource('sales', ApiSalesController::class);

    // External: Currency Converter
    Route::get('/currency/rates', [ApiCurrencyController::class, 'rates'])->name('currency.rates');
    Route::post('/currency/convert', [ApiCurrencyController::class, 'convert'])->name('currency.convert');

    // Admin-only endpoints
    Route::middleware(\App\Http\Middleware\CheckAdmin::class)->group(function () {
        Route::get('/reports', [ApiReportController::class, 'index'])->name('reports.index');
        Route::apiResource('users', ApiUserController::class);

        // Admin: reset a user's password
        Route::put('/users/{id}/reset-password', [ApiUserController::class, 'resetPassword'])->name('users.resetPassword');
    });
});
