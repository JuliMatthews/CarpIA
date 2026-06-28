<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CarpIA — Sin conexión</title>
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0d0d0d;
            color: #f0f0f0;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; padding: 2rem;
        }
        .container { text-align: center; max-width: 400px; }
        .avatar { font-size: 80px; margin-bottom: 1.5rem; }
        h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.75rem; }
        p { color: #888888; line-height: 1.6; margin-bottom: 2rem; }
        .btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.75rem 1.5rem; background-color: #7c3aed; color: white;
            text-decoration: none; border-radius: 0.75rem; font-weight: 500;
            transition: background-color 0.2s;
        }
        .btn:hover { background-color: #6d28d9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="avatar">🦦</div>
        <h1>Sin conexión</h1>
        <p>Parece que no tienes internet. El carpincho está tomando un descanso. Conéctate y vuelve a intentar.</p>
        <a href="/" class="btn">Reintentar</a>
    </div>
</body>
</html>
