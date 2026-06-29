@php
    $userTheme = auth()->check() ? (auth()->user()->settings?->theme ?? 'dark') : 'dark';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark"
      class="{{ $userTheme }}"
      x-data="{ theme: '{{ $userTheme }}', sidebarOpen: window.innerWidth > 1024 }"
      x-init="
        $watch('theme', val => {
            document.documentElement.className = val;
            localStorage.setItem('theme', val);
        });
        window.addEventListener('theme-changed', e => theme = e.detail.theme);
        theme = localStorage.getItem('theme') || '{{ $userTheme }}';
        window.addEventListener('resize', () => { if (window.innerWidth > 1024) sidebarOpen = true; });
    ">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'CarpIA') }}</title>

        <link rel="icon" type="image/png" href="/favicon.png">
        <link rel="shortcut icon" href="/favicon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        {{-- PWA --}}
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#7c3aed">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="CarpIA">
        <link rel="apple-touch-icon" href="/pet-2.png">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* ---- Utility classes ---- */
            .sidebar-item {
                display: flex; align-items: center; gap: 0.75rem;
                padding: 0.5rem 0.75rem; border-radius: 0.5rem;
                font-size: 0.875rem; color: #888888;
                transition: all 0.2s;
            }
            .sidebar-item:hover { background-color: #1e1e1e; color: #f0f0f0; }
            .sidebar-item-active {
                background-color: rgba(124,58,237,0.1); color: #a78bfa;
            }
            .sidebar-item-active:hover {
                background-color: rgba(124,58,237,0.15); color: #a78bfa;
            }
            .sidebar-section-title {
                font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
                letter-spacing: 0.05em; color: #555555;
                padding-left: 0.75rem; margin-bottom: 0.5rem; margin-top: 1rem;
            }
            .card {
                background-color: #161616; border: 1px solid #2a2a2a;
                border-radius: 0.75rem;
            }
            .btn-primary {
                display: inline-flex; align-items: center; justify-content: center;
                gap: 0.5rem; padding: 0.625rem 1.25rem;
                background-color: #7c3aed; color: white;
                font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem;
                transition: all 0.2s;
            }
            .btn-primary:hover { background-color: #6d28d9; }
            .btn-primary:focus { outline: none; box-shadow: 0 0 0 2px rgba(124,58,237,0.5); }
            .btn-secondary {
                display: inline-flex; align-items: center; justify-content: center;
                gap: 0.5rem; padding: 0.625rem 1.25rem;
                background-color: #1e1e1e; border: 1px solid #2a2a2a;
                color: #f0f0f0; font-size: 0.875rem; font-weight: 500;
                border-radius: 0.5rem; transition: all 0.2s;
            }
            .btn-secondary:hover { border-color: #7c3aed; }
            .btn-secondary:focus { outline: none; box-shadow: 0 0 0 2px rgba(124,58,237,0.5); }
            .input {
                width: 100%; padding: 0.625rem 1rem;
                background-color: #1e1e1e; border: 1px solid #2a2a2a;
                border-radius: 0.5rem; color: #f0f0f0; font-size: 0.875rem;
                transition: all 0.2s;
            }
            .input::placeholder { color: #666666; }
            .input:focus { border-color: #7c3aed; outline: none; box-shadow: 0 0 0 2px rgba(124,58,237,0.2); }

            /* ---- Light theme overrides ---- */
            html:not(.dark) body { background-color: #f5f5f5; color: #1a1a1a; }
            html:not(.dark) .bg-\[\#0d0d0d\] { background-color: #f5f5f5 !important; }
            html:not(.dark) .bg-\[\#161616\] { background-color: #ffffff !important; }
            html:not(.dark) .bg-\[\#1e1e1e\] { background-color: #f0f0f0 !important; }
            html:not(.dark) .border-\[\#2a2a2a\] { border-color: #e0e0e0 !important; }
            html:not(.dark) .text-\[\#f0f0f0\] { color: #1a1a1a !important; }
            html:not(.dark) .text-\[\#888888\] { color: #666666 !important; }
            html:not(.dark) .text-\[\#a78bfa\] { color: #7c3aed !important; }
            html:not(.dark) .text-\[\#555555\] { color: #999999 !important; }
            html:not(.dark) .hover\:bg-\[\#1e1e1e\]:hover { background-color: #e8e8e8 !important; }
            html:not(.dark) .hover\:text-\[\#f0f0f0\]:hover { color: #1a1a1a !important; }
            html:not(.dark) .hover\:bg-\[\#2a2a2a\]:hover { background-color: #e0e0e0 !important; }
            html:not(.dark) .hover\:bg-\[\#6d28d9\]:hover { background-color: #5b21b6 !important; }
            html:not(.dark) .hover\:bg-\[\#7c3aed\]:hover { background-color: #5b21b6 !important; }
            html:not(.dark) .hover\:border-\[\#7c3aed\]:hover { border-color: #7c3aed !important; }
            html:not(.dark) .focus\:border-\[\#7c3aed\]:focus { border-color: #7c3aed !important; }
            html:not(.dark) .focus\:ring-\[\#7c3aed\]:focus { --tw-ring-color: #7c3aed !important; }
            html:not(.dark) input, html:not(.dark) select, html:not(.dark) textarea { color-scheme: light; }

            html:not(.dark) .card { background-color: #ffffff !important; border-color: #e0e0e0 !important; }
            html:not(.dark) .sidebar-item:hover { background-color: #f0f0f0; color: #1a1a1a; }
            html:not(.dark) .sidebar-item-active { background-color: rgba(124,58,237,0.08); color: #7c3aed; }
            html:not(.dark) .sidebar-section-title { color: #999999; }
            html:not(.dark) .btn-secondary { background-color: #f0f0f0; border-color: #e0e0e0; color: #1a1a1a; }
            html:not(.dark) .input { background-color: #f0f0f0; border-color: #e0e0e0; color: #1a1a1a; }
            html:not(.dark) .input::placeholder { color: #999999; }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#0d0d0d] text-[#f0f0f0]">
        <div class="flex h-screen overflow-hidden">
            @include('layouts.sidebar')

            <div class="flex-1 flex flex-col overflow-hidden lg:ml-64">
                {{-- Header --}}
                <header class="sticky top-0 z-40 flex items-center justify-between px-4 lg:px-6 py-3 border-b border-[#2a2a2a] bg-[#0d0d0d]/95 backdrop-blur-sm">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden flex items-center justify-center w-9 h-9 rounded-lg text-[#888888] hover:bg-[#1e1e1e] hover:text-[#f0f0f0] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        @isset($header)
                            {{ $header }}
                        @else
                            <h2 class="text-lg font-semibold text-[#f0f0f0]">Dashboard</h2>
                        @endisset
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Theme toggle --}}
                        <button @click="theme = theme === 'dark' ? 'light' : 'dark'"
                            class="flex items-center justify-center w-9 h-9 rounded-lg text-[#888888] hover:bg-[#1e1e1e] hover:text-[#f0f0f0] transition-colors"
                            title="Toggle theme">
                            <svg x-show="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        {{-- Profile --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-lg hover:bg-[#1e1e1e] transition-colors">
                                <div class="w-8 h-8 rounded-full bg-[#7c3aed] flex items-center justify-center text-white text-sm font-medium">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="hidden sm:block text-sm text-[#f0f0f0] font-medium">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" @keydown.escape.window="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="absolute right-0 top-full mt-2 w-56 bg-[#1e1e1e] border border-[#2a2a2a] rounded-xl shadow-2xl overflow-hidden z-50">
                                <div class="px-4 py-3 border-b border-[#2a2a2a]">
                                    <p class="text-sm font-medium text-[#f0f0f0]">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-[#888888] truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-[#888888] hover:bg-[#2a2a2a] hover:text-[#f0f0f0] transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>{{ __('Perfil') }}</span>
                                </a>
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-[#888888] hover:bg-[#2a2a2a] hover:text-[#f0f0f0] transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ __('Panel Admin') }}</span>
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 text-sm text-[#888888] hover:bg-[#2a2a2a] hover:text-[#f0f0f0] transition-colors border-t border-[#2a2a2a]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        <span>{{ __('Salir') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                {{-- Content --}}
                <div class="flex-1 overflow-y-auto">
                    {{ $slot }}
                </div>
            </div>
        </div>

        {{-- Mobile sidebar overlay --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-30 lg:hidden" x-transition.opacity style="display:none"></div>
    </body>
</html>
