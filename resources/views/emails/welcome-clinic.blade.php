<!DOCTYPE html>
<html>

<head>
    <title>Bienvenido a DentalFlow</title>
</head>

<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <h2>¡Bienvenido a DentalFlow!</h2>

    <p>Usa el siguiente enlace para acceder al panel de tu clinica:</p>

    <p>
        <a href="{{ $url }}"
            style="background-color: #0891B2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px;">
            Access {{ $clinic->name }}
        </a>
    </p>

    <p>O copia este enlace: <br> {{ $url }}</p>

    <p>Gracias,<br>El equipo de DentalFlow</p>
</body>

</html>