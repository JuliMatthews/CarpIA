<x-guest-layout>
    <div class="mb-4 text-sm text-[#888888]">
        ¿Olvidaste tu contraseña? No hay problema. Solo ingresa tu email y te enviaremos un enlace para restablecerla.
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Email" class="text-[#888888]" />
            <x-text-input id="email" class="block mt-1 w-full bg-[#1e1e1e] border-[#2a2a2a] text-[#f0f0f0] focus:border-[#7c3aed] focus:ring-[#7c3aed]" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="bg-[#7c3aed] hover:bg-[#6d28d9] focus:ring-[#7c3aed]">
                Enviar enlace de restablecimiento
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
