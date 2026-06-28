@php
    $user = auth()->user();
    $hasAccess = ($user->subscription && $user->subscription->isActive()) || $user->hasActivePromoAccess();
@endphp

<x-app-layout>
    @if($hasAccess)
        <livewire:chat.chat-interface />
    @else
        <div class="flex items-center justify-center min-h-[calc(100vh-5rem)] px-4">
            <div class="text-center max-w-md">
                <img src="{{ asset('pet-2.png') }}" alt="CarpIA" class="w-24 h-24 mx-auto mb-6 opacity-40 grayscale" />
                <div class="text-5xl mb-4">🔒</div>
                <h2 class="text-2xl font-bold text-[#f0f0f0] mb-3">{{ __('Chat bloqueado') }}</h2>
                <p class="text-[#888888] mb-8 leading-relaxed">
                    {{ __('Suscríbete para acceder al chat con inteligencia artificial y todos los modelos disponibles.') }}
                </p>
                <a href="{{ route('planes') }}" class="btn-primary text-base px-8 py-3">
                    {{ __('Pagar mes de suscripción') }}
                </a>

                <div class="mt-12">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <span class="w-full border-t border-[#2a2a2a]"></span>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="px-4 text-sm text-[#888888] bg-[#0d0d0d]">{{ __('¿Tienes un código?') }}</span>
                        </div>
                    </div>

                    @if(session('error'))
                        <div class="mt-4 p-3 bg-red-500/10 border border-red-500/20 rounded-lg text-sm text-red-400">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="mt-4 p-3 bg-green-500/10 border border-green-500/20 rounded-lg text-sm text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('promo.redeem') }}" class="mt-4 flex gap-2">
                        @csrf
                        <input type="text" name="code" placeholder="{{ __('Ingresa tu código') }}" required
                            class="input flex-1 uppercase">
                        <button type="submit" class="btn-secondary whitespace-nowrap">
                            {{ __('Canjear') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
