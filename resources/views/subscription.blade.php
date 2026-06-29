<x-guest-layout>
    <div class="flex flex-col items-center text-center">

        <img src="{{ asset('pet-2.png') }}" alt="CarpIA" class="w-24 h-24 object-contain mb-4">

        <h1 class="text-2xl font-bold text-[#f0f0f0] mb-2">Activa tu cuenta</h1>
        <p class="text-sm text-[#888888] mb-8">Para usar CarpIA necesitas una suscripción activa.</p>

        {{-- Precio --}}
        <div class="w-full bg-[#1e1e1e] border border-[#2a2a2a] rounded-xl p-6 mb-6">
            <div class="text-4xl font-bold text-[#7c3aed] mb-1">$1.990</div>
            <div class="text-sm text-[#888888]">CLP / mes · Acceso completo a todos los modelos</div>
            <button class="mt-4 w-full px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-semibold transition-colors">
                Obtener Suscripción
            </button>
        </div>

        {{-- Divider --}}
        <div class="relative w-full my-2 mb-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-[#2a2a2a]"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-[#161616] text-[#888888]">o ingresa un código de acceso</span>
            </div>
        </div>

        {{-- Código promo --}}
        @if(session('success'))
            <div class="w-full mb-4 p-3 bg-green-500/10 border border-green-500/30 rounded-lg text-sm text-green-400">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="w-full mb-4 p-3 bg-red-500/10 border border-red-500/30 rounded-lg text-sm text-red-400">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('promo.redeem') }}" class="w-full">
            @csrf
            <div class="flex gap-2">
                <input
                    type="text"
                    name="code"
                    placeholder="CÓDIGO-PROMO"
                    class="flex-1 px-4 py-3 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm focus:border-[#7c3aed] focus:ring-1 focus:ring-[#7c3aed] outline-none uppercase"
                    required
                >
                <button type="submit" class="px-6 py-3 bg-[#7c3aed] hover:bg-[#6d28d9] text-white rounded-lg text-sm font-medium transition-colors">
                    Activar
                </button>
            </div>
            @error('code')
                <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </form>

        {{-- Salir --}}
        <form method="POST" action="{{ route('logout') }}" class="mt-8">
            @csrf
            <button type="submit" class="text-xs text-[#888888] hover:text-[#f0f0f0] transition-colors">
                Cerrar sesión
            </button>
        </form>

    </div>
</x-guest-layout>
