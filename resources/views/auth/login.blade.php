<x-guest-layout>
    <div class="flex flex-col items-center">

        {{-- Título --}}
        <p class="text-sm text-[#888888] mb-6 text-center">
            Accede o crea tu cuenta con Google
        </p>

        {{-- Google Button --}}
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

        @if ($errors->has('google'))
            <div class="mt-4 p-3 bg-[#ef4444]/10 border border-[#ef4444]/30 rounded-lg text-sm text-[#ef4444] text-center w-full">
                {{ $errors->first('google') }}
            </div>
        @endif

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