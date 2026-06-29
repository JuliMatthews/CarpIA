<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirigiendo a Webpay...</title>
    <style>
        body {
            background-color: #0d0d0d;
            color: #f0f0f0;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
        }
        .spinner {
            border: 3px solid #2a2a2a;
            border-top: 3px solid #7c3aed;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <h2>Redirigiendo a Webpay...</h2>
        <p style="color: #888888;">Por favor espera mientras te redirigimos al formulario de pago.</p>
        
        <form id="webpay-form" action="{{ $url }}" method="POST">
            <input type="hidden" name="token_ws" value="{{ $token }}" />
        </form>
    </div>

    <script>
        document.getElementById('webpay-form').submit();
    </script>
</body>
</html>
