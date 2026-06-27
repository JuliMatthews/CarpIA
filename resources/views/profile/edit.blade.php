<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#f0f0f0] leading-tight">
            {{ __('Perfil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Profile Information --}}
            <div class="p-4 sm:p-8 bg-[#161616] border border-[#2a2a2a] sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- User Settings --}}
            <div class="p-4 sm:p-8 bg-[#161616] border border-[#2a2a2a] sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-[#f0f0f0]">
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
            <div class="p-4 sm:p-8 bg-[#161616] border border-[#2a2a2a] sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="p-4 sm:p-8 bg-[#161616] border border-[#2a2a2a] sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
