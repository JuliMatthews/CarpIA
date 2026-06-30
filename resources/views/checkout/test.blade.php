<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarpIA - Test Transbank Webpay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #0d0d0d; color: #f0f0f0; font-family: 'Inter', sans-serif; }
        .token-box { background: #1e1e1e; border: 1px solid #7c3aed; border-radius: 8px; word-break: break-all; }
        .copy-btn { background: #7c3aed; transition: background 0.2s; }
        .copy-btn:hover { background: #6d28d9; }
        .go-btn { background: #10b981; transition: background 0.2s; }
        .go-btn:hover { background: #059669; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full space-y-8">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-[#7c3aed]">CarpIA - Test Webpay</h1>
            <p class="text-[#888888] mt-2">Transacción de prueba para validación de Transbank</p>
        </div>

        <div class="bg-[#161616] rounded-lg p-6 space-y-6">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-[#888888]">Buy Order:</span>
                    <p class="font-mono text-[#a78bfa]">{{ $buyOrder }}</p>
                </div>
                <div>
                    <span class="text-[#888888]">Monto:</span>
                    <p class="font-mono text-[#a78bfa]">${{ number_format($amount, 0, ',', '.') }} CLP</p>
                </div>
            </div>

            <div>
                <label class="text-[#888888] text-sm">Token de la transacción:</label>
                <div class="token-box p-4 mt-2 relative">
                    <span id="token" class="text-sm text-[#f0f0f0]">{{ $token }}</span>
                    <button onclick="copyToken()" class="copy-btn absolute right-2 top-2 px-3 py-1 rounded text-white text-xs font-medium">
                        Copiar
                    </button>
                </div>
                <p class="text-[#888888] text-xs mt-1">Copia este token y pégalo en el formulario de Transbank</p>
            </div>

            <div class="border-t border-[#2a2a2a] pt-6 space-y-4">
                <h3 class="font-semibold text-[#f0f0f0]">Tarjetas de prueba:</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="bg-[#1e1e1e] p-3 rounded">
                        <p class="text-[#10b981] font-medium">✅ Aprobada</p>
                        <p class="font-mono">4051 8856 0044 6623</p>
                        <p class="text-[#888888]">CVV: 123 | RUT: 11.111.111-1</p>
                    </div>
                    <div class="bg-[#1e1e1e] p-3 rounded">
                        <p class="text-[#ef4444] font-medium">❌ Rechazada</p>
                        <p class="font-mono">5186 0595 5959 0568</p>
                        <p class="text-[#888888]">CVV: 123 | RUT: 11.111.111-1</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <a href="{{ $url }}" class="go-btn flex-1 text-center py-3 rounded-lg font-semibold text-white">
                    Ir a Webpay (Pagar)
                </a>
                <a href="{{ route('checkout.test') }}" class="flex-1 text-center py-3 rounded-lg font-semibold bg-[#2a2a2a] text-[#f0f0f0] hover:bg-[#3a3a3a] transition">
                    Generar nueva transacción
                </a>
            </div>
        </div>

        <p class="text-center text-[#888888] text-xs">
            Las transacciones de prueba expiran en 10 minutos. Genera una nueva si expira.
        </p>
    </div>

    <script>
        function copyToken() {
            const token = document.getElementById('token').textContent;
            navigator.clipboard.writeText(token).then(() => {
                const btn = event.target;
                btn.textContent = 'Copiado!';
                setTimeout(() => btn.textContent = 'Copiar', 2000);
            });
        }
    </script>
</body>
</html>
