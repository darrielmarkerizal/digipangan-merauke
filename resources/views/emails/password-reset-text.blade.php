{{ $appName }} — Atur Ulang Kata Sandi

Halo {{ $userName }},

Kami menerima permintaan untuk mengatur ulang kata sandi akun DigiPangan Anda.
Buka tautan berikut untuk membuat kata sandi baru:
{{ $resetUrl }}

Tautan ini berlaku selama {{ $expireMinutes }} menit dan hanya dapat digunakan satu kali.

Jika Anda tidak meminta perubahan ini, abaikan email ini. Kata sandi Anda tidak akan berubah sampai tautan digunakan.
@if ($supportEmail)

Butuh bantuan? Hubungi {{ $supportEmail }}.
@endif
