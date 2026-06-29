<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ __('CarpIA') }} — {{ __('Planes') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#0d0d0d] text-[#f0f0f0] min-h-screen">
        <nav class="fixed top-0 left-0 right-0 z-50 bg-[#0d0d0d]/80 backdrop-blur-sm border-b border-[#2a2a2a]">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                <a href="/" class="flex items-center gap-3">
                    <img src="{{ asset('pet-2.png') }}" alt="CarpIA" class="h-8 w-8 object-contain">
                    <span class="text-xl font-bold text-[#7c3aed]">CarpIA</span>
                </a>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm text-[#888888] hover:text-[#f0f0f0] transition-colors">{{ __('Volver al inicio') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm text-[#888888] hover:text-[#f0f0f0] transition-colors">{{ __('Iniciar sesión') }}</a>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="pt-24 pb-16 px-6">
            <div class="max-w-5xl mx-auto text-center">
                <div class="mb-4">
                    <img src="{{ asset('pet-2.png') }}" alt="CarpIA" class="h-16 w-16 mx-auto mb-4 object-contain">
                </div>
                <h1 class="text-4xl font-bold text-[#f0f0f0] mb-4">
                    {{ __('Suscripción CarpIA') }}
                </h1>
                <p class="text-lg text-[#888888] max-w-2xl mx-auto mb-32">
                    {{ __('Accede a todos los modelos de IA por solo $1.990 al mes.') }}
                </p>

                <div class="w-80 mx-auto">
                    <div class="bg-[#161616] border border-[#2a2a2a] rounded-2xl p-8 text-left">
                        <div class="text-center mb-6">
                            <h3 class="text-xl font-bold text-[#f0f0f0]">{{ __('Plan Único') }}</h3>
                            <p class="text-sm text-[#888888] mt-1">{{ __('Acceso completo a CarpIA') }}</p>
                        </div>
                        <div class="text-center mb-6">
                            <span class="text-5xl font-bold text-[#f0f0f0]">$1.990</span>
                            <span class="text-[#888888] text-lg">/mes</span>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center gap-2 text-sm text-[#f0f0f0]">
                                <svg class="w-4 h-4 text-[#7c3aed] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Todos los modelos de IA disponibles') }}
                            </li>
                            <li class="flex items-center gap-2 text-sm text-[#f0f0f0]">
                                <svg class="w-4 h-4 text-[#7c3aed] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Mensajes ilimitados') }}
                            </li>
                            <li class="flex items-center gap-2 text-sm text-[#f0f0f0]">
                                <svg class="w-4 h-4 text-[#7c3aed] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Historial de conversaciones') }}
                            </li>
                            <li class="flex items-center gap-2 text-sm text-[#f0f0f0]">
                                <svg class="w-4 h-4 text-[#7c3aed] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Biblioteca de prompts') }}
                            </li>
                        </ul>
                        @auth
                            <button class="w-full px-6 py-4 bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-medium rounded-xl transition-colors text-lg">
                                {{ __('Pagar $1.990 — Suscribirse') }}
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="block text-center px-6 py-4 bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-medium rounded-xl transition-colors text-lg">
                                {{ __('Comenzar') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </main>

        <footer class="border-t border-[#2a2a2a] py-8 px-6">
            <div class="max-w-4xl mx-auto text-center text-sm text-[#888888]">
                <p>{{ __('CarpIA.cl &copy; :year — Paga con MercadoPago, Webpay o transferencia', ['year' => date('Y')]) }}</p>
            </div>
        </footer>
    </body>
</html>