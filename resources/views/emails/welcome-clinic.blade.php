<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Bienvenido a DentalFlow</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f1ea; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif; color:#23282a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f1ea; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background-color:#ffffff; border:1px solid #e8e4da; border-radius:14px; overflow:hidden;">
                    <tr>
                        <td style="background-color:#1f9e8f; padding:24px 32px;">
                            <p style="margin:0; font-size:18px; font-weight:700; color:#ffffff; letter-spacing:-0.01em;">DentalFlow</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <h1 style="margin:0 0 12px; font-size:22px; line-height:1.25; color:#23282a;">Bienvenido a DentalFlow</h1>
                            <p style="margin:0 0 20px; font-size:15px; line-height:1.6; color:#5c574d;">
                                Tu clínica <strong style="color:#23282a;">{{ $clinic->name }}</strong> ya está lista. Usa el siguiente botón para acceder al panel.
                            </p>
                            <p style="margin:0 0 24px;">
                                <a href="{{ $url }}" style="display:inline-block; background-color:#1f9e8f; color:#ffffff; padding:12px 24px; text-decoration:none; border-radius:10px; font-size:15px; font-weight:600;">
                                    Acceder a mi panel
                                </a>
                            </p>
                            <p style="margin:0 0 8px; font-size:13px; color:#847d6f;">Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
                            <p style="margin:0; font-size:13px; word-break:break-all;"><a href="{{ $url }}" style="color:#157e73;">{{ $url }}</a></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px; border-top:1px solid #e8e4da; background-color:#faf9f6;">
                            <p style="margin:0; font-size:12px; color:#948e82;">Gracias por confiar en DentalFlow. Este es un correo automático, no respondas a este mensaje.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
