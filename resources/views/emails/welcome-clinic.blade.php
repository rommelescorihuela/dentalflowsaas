<!DOCTYPE html>
<html>

<head>
    <title>Welcome to DentalFlowSaaS</title>
</head>

<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <h2>Welcome to DentalFlowSaaS!</h2>

    <p>Use the link below to access your new clinic dashboard:</p>

    <p>
        <a href="{{ $url }}"
            style="background-color: #2563EB; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            Access {{ $clinic->name }}
        </a>
    </p>

    <p>Or copy this link: <br> {{ $url }}</p>

    <p>Thank you,<br>The DentalFlow Team</p>
</body>

</html>