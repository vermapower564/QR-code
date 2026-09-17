<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'admin' => \App\Http\Middleware\CheckAdmin::class,
        ]);
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->redirectGuestsTo(fn () => route('login', ['auth_required' => 1]));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your security session has expired. Please refresh the page.',
                ], 419);
            }

            $target = $request->headers->get('referer') ?: url()->previous() ?: route('home');

            return redirect()->to($target)
                ->withInput($request->except(['_token', 'password', 'password_confirmation', 'profile_image', 'logo']))
                ->with('error', 'Your page session expired. All your form inputs have been saved — please click submit again.');
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Your security session has expired. Please refresh the page.',
                    ], 419);
                }

                $target = $request->headers->get('referer') ?: url()->previous() ?: route('home');

                return redirect()->to($target)
                    ->withInput($request->except(['_token', 'password', 'password_confirmation', 'profile_image', 'logo']))
                    ->with('error', 'Your page session expired. All your form inputs have been saved — please click submit again.');
            }
        });
    })->create();

if (env('LARAVEL_STORAGE_PATH')) {
    $app->useStoragePath(env('LARAVEL_STORAGE_PATH'));
}

$app->booted(function () {
    if ((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
        (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
        (env('APP_ENV') === 'production')) {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
});

return $app;

