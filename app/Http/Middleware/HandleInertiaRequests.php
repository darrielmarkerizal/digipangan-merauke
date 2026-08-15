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
                'user' => fn () => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'avatar_url' => $request->user()->avatarUrl(),
                    'region_id' => $request->user()->region_id,
                    'region' => $request->user()->region ? [
                        'id' => $request->user()->region->id,
                        'name' => $request->user()->region->name,
                        'slug' => $request->user()->region->slug,
                    ] : null,
                ] : null,
                'roles' => fn () => $request->user()?->getRoleNames() ?? [],
                'permissions' => fn () => $request->user()
                    ?->getAllPermissions()
                    ->pluck('name') ?? [],
                'is_district_admin' => fn () => $request->user()?->isDistrictAdmin() ?? false,
                'is_super_admin' => fn () => $request->user()?->isSuperAdmin() ?? false,
            ],

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'info' => fn () => $request->session()->get('info'),
            ],

            'app' => [
                'name' => config('app.name'),
                'env' => app()->environment(),
                'locale' => app()->getLocale(),
                'whatsapp_admin' => config('digipangan.whatsapp_admin'),
                'contact_email' => config('digipangan.contact_email'),
            ],

            'query' => fn () => $request->query(),
        ];
    }
}
