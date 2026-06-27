<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" value="Nombre" class="text-[#888888]" />
            <x-text-input id="name" class="block mt-1 w-full bg-[#1e1e1e] border-[#2a2a2a] text-[#f0f0f0] focus:border-[#7c3aed] focus:ring-[#7c3aed]" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="Email" class="text-[#888888]" />
            <x-text-input id="email" class="block mt-1 w-full bg-[#1e1e1e] border-[#2a2a2a] text-[#f0f0f0] focus:border-[#7c3aed] focus:ring-[#7c3aed]" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" class="text-[#888888]" />
            <x-text-input id="password" class="block mt-1 w-full bg-[#1e1e1e] border-[#2a2a2a] text-[#f0f0f0] focus:border-[#7c3aed] focus:ring-[#7c3aed]"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar contraseña" class="text-[#888888]" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full bg-[#1e1e1e] border-[#2a2a2a] text-[#f0f0f0] focus:border-[#7c3aed] focus:ring-[#7c3aed]"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-[#888888] hover:text-[#f0f0f0] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#7c3aed]" href="{{ route('login') }}">
                ¿Ya tienes cuenta?
            </a>

            <x-primary-button class="ms-4 bg-[#7c3aed] hover:bg-[#6d28d9] focus:ring-[#7c3aed]">
                Registrarse
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
