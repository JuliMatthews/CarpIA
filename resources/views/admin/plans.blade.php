<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-[#f0f0f0] leading-tight">
                {{ __('Gestión de Planes') }}
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-[#7c3aed] hover:text-[#a78bfa]">← Volver</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-sm text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($plans as $plan)
                    <div class="p-6 bg-[#161616] border border-[#2a2a2a] rounded-xl">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-[#f0f0f0]">{{ $plan->display_name }}</h3>
                            <span class="px-2 py-1 text-xs {{ $plan->is_active ? 'bg-green-500/20 text-green-400' : 'bg-[#888888]/20 text-[#888888]' }} rounded">
                                {{ $plan->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span class="text-sm text-[#888888]">Créditos/mes</span>
                                <span class="text-sm text-[#f0f0f0]">{{ number_format($plan->monthly_credits) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-[#888888]">Precio mensual</span>
                                <span class="text-sm text-[#f0f0f0]">${{ number_format($plan->monthly_price) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-[#888888]">Precio anual</span>
                                <span class="text-sm text-[#f0f0f0]">${{ number_format($plan->yearly_price) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-[#888888]">Suscripciones</span>
                                <span class="text-sm text-[#f0f0f0]">{{ $plan->subscriptions_count }}</span>
                            </div>
                        </div>

                        @if($plan->features)
                            <div class="mb-4">
                                <div class="text-xs text-[#888888] mb-2">Características:</div>
                                <ul class="space-y-1">
                                    @foreach($plan->features as $feature)
                                        <li class="flex items-center gap-2 text-xs text-[#f0f0f0]">
                                            <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-[#888888]">
                        No hay planes configurados. Ejecuta el seeder: `php artisan db:seed --class=PlanSeeder`
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
