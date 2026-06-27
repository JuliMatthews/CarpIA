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
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#888888] uppercase">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#888888] uppercase">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#888888] uppercase">Créditos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#888888] uppercase">Registro</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#888888] uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2a2a2a]">
                        @forelse($users as $user)
                            <tr class="hover:bg-[#1e1e1e]">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-[#7c3aed] flex items-center justify-center text-white text-sm font-medium">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm text-[#f0f0f0]">{{ $user->name }}</div>
                                            <div class="text-xs text-[#888888]">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded {{ $user->plan === 'free' ? 'bg-[#888888]/20 text-[#888888]' : 'bg-green-500/20 text-green-400' }}">
                                        {{ ucfirst($user->plan) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-[#f0f0f0]">{{ number_format($user->credits) }}</td>
                                <td class="px-6 py-4 text-sm text-[#888888]">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.user', $user) }}" class="text-sm text-[#7c3aed] hover:text-[#a78bfa]">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-[#888888]">No hay usuarios</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="px-6 py-4 border-t border-[#2a2a2a]">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
