<x-guest-layout>
    <div x-data="{ mode: 'login' }">
        {{-- Google Button --}}
        <a href="{{ route('google.redirect') }}" class="flex items-center justify-center gap-3 w-full px-4 py-3 bg-white hover:bg-gray-100 text-gray-900 font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            <span>Continuar con Google</span>
        </a>

        @if ($errors->has('google'))
            <div class="mt-3 p-3 bg-[#ef4444]/10 border border-[#ef4444]/30 rounded-lg text-sm text-[#ef4444] text-center">
                {{ $errors->first('google') }}
            </div>
        @endif

        {{-- Divider --}}
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-[#2a2a2a]"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-[#161616] text-[#888888]">o</span>
            </div>
        </div>

        {{-- Toggle links --}}
        <div class="text-center mb-6">
            <button
                @click="mode = 'login'"
                :class="mode === 'login' ? 'text-[#f0f0f0] border-b-2 border-[#7c3aed]' : 'text-[#888888] hover:text-[#f0f0f0]'"
                class="px-4 py-2 text-sm font-medium transition-colors"
            >
                Iniciar sesión
            </button>
            <button
                @click="mode = 'register'"
                :class="mode === 'register' ? 'text-[#f0f0f0] border-b-2 border-[#7c3aed]' : 'text-[#888888] hover:text-[#f0f0f0]'"
                class="px-4 py-2 text-sm font-medium transition-colors"
            >
                Registrarse
            </button>
        </div>

        {{-- Login Form --}}
        <form x-show="mode === 'login'" x-transition method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-input-label for="login_email" value="Email" class="text-[#888888]" />
                <x-text-input id="login_email" class="block mt-1 w-full bg-[#1e1e1e] border-[#2a2a2a] text-[#f0f0f0] focus:border-[#7c3aed] focus:ring-[#7c3aed]" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="login_password" value="Contraseña" class="text-[#888888]" />
                <x-text-input id="login_password" class="block mt-1 w-full bg-[#1e1e1e] border-[#2a2a2a] text-[#f0f0f0] focus:border-[#7c3aed] focus:ring-[#7c3aed]"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-[#2a2a2a] bg-[#1e1e1e] text-[#7c3aed] shadow-sm focus:ring-[#7c3aed]" name="remember">
                    <span class="ms-2 text-sm text-[#888888]">Recordarme</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-4">
                @if (Route::has('password.request'))
                    <a class="text-sm text-[#888888] hover:text-[#f0f0f0]" href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif

                <x-primary-button class="bg-[#7c3aed] hover:bg-[#6d28d9] focus:ring-[#7c3aed]">
                    Iniciar sesión
                </x-primary-button>
            </div>
        </form>

        {{-- Register Form --}}
        <form x-show="mode === 'register'" x-transition method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-input-label for="register_name" value="Nombre" class="text-[#888888]" />
                <x-text-input id="register_name" class="block mt-1 w-full bg-[#1e1e1e] border-[#2a2a2a] text-[#f0f0f0] focus:border-[#7c3aed] focus:ring-[#7c3aed]" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="register_email" value="Email" class="text-[#888888]" />
                <x-text-input id="register_email" class="block mt-1 w-full bg-[#1e1e1e] border-[#2a2a2a] text-[#f0f0f0] focus:border-[#7c3aed] focus:ring-[#7c3aed]" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="register_password" value="Contraseña" class="text-[#888888]" />
                <x-text-input id="register_password" class="block mt-1 w-full bg-[#1e1e1e] border-[#2a2a2a] text-[#f0f0f0] focus:border-[#7c3aed] focus:ring-[#7c3aed]"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="register_password_confirmation" value="Confirmar contraseña" class="text-[#888888]" />
                <x-text-input id="register_password_confirmation" class="block mt-1 w-full bg-[#1e1e1e] border-[#2a2a2a] text-[#f0f0f0] focus:border-[#7c3aed] focus:ring-[#7c3aed]"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-6">
                <x-primary-button class="bg-[#7c3aed] hover:bg-[#6d28d9] focus:ring-[#7c3aed]">
                    Registrarse
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
