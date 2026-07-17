<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),

            'auth' => [
                'user' => fn () => $request->user()?->only(
                    'id', 'name', 'email', 'phone', 'avatar_url'
                ),
                'roles' => fn () => $request->user()?->getRoleNames(),
                'permissions' => fn () => $request->user()
                    ?->getAllPermissions()
                    ->pluck('name'),
            ],

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'info'    => fn () => $request->session()->get('info'),
            ],

            'app' => [
                'name'            => config('app.name'),
                'env'             => app()->environment(),
                'locale'          => app()->getLocale(),
                'whatsapp_admin'  => config('digipangan.whatsapp_admin'),
                'contact_email'   => config('digipangan.contact_email'),
            ],

            'query' => fn () => $request->query(),
        ];
    }
}
