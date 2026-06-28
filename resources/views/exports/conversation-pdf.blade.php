<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $conversation->title }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #1a1a1a; font-size: 12px; line-height: 1.6; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        .meta { color: #666; font-size: 11px; margin-bottom: 20px; }
        hr { border: none; border-top: 1px solid #ddd; margin: 16px 0; }
        .message { margin-bottom: 16px; }
        .role { font-weight: bold; font-size: 13px; margin-bottom: 2px; }
        .content { white-space: pre-wrap; }
        .footer { margin-top: 30px; font-size: 10px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <h1>{{ $conversation->title }}</h1>
    <div class="meta">
        Fecha: {{ $conversation->created_at->format('d/m/Y H:i') }}<br>
        Modelo: {{ $modelName }}
    </div>
    <hr>

    @foreach($conversation->messages as $message)
        <div class="message">
            <div class="role">{{ $message->role === 'user' ? 'Tú' : 'CarpIA' }}</div>
            <div class="content">{{ $message->content }}</div>
        </div>
    @endforeach

    <div class="footer">
        Generado por CarpIA.cl — {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
