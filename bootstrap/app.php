<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->wantsJson(),
        );

        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                $statusCode = 500;
                $message = 'Server Error';
                $errors = null;

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    $statusCode = 422;
                    $message = $e->getMessage();
                    $errors = $e->errors();
                } elseif ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException || $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    $statusCode = 404;
                    $message = 'Resource not found.';
                } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    $statusCode = 401;
                    $message = 'Unauthenticated.';
                } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    $statusCode = 403;
                    $message = $e->getMessage() ?: 'This action is unauthorized.';
                } elseif ($e instanceof \Spatie\QueryBuilder\Exceptions\InvalidQuery) {
                    $statusCode = 400; // Bad Request
                    $message = $e->getMessage();
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    $statusCode = $e->getStatusCode();
                    $message = $e->getMessage() ?: \Symfony\Component\HttpFoundation\Response::$statusTexts[$statusCode] ?? 'Error';
                } else {
                    $message = config('app.debug') ? $e->getMessage() : 'Internal Server Error';
                }

                $response = [
                    'success' => false,
                    'message' => $message,
                ];

                if (!is_null($errors)) {
                    $response['errors'] = $errors;
                }

                return response()->json($response, $statusCode);
            }
        });
    })->create();
