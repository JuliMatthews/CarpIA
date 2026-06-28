<x-guest-layout>
    <div x-data="{ mode: 'login' }">
        {{-- Google Button (GIS - sin redirect, evita ModSecurity) --}}
        <div id="google-login-container" class="flex justify-center mb-4">
            <div id="g_id_onload"
                 data-client_id="{{ config('services.google.client_id') }}"
                 data-callback="handleGoogleCredential"
                 data-auto_prompt="false"
                 data-ux_mode="popup">
            </div>
            <div class="g_id_signin"
                 data-type="standard"
                 data-shape="rectangular"
                 data-theme="outline"
                 data-text="continue_with"
                 data-size="large"
                 data-width="280"
                 data-logo_alignment="left">
            </div>
        </div>

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

    {{-- GIS Google script --}}
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        function handleGoogleCredential(response) {
            fetch('{{ route('google.token') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ credential: response.credential }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    alert('Error al iniciar sesión con Google');
                }
            })
            .catch(() => alert('Error de conexión'));
        }
    </script>
</x-guest-layout>
