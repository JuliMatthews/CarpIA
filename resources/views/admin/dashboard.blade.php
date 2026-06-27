<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#f0f0f0] leading-tight">
            {{ __('Panel de Administración') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="p-6 bg-[#161616] border border-[#2a2a2a] rounded-xl">
                    <div class="text-sm text-[#888888]">Usuarios totales</div>
                    <div class="text-3xl font-bold text-[#f0f0f0] mt-1">{{ $stats['total_users'] }}</div>
                    <div class="text-xs text-[#888888] mt-1">{{ $stats['active_users'] }} activos (30 días)</div>
                </div>

                <div class="p-6 bg-[#161616] border border-[#2a2a2a] rounded-xl">
                    <div class="text-sm text-[#888888]">Conversaciones</div>
                    <div class="text-3xl font-bold text-[#f0f0f0] mt-1">{{ number_format($stats['total_conversations']) }}</div>
                    <div class="text-xs text-[#888888] mt-1">{{ number_format($stats['total_messages']) }} mensajes</div>
                </div>

                <div class="p-6 bg-[#161616] border border-[#2a2a2a] rounded-xl">
                    <div class="text-sm text-[#888888]">Suscripciones activas</div>
                    <div class="text-3xl font-bold text-[#f0f0f0] mt-1">{{ $stats['total_subscriptions'] }}</div>
                    <div class="text-xs text-[#888888] mt-1">{{ $stats['premium_users'] }} usuarios premium</div>
                </div>

                <div class="p-6 bg-[#161616] border border-[#2a2a2a] rounded-xl">
                    <div class="text-sm text-[#888888]">Créditos utilizados</div>
                    <div class="text-3xl font-bold text-[#f0f0f0] mt-1">{{ number_format($stats['total_credits_used']) }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Recent Users --}}
                <div class="p-6 bg-[#161616] border border-[#2a2a2a] rounded-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-[#f0f0f0]">Usuarios recientes</h3>
                        <a href="{{ route('admin.users') }}" class="text-sm text-[#7c3aed] hover:text-[#a78bfa]">Ver todos</a>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentUsers as $user)
                            <div class="flex items-center gap-3 p-3 bg-[#1e1e1e] rounded-lg">
                                <div class="w-8 h-8 rounded-full bg-[#7c3aed] flex items-center justify-center text-white text-sm font-medium">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm text-[#f0f0f0] truncate">{{ $user->name }}</div>
                                    <div class="text-xs text-[#888888] truncate">{{ $user->email }}</div>
                                </div>
                                <div class="text-xs text-[#888888]">{{ $user->created_at->diffForHumans() }}</div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-sm text-[#888888]">No hay usuarios</div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Subscriptions --}}
                <div class="p-6 bg-[#161616] border border-[#2a2a2a] rounded-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-[#f0f0f0]">Suscripciones recientes</h3>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentSubscriptions as $subscription)
                            <div class="flex items-center gap-3 p-3 bg-[#1e1e1e] rounded-lg">
                                <div class="w-8 h-8 rounded-full {{ $subscription->plan->name === 'free' ? 'bg-[#888888]' : 'bg-green-500' }} flex items-center justify-center text-white text-sm">
                                    {{ strtoupper(substr($subscription->plan->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm text-[#f0f0f0] truncate">{{ $subscription->user->name }}</div>
                                    <div class="text-xs text-[#888888]">{{ $subscription->plan->display_name }} - {{ $subscription->status }}</div>
                                </div>
                                <div class="text-xs text-[#888888]">{{ $subscription->created_at->diffForHumans() }}</div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-sm text-[#888888]">No hay suscripciones</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('admin.users') }}" class="p-4 bg-[#161616] border border-[#2a2a2a] rounded-xl hover:border-[#7c3aed] transition-colors">
                    <div class="text-[#7c3aed] mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-[#f0f0f0]">Gestionar usuarios</div>
                </a>

                <a href="{{ route('admin.models') }}" class="p-4 bg-[#161616] border border-[#2a2a2a] rounded-xl hover:border-[#7c3aed] transition-colors">
                    <div class="text-[#7c3aed] mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-[#f0f0f0]">Gestionar modelos</div>
                </a>

                <a href="{{ route('admin.plans') }}" class="p-4 bg-[#161616] border border-[#2a2a2a] rounded-xl hover:border-[#7c3aed] transition-colors">
                    <div class="text-[#7c3aed] mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-[#f0f0f0]">Gestionar planes</div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
