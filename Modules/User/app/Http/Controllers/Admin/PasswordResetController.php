<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PasswordResetController extends Controller
{
    private const GENERIC_SENT_MESSAGE = 'Jika alamat email terdaftar, kami akan mengirim tautan untuk mengatur ulang kata sandi. Periksa kotak masuk dan folder spam. Tautan dapat diminta kembali setelah 60 detik.';

    public function create(): \Inertia\Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:150'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'email.max' => 'Alamat email maksimal 150 karakter.',
        ]);

        try {
            Password::sendResetLink($credentials);
        } catch (Throwable $exception) {
            Log::error('Pengiriman email reset kata sandi gagal.', [
                'exception' => $exception::class,
            ]);
        }

        // Always use the same response so the endpoint cannot reveal whether an
        // account exists, is eligible, or was recently sent a reset link.
        return back()->with('status', self::GENERIC_SENT_MESSAGE);
    }

    public function edit(Request $request, string $token): Response
    {
        $response = Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ])->toResponse($request);

        // Reset tokens are secrets carried in the URL; keep this page out of
        // caches and prevent the token URL from being sent as a referrer.
        $response->headers->set('Cache-Control', 'no-store, private');
        $response->headers->set('Referrer-Policy', 'no-referrer');

        return $response;
    }

    public function update(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ], [
            'token.required' => 'Tautan reset tidak lengkap. Buka kembali tautan dari email.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'email.max' => 'Alamat email maksimal 150 karakter.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari 8 karakter.',
            'password.max' => 'Kata sandi maksimal 255 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $status = Password::reset(
            $credentials,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                // A reset is a credential change, so revoke existing API tokens.
                $user->tokens()->delete();
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with(
                'success',
                'Kata sandi berhasil diubah. Silakan masuk menggunakan kata sandi baru.',
            );
        }

        $message = match ($status) {
            Password::INVALID_USER => 'Alamat email tidak cocok dengan tautan reset ini.',
            Password::INVALID_TOKEN => 'Tautan reset tidak valid atau sudah kedaluwarsa. Minta tautan baru.',
            default => 'Tautan reset tidak dapat digunakan. Minta tautan baru dan coba lagi.',
        };

        return back()->withErrors(['email' => $message])->withInput($request->only('email'));
    }
}
