<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Spatie\QueryBuilder\Exceptions\InvalidQuery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo('/admin/dashboard');
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->wantsJson(),
        );

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                $statusCode = 500;
                $message = 'Server Error';
                $errors = null;

                if ($e instanceof ValidationException) {
                    $statusCode = 422;
                    $message = $e->getMessage();
                    $errors = $e->errors();
                } elseif ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                    $statusCode = 404;
                    $message = 'Resource not found.';
                } elseif ($e instanceof AuthenticationException) {
                    $statusCode = 401;
                    $message = 'Unauthenticated.';
                } elseif ($e instanceof AuthorizationException) {
                    $statusCode = 403;
                    $message = $e->getMessage() ?: 'This action is unauthorized.';
                } elseif ($e instanceof InvalidQuery) {
                    $statusCode = 400; // Bad Request
                    $message = $e->getMessage();
                } elseif ($e instanceof HttpException) {
                    $statusCode = $e->getStatusCode();
                    $message = $e->getMessage() ?: Response::$statusTexts[$statusCode] ?? 'Error';
                } else {
                    $message = config('app.debug') ? $e->getMessage() : 'Internal Server Error';
                }

                $response = [
                    'success' => false,
                    'message' => $message,
                ];

                if (! is_null($errors)) {
                    $response['errors'] = $errors;
                }

                return response()->json($response, $statusCode);
            }
        });
    })->create();
