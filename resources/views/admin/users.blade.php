<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-[#f0f0f0] leading-tight">
                {{ __('Gestión de Usuarios') }}
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

            <div class="bg-[#161616] border border-[#2a2a2a] rounded-xl overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-[#2a2a2a]">
                                <th class="px-8 py-5 text-left text-sm font-semibold text-[#888888] uppercase">Usuario</th>
                                <th class="px-8 py-5 text-left text-sm font-semibold text-[#888888] uppercase">Plan</th>
                                <th class="px-8 py-5 text-left text-sm font-semibold text-[#888888] uppercase">Pagó</th>
                                <th class="px-8 py-5 text-left text-sm font-semibold text-[#888888] uppercase">Créditos</th>
                                <th class="px-8 py-5 text-left text-sm font-semibold text-[#888888] uppercase">Conversaciones</th>
                                <th class="px-8 py-5 text-left text-sm font-semibold text-[#888888] uppercase">Registro</th>
                                <th class="px-8 py-5 text-left text-sm font-semibold text-[#888888] uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2a2a2a]">
                            @forelse($users as $user)
                                @php
                                    $hasPaid = $user->subscription && $user->subscription->isActive();
                                    $convCount = $user->conversations_count ?? $user->conversations()->count();
                                @endphp
                                <tr class="hover:bg-[#1e1e1e]">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-full bg-[#7c3aed] flex items-center justify-center text-white font-medium">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-base text-[#f0f0f0] font-medium">{{ $user->name }}</div>
                                                <div class="text-sm text-[#888888]">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="px-3 py-1.5 text-sm rounded {{ $user->plan === 'free' ? 'bg-[#888888]/20 text-[#888888]' : 'bg-green-500/20 text-green-400' }}">
                                            {{ ucfirst($user->plan) }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5">
                                        @if($hasPaid)
                                            <span class="px-3 py-1.5 text-sm rounded bg-green-500/20 text-green-400">✅ Pagó</span>
                                        @else
                                            <span class="px-3 py-1.5 text-sm rounded bg-red-500/20 text-red-400">❌ No pagó</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5 text-base text-[#f0f0f0] font-medium">{{ number_format($user->credits) }}</td>
                                    <td class="px-8 py-5 text-base text-[#888888]">{{ number_format($convCount) }}</td>
                                    <td class="px-8 py-5 text-base text-[#888888]">{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td class="px-8 py-5">
                                        <a href="{{ route('admin.user', $user) }}" class="text-base text-[#7c3aed] hover:text-[#a78bfa] font-medium">Ver detalle →</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-8 py-10 text-center text-base text-[#888888]">No hay usuarios</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                <div class="px-8 py-5 border-t border-[#2a2a2a]">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
