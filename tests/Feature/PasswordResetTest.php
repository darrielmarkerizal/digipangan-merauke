<?php

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

function passwordResetUser(string $email = 'reset.user@digipangan.test'): User
{
    return User::factory()->create([
        'name' => 'Pengguna DigiPangan',
        'email' => $email,
        'password' => 'password-lama-123',
        'is_active' => true,
    ]);
}

function requestPasswordResetFor(string $email): void
{
    test()->post(route('password.email'), ['email' => $email])->assertRedirect();
}

function latestResetTokenFor(User $user): string
{
    /** @var ResetPasswordNotification $notification */
    $notification = Notification::sent($user, ResetPasswordNotification::class)->first();

    return $notification->token;
}

describe('permintaan reset kata sandi', function () {
    it('menampilkan halaman lupa kata sandi dan tautan tersedia dari login', function () {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Auth/ForgotPassword')
                ->where('status', null));

        expect(route('password.request'))->toContain('/lupa-kata-sandi');
    });

    it('mengirim notifikasi bermerek kepada email pengguna terdaftar', function () {
        Notification::fake();
        $user = passwordResetUser();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordNotification::class);

        $mail = Notification::sent($user, ResetPasswordNotification::class)->first()->toMail($user);

        expect($mail->subject)->toContain('Atur Ulang Kata Sandi')
            ->and($mail->view['html'])->toBe('emails.password-reset')
            ->and($mail->view['text'])->toBe('emails.password-reset-text')
            ->and($mail->viewData['resetUrl'])->toContain('/reset-password/')
            ->and(view('emails.password-reset', $mail->viewData)->render())
            ->toContain('Buat Kata Sandi Baru')
            ->toContain('60 menit');
    });

    it('memberikan respons yang sama untuk email yang tidak terdaftar', function () {
        Notification::fake();
        $user = passwordResetUser();

        $knownResponse = $this->post(route('password.email'), ['email' => $user->email]);
        $unknownResponse = $this->post(route('password.email'), ['email' => 'not-found@digipangan.test']);

        expect($unknownResponse->getSession()->get('status'))
            ->toBe($knownResponse->getSession()->get('status'));

        Notification::assertSentTo($user, ResetPasswordNotification::class);
        Notification::assertNothingSentTo(new User(['email' => 'not-found@digipangan.test']));
    });

    it('menolak email tidak valid dan wajib diisi', function () {
        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'bukan-email'])
            ->assertSessionHasErrors('email');

        $this->from(route('password.request'))
            ->post(route('password.email'), [])
            ->assertSessionHasErrors('email');
    });

    it('mengikuti jeda broker sebelum mengirim tautan baru untuk email yang sama', function () {
        Notification::fake();
        $user = passwordResetUser();

        requestPasswordResetFor($user->email);
        requestPasswordResetFor($user->email);

        Notification::assertSentTimes(ResetPasswordNotification::class, 1);
    });

    it('membatasi permintaan reset berulang dari satu alamat IP', function () {
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->post(route('password.email'), ['email' => 'missing@digipangan.test']);
        }

        $this->post(route('password.email'), ['email' => 'missing@digipangan.test'])
            ->assertSessionHasErrors('email');
    });

    it('tetap menampilkan respons umum saat server email gagal', function () {
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andThrow(new RuntimeException('SMTP details must not be shown to users'));

        Log::shouldReceive('error')
            ->once()
            ->with('Pengiriman email reset kata sandi gagal.', ['exception' => RuntimeException::class]);

        $this->post(route('password.email'), ['email' => 'someone@digipangan.test'])
            ->assertRedirect()
            ->assertSessionHas('status', fn (string $status): bool => str_contains($status, 'Jika alamat email terdaftar'));
    });
});

describe('penggantian kata sandi dengan token', function () {
    it('menampilkan form reset dengan proteksi cache dan referrer', function () {
        $this->get(route('password.reset', ['token' => 'opaque-token', 'email' => 'user@digipangan.test']))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('Referrer-Policy', 'no-referrer')
            ->assertInertia(fn ($page) => $page
                ->component('Auth/ResetPassword')
                ->where('token', 'opaque-token')
                ->where('email', 'user@digipangan.test'));
    });

    it('mengganti kata sandi, merotasi remember token, mencabut token API, dan memakai token reset sekali saja', function () {
        Notification::fake();
        $user = passwordResetUser();
        $user->createToken('existing-device');

        requestPasswordResetFor($user->email);
        $token = latestResetTokenFor($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'kata-sandi-baru-123',
            'password_confirmation' => 'kata-sandi-baru-123',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $user->refresh();
        expect(Hash::check('kata-sandi-baru-123', $user->password))->toBeTrue()
            ->and($user->remember_token)->not->toBeNull()
            ->and($user->tokens()->count())->toBe(0);

        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);

        $this->from(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->post(route('password.update'), [
                'token' => $token,
                'email' => $user->email,
                'password' => 'another-password-123',
                'password_confirmation' => 'another-password-123',
            ])
            ->assertSessionHasErrors('email');

        expect(Hash::check('kata-sandi-baru-123', $user->fresh()->password))->toBeTrue();
    });

    it('menolak kata sandi terlalu pendek dan konfirmasi yang tidak cocok tanpa memakai token', function () {
        Notification::fake();
        $user = passwordResetUser();
        requestPasswordResetFor($user->email);
        $token = latestResetTokenFor($user);

        $this->from(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->post(route('password.update'), [
                'token' => $token,
                'email' => $user->email,
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertSessionHasErrors(['password']);

        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
        expect(Hash::check('password-lama-123', $user->fresh()->password))->toBeTrue();
    });

    it('menolak token palsu, email yang tidak cocok, dan token kedaluwarsa', function () {
        Notification::fake();
        config(['auth.passwords.users.expire' => 1]);
        $user = passwordResetUser();
        requestPasswordResetFor($user->email);
        $token = latestResetTokenFor($user);

        $this->from(route('password.reset', ['token' => 'invalid-token', 'email' => $user->email]))
            ->post(route('password.update'), [
                'token' => 'invalid-token',
                'email' => $user->email,
                'password' => 'kata-sandi-baru-123',
                'password_confirmation' => 'kata-sandi-baru-123',
            ])
            ->assertSessionHasErrors('email');

        $this->from(route('password.reset', ['token' => $token, 'email' => 'other@digipangan.test']))
            ->post(route('password.update'), [
                'token' => $token,
                'email' => 'other@digipangan.test',
                'password' => 'kata-sandi-baru-123',
                'password_confirmation' => 'kata-sandi-baru-123',
            ])
            ->assertSessionHasErrors('email');

        $this->travel(2)->minutes();

        $this->from(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->post(route('password.update'), [
                'token' => $token,
                'email' => $user->email,
                'password' => 'kata-sandi-baru-123',
                'password_confirmation' => 'kata-sandi-baru-123',
            ])
            ->assertSessionHasErrors('email');

        expect(Hash::check('password-lama-123', $user->fresh()->password))->toBeTrue();
    });
});
