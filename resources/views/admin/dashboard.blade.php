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
                <div class="card p-6">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-10 h-10 rounded-xl bg-[#7c3aed]/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#7c3aed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="text-sm text-[#888888]">Usuarios totales</span>
                    </div>
                    <div class="text-3xl font-bold text-[#f0f0f0] mt-3">{{ $stats['total_users'] }}</div>
                    <div class="text-xs text-[#888888] mt-1">{{ $stats['active_users'] }} activos (30 días)</div>
                </div>

                <div class="card p-6">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <span class="text-sm text-[#888888]">Conversaciones</span>
                    </div>
                    <div class="text-3xl font-bold text-[#f0f0f0] mt-3">{{ number_format($stats['total_conversations']) }}</div>
                    <div class="text-xs text-[#888888] mt-1">{{ number_format($stats['total_messages']) }} mensajes</div>
                </div>

                <div class="card p-6">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-sm text-[#888888]">Suscripciones activas</span>
                    </div>
                    <div class="text-3xl font-bold text-[#f0f0f0] mt-3">{{ $stats['total_subscriptions'] }}</div>
                    <div class="text-xs text-[#888888] mt-1">{{ $stats['premium_users'] }} usuarios premium</div>
                </div>

                <div class="card p-6">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-10 h-10 rounded-xl bg-yellow-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <span class="text-sm text-[#888888]">Créditos utilizados</span>
                    </div>
                    <div class="text-3xl font-bold text-[#f0f0f0] mt-3">{{ number_format($stats['total_credits_used']) }}</div>
                </div>
            </div>

            {{-- Activity Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Mensajes por día --}}
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-[#f0f0f0] mb-4">Mensajes por día (30 días)</h3>
                    <div class="space-y-1">
                        @php $maxCount = $messagesByDay->max('count') ?: 1; @endphp
                        @forelse($messagesByDay as $day)
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-[#888888] w-24 shrink-0">{{ \Carbon\Carbon::parse($day->date)->format('d/m') }}</span>
                                <div class="flex-1 h-5 bg-[#1e1e1e] rounded-full overflow-hidden">
                                    <div class="h-full bg-[#7c3aed] rounded-full transition-all" style="width: {{ ($day->count / $maxCount) * 100 }}%"></div>
                                </div>
                                <span class="text-xs text-[#888888] w-8 text-right">{{ $day->count }}</span>
                            </div>
                        @empty
                            <div class="text-center py-4 text-sm text-[#888888]">Sin datos</div>
                        @endforelse
                    </div>
                </div>

                {{-- Modelos más usados --}}
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-[#f0f0f0] mb-4">Modelos más usados</h3>
                    <div class="space-y-2">
                        @php $maxModelCount = $modelUsage->max('count') ?: 1; @endphp
                        @forelse($modelUsage as $model)
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-[#f0f0f0] w-48 truncate shrink-0">{{ $model->model }}</span>
                                <div class="flex-1 h-5 bg-[#1e1e1e] rounded-full overflow-hidden">
                                    <div class="h-full bg-green-500 rounded-full transition-all" style="width: {{ ($model->count / $maxModelCount) * 100 }}%"></div>
                                </div>
                                <span class="text-xs text-[#888888] w-8 text-right">{{ $model->count }}</span>
                            </div>
                        @empty
                            <div class="text-center py-4 text-sm text-[#888888]">Sin datos</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Recent Users --}}
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-[#f0f0f0]">Usuarios recientes</h3>
                        <a href="{{ route('admin.users') }}" class="text-sm text-[#7c3aed] hover:text-[#a78bfa]">Ver todos</a>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentUsers as $user)
                            <a href="{{ route('admin.user', $user) }}" class="flex items-center gap-3 p-3 bg-[#1e1e1e] rounded-lg hover:bg-[#2a2a2a] transition-colors">
                                <div class="w-8 h-8 rounded-full bg-[#7c3aed] flex items-center justify-center text-white text-sm font-medium">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm text-[#f0f0f0] truncate">{{ $user->name }}</div>
                                    <div class="text-xs text-[#888888] truncate">{{ $user->email }}</div>
                                </div>
                                <div class="text-xs text-[#888888]">{{ $user->created_at->diffForHumans() }}</div>
                            </a>
                        @empty
                            <div class="text-center py-4 text-sm text-[#888888]">No hay usuarios</div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Subscriptions --}}
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-[#f0f0f0]">Suscripciones recientes</h3>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentSubscriptions as $subscription)
                            <a href="{{ route('admin.user', $subscription->user) }}" class="flex items-center gap-3 p-3 bg-[#1e1e1e] rounded-lg hover:bg-[#2a2a2a] transition-colors">
                                <div class="w-8 h-8 rounded-full {{ $subscription->plan->name === 'free' ? 'bg-[#888888]' : 'bg-green-500' }} flex items-center justify-center text-white text-sm">
                                    {{ strtoupper(substr($subscription->plan->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm text-[#f0f0f0] truncate">{{ $subscription->user->name }}</div>
                                    <div class="text-xs text-[#888888]">{{ $subscription->plan->display_name }} - {{ $subscription->status }}</div>
                                </div>
                                <div class="text-xs text-[#888888]">{{ $subscription->created_at->diffForHumans() }}</div>
                            </a>
                        @empty
                            <div class="text-center py-4 text-sm text-[#888888]">No hay suscripciones</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.users') }}" class="p-4 bg-[#161616] border border-[#2a2a2a] rounded-xl hover:border-[#7c3aed] transition-colors">
                    <div class="text-[#7c3aed] mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-[#f0f0f0]">Usuarios</div>
                </a>

                <a href="{{ route('admin.models') }}" class="p-4 bg-[#161616] border border-[#2a2a2a] rounded-xl hover:border-[#7c3aed] transition-colors">
                    <div class="text-[#7c3aed] mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-[#f0f0f0]">Modelos</div>
                </a>

                <a href="{{ route('admin.plans') }}" class="p-4 bg-[#161616] border border-[#2a2a2a] rounded-xl hover:border-[#7c3aed] transition-colors">
                    <div class="text-[#7c3aed] mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-[#f0f0f0]">Planes</div>
                </a>

                <a href="{{ route('admin.promo-codes') }}" class="p-4 bg-[#161616] border border-[#2a2a2a] rounded-xl hover:border-[#7c3aed] transition-colors">
                    <div class="text-[#7c3aed] mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-[#f0f0f0]">Códigos Promo</div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
