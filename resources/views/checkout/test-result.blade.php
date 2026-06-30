<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarpIA - Resultado Test Webpay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #0d0d0d; color: #f0f0f0; font-family: 'Inter', sans-serif; }
        .token-box { background: #1e1e1e; border: 1px solid #2a2a2a; border-radius: 8px; word-break: break-all; }
        .copy-btn { background: #7c3aed; transition: background 0.2s; }
        .copy-btn:hover { background: #6d28d9; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full space-y-8">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-[#7c3aed]">CarpIA - Resultado Test</h1>
        </div>

        <div class="bg-[#161616] rounded-lg p-6 space-y-6">
            {{-- Status Badge --}}
            <div class="text-center">
                @if($success)
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-[#10b981]/20 text-[#10b981] rounded-full font-semibold">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Transacción APROBADA
                    </div>
                @else
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-[#ef4444]/20 text-[#ef4444] rounded-full font-semibold">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        Transacción RECHAZADA
                    </div>
                @endif
                <p class="text-[#888888] mt-3">{{ $message }}</p>
            </div>

            {{-- Token para copiar --}}
            @if($token)
            <div>
                <label class="text-[#888888] text-sm">Token de la transacción (para el formulario de Transbank):</label>
                <div class="token-box p-4 mt-2 relative">
                    <span id="token" class="text-sm text-[#f0f0f0]">{{ $token }}</span>
                    <button onclick="copyToken()" class="copy-btn absolute right-2 top-2 px-3 py-1 rounded text-white text-xs font-medium">
                        Copiar
                    </button>
                </div>
            </div>
            @endif

            {{-- Detalles --}}
            @if($details)
            <div class="border-t border-[#2a2a2a] pt-6">
                <h3 class="font-semibold text-[#f0f0f0] mb-3">Detalles de la transacción:</h3>
                <div class="grid grid-cols-2 gap-3 text-sm bg-[#1e1e1e] p-4 rounded">
                    <div>
                        <span class="text-[#888888]">Estado:</span>
                        <p class="font-mono text-[#a78bfa]">{{ $details['status'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-[#888888]">Código de autorización:</span>
                        <p class="font-mono text-[#a78bfa]">{{ $details['authorization_code'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-[#888888]">Monto:</span>
                        <p class="font-mono text-[#a78bfa]">${{ number_format($details['amount'] ?? 0, 0, ',', '.') }} CLP</p>
                    </div>
                    <div>
                        <span class="text-[#888888]">Buy Order:</span>
                        <p class="font-mono text-[#a78bfa]">{{ $details['buy_order'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-[#888888]">Últimos 4 dígitos:</span>
                        <p class="font-mono text-[#a78bfa]">{{ $details['card_detail']['card_number'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-[#888888]">Código de respuesta:</span>
                        <p class="font-mono text-[#a78bfa]">{{ $details['response_code'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Info de la transacción original --}}
            @if($testTx)
            <div class="border-t border-[#2a2a2a] pt-6">
                <h3 class="font-semibold text-[#f0f0f0] mb-3">Info de la transacción creada:</h3>
                <div class="grid grid-cols-2 gap-3 text-sm bg-[#1e1e1e] p-4 rounded">
                    <div>
                        <span class="text-[#888888]">Buy Order original:</span>
                        <p class="font-mono text-[#a78bfa]">{{ $testTx['buy_order'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-[#888888]">Monto:</span>
                        <p class="font-mono text-[#a78bfa]">${{ number_format($testTx['amount'] ?? 0, 0, ',', '.') }} CLP</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="flex gap-4">
                <a href="{{ route('checkout.test') }}" class="flex-1 text-center py-3 rounded-lg font-semibold bg-[#7c3aed] text-white hover:bg-[#6d28d9] transition">
                    Nueva transacción APROBADA
                </a>
                <a href="{{ route('checkout.test') }}" class="flex-1 text-center py-3 rounded-lg font-semibold bg-[#2a2a2a] text-[#f0f0f0] hover:bg-[#3a3a3a] transition">
                    Generar otra prueba
                </a>
            </div>
        </div>

        <p class="text-center text-[#888888] text-xs">
            Copia el token y pégalo en el formulario de validación de Transbank.
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
