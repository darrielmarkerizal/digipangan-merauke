<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Services\AuthService;

class AuthAdminController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $this->authService->login($request, $request->credentials(), $request->remember());

        return redirect()->intended('/admin/dashboard')
            ->with('success', 'Berhasil masuk ke Portal Admin DigiPangan Merauke.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login')
            ->with('success', 'Berhasil keluar dari sesi admin.');
    }
}
