<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'SIPENA — SMK Yadika 8')</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#1e293b;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:28px 0;">
<tr><td align="center">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 1px 4px rgba(15,39,68,.08);">

        {{-- Header --}}
        <tr>
            <td style="background:linear-gradient(135deg,#0f2744,#1a4a8a);padding:26px 30px;">
                <div style="font-size:16px;font-weight:800;color:#ffffff;letter-spacing:.5px;">SIPENA</div>
                <div style="font-size:11.5px;color:#e8a020;font-weight:700;margin-top:2px;">SMK YADIKA 8 JATIMULYA</div>
            </td>
        </tr>

        {{-- Body --}}
        <tr>
            <td style="padding:30px;">
                @yield('content')
            </td>
        </tr>

        {{-- Footer --}}
        <tr>
            <td style="padding:18px 30px;background:#f8fafc;border-top:1px solid #e2e8f0;">
                <p style="margin:0;font-size:11px;color:#94a3b8;line-height:1.5;">
                    Email ini dikirim otomatis oleh sistem SIPENA (Sistem Informasi PPDB SMK Yadika 8 Jatimulya).
                    Mohon tidak membalas email ini.
                </p>
            </td>
        </tr>

    </table>
</td></tr>
</table>
</body>
</html>
