<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CarpIA') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#0d0d0d] text-[#f0f0f0]">
        <div class="flex h-screen overflow-hidden">
            @include('layouts.sidebar')

            <main class="flex-1 flex flex-col overflow-hidden ml-64">
                @isset($header)
                    <header class="border-b border-[#2a2a2a] bg-[#0d0d0d] px-6 py-4">
                        <div class="max-w-3xl mx-auto">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <div class="flex-1 overflow-y-auto">
                    <div class="max-w-3xl mx-auto px-4 py-6">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
