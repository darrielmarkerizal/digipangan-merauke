<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Atur ulang kata sandi</title>
</head>
<body style="margin:0; padding:0; background:#f2f6f3; color:#18352b; font-family:Arial, Helvetica, sans-serif;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent;">
        Tautan aman untuk mengatur ulang kata sandi akun DigiPangan Anda.
    </div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f2f6f3; padding:36px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px; background:#ffffff; border:1px solid #dfe9e3; border-radius:16px; overflow:hidden;">
                    <tr>
                        <td style="padding:28px 36px; background:#087b59; color:#ffffff;">
                            <div style="font-size:22px; line-height:1.3; font-weight:700; letter-spacing:-0.3px;">{{ $appName }}</div>
                            <div style="margin-top:5px; color:#d9f1e7; font-size:13px; line-height:1.5;">Portal layanan pangan dan pertanian Merauke</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px;">
                            <div style="display:inline-block; margin-bottom:18px; padding:7px 11px; border-radius:999px; background:#e8f5ee; color:#087b59; font-size:12px; font-weight:700; letter-spacing:0.4px;">PEMULIHAN AKUN</div>
                            <h1 style="margin:0 0 16px; color:#18352b; font-size:25px; line-height:1.3;">Atur ulang kata sandi</h1>
                            <p style="margin:0 0 14px; color:#52665d; font-size:15px; line-height:1.7;">Halo {{ $userName }},</p>
                            <p style="margin:0 0 24px; color:#52665d; font-size:15px; line-height:1.7;">Kami menerima permintaan untuk mengatur ulang kata sandi akun DigiPangan Anda. Gunakan tombol di bawah untuk membuat kata sandi baru.</p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 24px;">
                                <tr>
                                    <td align="center" style="border-radius:9px; background:#087b59;">
                                        <a href="{{ $resetUrl }}" style="display:inline-block; padding:14px 22px; border:1px solid #087b59; border-radius:9px; color:#ffffff; font-size:15px; line-height:1.2; font-weight:700; text-decoration:none;">Buat Kata Sandi Baru</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0 0 10px; color:#65776e; font-size:13px; line-height:1.7;">Tautan ini berlaku selama <strong>{{ $expireMinutes }} menit</strong> dan hanya dapat digunakan satu kali.</p>
                            <p style="margin:0 0 8px; color:#65776e; font-size:13px; line-height:1.7;">Jika tombol tidak dapat dibuka, salin tautan berikut ke browser:</p>
                            <p style="margin:0 0 24px; overflow-wrap:anywhere; color:#087b59; font-size:12px; line-height:1.7;"><a href="{{ $resetUrl }}" style="color:#087b59;">{{ $resetUrl }}</a></p>
                            <div style="height:1px; background:#e7eee9;"></div>
                            <p style="margin:20px 0 0; color:#65776e; font-size:13px; line-height:1.7;">Jika Anda tidak meminta perubahan ini, abaikan email ini. Kata sandi Anda tidak akan berubah sampai tautan digunakan.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 36px; background:#f8faf8; border-top:1px solid #e7eee9; color:#738178; font-size:12px; line-height:1.6;">
                            Email otomatis dari {{ $appName }}. Mohon jangan membalas email ini.
                            @if ($supportEmail)
                                <br>Butuh bantuan? Hubungi <a href="mailto:{{ $supportEmail }}" style="color:#087b59;">{{ $supportEmail }}</a>.
                            @endif
                        </td>
                    </tr>
                </table>
                <p style="margin:18px 0 0; color:#87948d; font-size:11px; line-height:1.5;">© {{ date('Y') }} {{ $appName }} · Papua Selatan, Indonesia</p>
            </td>
        </tr>
    </table>
</body>
</html>
