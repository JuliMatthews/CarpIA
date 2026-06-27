<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CarpIA') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#0d0d0d] text-[#f0f0f0] min-h-screen">
        {{-- Nav --}}
        <nav class="fixed top-0 left-0 right-0 z-50 bg-[#0d0d0d]/80 backdrop-blur-sm border-b border-[#2a2a2a]">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                <a href="/" class="flex items-center gap-3">
                    <span class="text-3xl">🐾</span>
                    <span class="text-xl font-bold text-[#7c3aed]">CarpIA</span>
                </a>

                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-sm text-[#888888] hover:text-[#f0f0f0] transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm text-[#888888] hover:text-[#f0f0f0] transition-colors">
                                Iniciar sesión
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-4 py-2 text-sm bg-[#7c3aed] hover:bg-[#6d28d9] text-white rounded-lg transition-colors">
                                    Registrarse
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        {{-- Hero --}}
        <main class="pt-24 pb-16 px-6">
            <div class="max-w-4xl mx-auto text-center">
                {{-- Logo + Carpincho --}}
                <div class="mb-8">
                    <div class="text-8xl mb-4">🐾</div>
                    <h1 class="text-5xl font-bold text-[#f0f0f0] mb-4">
                        <span class="text-[#7c3aed]">Carp</span>IA
                    </h1>
                    <p class="text-xl text-[#888888] max-w-2xl mx-auto">
                        Tu asistente de IA unificado. Un múltiples modelos de inteligencia artificial en una sola interfaz.
                    </p>
                </div>

                {{-- Sugerencias de prompts --}}
                <div class="mb-16">
                    <h2 class="text-sm font-semibold text-[#888888] uppercase tracking-wider mb-6">¿Qué puedo hacer por ti?</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-4 bg-[#161616] border border-[#2a2a2a] rounded-xl text-left hover:border-[#7c3aed] transition-colors cursor-pointer">
                            <div class="text-2xl mb-2">💡</div>
                            <div class="text-sm font-medium text-[#f0f0f0]">Explicar conceptos</div>
                            <div class="text-xs text-[#888888] mt-1">Aprende cualquier tema de forma clara y sencilla</div>
                        </div>
                        <div class="p-4 bg-[#161616] border border-[#2a2a2a] rounded-xl text-left hover:border-[#7c3aed] transition-colors cursor-pointer">
                            <div class="text-2xl mb-2">💻</div>
                            <div class="text-sm font-medium text-[#f0f0f0]">Escribir código</div>
                            <div class="text-xs text-[#888888] mt-1">PHP, Python, JavaScript y más</div>
                        </div>
                        <div class="p-4 bg-[#161616] border border-[#2a2a2a] rounded-xl text-left hover:border-[#7c3aed] transition-colors cursor-pointer">
                            <div class="text-2xl mb-2">✍️</div>
                            <div class="text-sm font-medium text-[#f0f0f0]">Crear contenido</div>
                            <div class="text-xs text-[#888888] mt-1">Artículos, emails, guiones, traducciones</div>
                        </div>
                        <div class="p-4 bg-[#161616] border border-[#2a2a2a] rounded-xl text-left hover:border-[#7c3aed] transition-colors cursor-pointer">
                            <div class="text-2xl mb-2">📊</div>
                            <div class="text-sm font-medium text-[#f0f0f0]">Analizar datos</div>
                            <div class="text-xs text-[#888888] mt-1">Resúmenes, comparativas, recomendaciones</div>
                        </div>
                    </div>
                </div>

                {{-- Modelos disponibles --}}
                <div class="mb-16">
                    <h2 class="text-sm font-semibold text-[#888888] uppercase tracking-wider mb-6">Modelos disponibles</h2>
                    <div class="flex flex-wrap justify-center gap-3">
                        @php
                            $providers = \App\Models\AiProvider::with('models')->where('is_active', true)->get();
                        @endphp
                        @foreach($providers as $provider)
                            <div class="px-4 py-2 bg-[#161616] border border-[#2a2a2a] rounded-lg">
                                <span class="text-sm text-[#f0f0f0]">{{ $provider->name }}</span>
                                <span class="text-xs text-[#888888] ml-2">({{ $provider->models->count() }} modelos)</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- CTA --}}
                <div>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-medium rounded-xl transition-colors">
                            <span>Ir al chat</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-medium rounded-xl transition-colors">
                            <span>Comenzar</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="border-t border-[#2a2a2a] py-8 px-6">
            <div class="max-w-4xl mx-auto text-center text-sm text-[#888888]">
                <p>CarpIA.cl &copy; {{ date('Y') }} — El ChatGPT de Latinoamérica</p>
            </div>
        </footer>
    </body>
</html>
