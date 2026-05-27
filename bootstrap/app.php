<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            // EnsureFrontendRequestsAreStateful removed to force fully stateless API tokens
        ]);
        $middleware->web(append: [
            //
        ]);
        $middleware->alias([
            'api.auth' => \App\Http\Middleware\EnsureApiAuthenticated::class,
            'active.user' => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            // API routes are protected by Sanctum Bearer tokens — CSRF not needed
            'api/*',
            // Livewire's own update/upload endpoints have internal component checksums
            'livewire/update',
            'livewire/upload-file',
            'livewire/preview-file/*',
        ]);
        // Exclude api_token from cookie encryption so JS-set plaintext
        // Bearer tokens can be read correctly by EnsureApiAuthenticated
        $middleware->encryptCookies(except: [
            'api_token',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e, $request) {
            if ($e->getStatusCode() === 419) {
                \Illuminate\Support\Facades\Log::warning('419 Page Expired detected!', [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'headers' => $request->headers->all(),
                    'session_id' => $request->session() ? $request->session()->getId() : null,
                    'has_token' => $request->has('_token'),
                    'token_match' => $request->session() && $request->input('_token') === $request->session()->token(),
                ]);

                if (! ($request->expectsJson() || $request->ajax() || $request->hasHeader('X-Livewire'))) {
                    return redirect($request->getSchemeAndHttpHost() . $request->getBasePath() . '/login')->with('error', 'Your session has expired. Please log in again.');
                }
            }
        });
    })->create();
