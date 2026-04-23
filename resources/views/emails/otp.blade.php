<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Kode OTP</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f4f6f8; padding:20px;">
    <div style="max-width:500px; margin:auto; background:#ffffff; padding:20px; border-radius:8px;">
        <h2 style="color:#333;">Verifikasi Email</h2>

        <p>Halo,</p>

        <p>Gunakan kode OTP berikut untuk verifikasi:</p>

        <div style="font-size:28px; font-weight:bold; letter-spacing:6px; text-align:center; margin:20px 0;">
            {{ $otp }}
        </div>

        <p style="color:#555;">
            Kode ini berlaku selama <strong>5 menit</strong>.
            Jangan bagikan kode ini kepada siapa pun.
        </p>

        <hr>

        <p style="font-size:12px; color:#888;">
            Jika kamu tidak merasa meminta OTP, abaikan email ini.
        </p>
    </div>
</body>

</html>