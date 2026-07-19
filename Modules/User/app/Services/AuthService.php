<?php

namespace Modules\User\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Modules\User\Repositories\Contracts\UserRepositoryInterface;

class AuthService
{
    public function __construct(private readonly UserRepositoryInterface $users) {}

    public function login(Request $request, array $credentials, bool $remember = false): User
    {
        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = $this->users->findOrFail(Auth::id(), ['roles']);

        if (! $user->is_active) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => __('Akun ini dinonaktifkan. Hubungi administrator.'),
            ]);
        }

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return $user;
    }

    public function logout(Request $request): void
    {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }

    public function profile(int|string $id): User
    {
        return $this->users->findOrFail($id, ['roles']);
    }

    public function updateProfile(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $attributes = array_filter([
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
            ], fn ($value) => ! is_null($value));

            if (! empty($data['password'])) {
                $attributes['password'] = Hash::make($data['password']);
            }

            if ($attributes !== []) {
                $this->users->update($user, $attributes);
            }

            if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                $this->users->replaceAvatar($user, $data['avatar']);
            }

            if (! empty($data['remove_avatar'])) {
                $this->users->clearAvatar($user);
            }

            return $this->users->findOrFail($user->id, ['roles']);
        });
    }
}
