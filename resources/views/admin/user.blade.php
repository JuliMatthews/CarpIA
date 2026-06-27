<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-[#f0f0f0] leading-tight">
                {{ __('Detalle de Usuario') }}
            </h2>
            <a href="{{ route('admin.users') }}" class="text-sm text-[#7c3aed] hover:text-[#a78bfa]">← Volver</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-sm text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- User Info --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="p-6 bg-[#161616] border border-[#2a2a2a] rounded-xl">
                        <h3 class="text-lg font-medium text-[#f0f0f0] mb-4">Información del usuario</h3>

                        <form method="POST" action="{{ route('admin.users.update', $user) }}">
                            @csrf
                            @method('PATCH')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-[#888888] mb-1">Nombre</label>
                                    <input type="text" value="{{ $user->name }}" disabled class="w-full px-3 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm text-[#888888] mb-1">Email</label>
                                    <input type="email" value="{{ $user->email }}" disabled class="w-full px-3 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm text-[#888888] mb-1">Créditos</label>
                                    <input type="number" name="credits" value="{{ $user->credits }}" min="0" class="w-full px-3 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm focus:border-[#7c3aed] focus:outline-none">
                                </div>

                                <div>
                                    <label class="block text-sm text-[#888888] mb-1">Plan</label>
                                    <select name="plan" class="w-full px-3 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm focus:border-[#7c3aed] focus:outline-none">
                                        <option value="free" {{ $user->plan === 'free' ? 'selected' : '' }}>Free</option>
                                        <option value="premium" {{ $user->plan === 'premium' ? 'selected' : '' }}>Premium</option>
                                        <option value="pro" {{ $user->plan === 'pro' ? 'selected' : '' }}>Pro</option>
                                    </select>
                                </div>

                                <div class="flex items-center gap-2">
                                    <input type="hidden" name="is_admin" value="0">
                                    <input type="checkbox" name="is_admin" value="1" {{ $user->is_admin ? 'checked' : '' }} class="rounded border-[#2a2a2a] bg-[#1e1e1e] text-[#7c3aed] focus:ring-[#7c3aed]">
                                    <label class="text-sm text-[#888888]">Administrador</label>
                                </div>
                            </div>

                            <div class="mt-4 flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white text-sm rounded-lg transition-colors">
                                    Guardar cambios
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Credit History --}}
                    <div class="p-6 bg-[#161616] border border-[#2a2a2a] rounded-xl">
                        <h3 class="text-lg font-medium text-[#f0f0f0] mb-4">Historial de créditos</h3>

                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @forelse($user->creditTransactions->take(20) as $transaction)
                                <div class="flex items-center justify-between p-3 bg-[#1e1e1e] rounded-lg">
                                    <div>
                                        <div class="text-sm text-[#f0f0f0]">{{ $transaction->description }}</div>
                                        <div class="text-xs text-[#888888]">{{ $transaction->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                    <div class="text-sm {{ $transaction->amount > 0 ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount) }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-sm text-[#888888]">Sin transacciones</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    <div class="p-6 bg-[#161616] border border-[#2a2a2a] rounded-xl">
                        <h3 class="text-lg font-medium text-[#f0f0f0] mb-4">Estadísticas</h3>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-[#888888]">Conversaciones</span>
                                <span class="text-sm text-[#f0f0f0]">{{ $stats['total_conversations'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-[#888888]">Mensajes</span>
                                <span class="text-sm text-[#f0f0f0]">{{ $stats['total_messages'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-[#888888]">Créditos usados</span>
                                <span class="text-sm text-[#f0f0f0]">{{ number_format($stats['credits_used']) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-[#161616] border border-[#2a2a2a] rounded-xl">
                        <h3 class="text-lg font-medium text-[#f0f0f0] mb-4">Suscripción</h3>

                        @if($user->subscription)
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-[#888888]">Plan</span>
                                    <span class="text-sm text-[#f0f0f0]">{{ $user->subscription->plan->display_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-[#888888]">Estado</span>
                                    <span class="text-sm {{ $user->subscription->status === 'active' ? 'text-green-400' : 'text-[#888888]' }}">{{ $user->subscription->status }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-[#888888]">Expira</span>
                                    <span class="text-sm text-[#f0f0f0]">{{ $user->subscription->ends_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        @else
                            <div class="text-sm text-[#888888]">Sin suscripción activa</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
