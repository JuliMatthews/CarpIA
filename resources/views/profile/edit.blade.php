<x-app-layout>
    <div class="py-8 px-4 lg:px-8 max-w-4xl mx-auto space-y-6">
        {{-- Profile Information --}}
        <div class="card p-6 lg:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- User Settings --}}
        <div class="card p-6 lg:p-8">
            <div class="max-w-xl">
                <header>
                    <h2 class="text-lg font-semibold text-[#f0f0f0]">
                        {{ __('Configuración') }}
                    </h2>
                    <p class="mt-1 text-sm text-[#888888]">
                        {{ __('Personaliza tu experiencia en CarpIA.') }}
                    </p>
                </header>

                <div class="mt-6">
                    <livewire:settings.user-settings />
                </div>
            </div>
        </div>

        {{-- Update Password --}}
        <div class="card p-6 lg:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- Delete Account --}}
        <div class="card p-6 lg:p-8 border-red-500/20">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
