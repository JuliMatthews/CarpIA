<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-[#f0f0f0] leading-tight">
                {{ __('Gestión de Modelos') }}
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#888888] uppercase">Modelo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#888888] uppercase">Proveedor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#888888] uppercase">Contexto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#888888] uppercase">Gratuito</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#888888] uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#888888] uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2a2a2a]">
                        @forelse($models as $model)
                            <tr class="hover:bg-[#1e1e1e]">
                                <td class="px-6 py-4 text-sm text-[#f0f0f0]">{{ $model->name }}</td>
                                <td class="px-6 py-4 text-sm text-[#888888]">{{ $model->provider->name }}</td>
                                <td class="px-6 py-4 text-sm text-[#888888]">{{ number_format($model->context_window) }}</td>
                                <td class="px-6 py-4">
                                    @if($model->is_free)
                                        <span class="px-2 py-1 text-xs bg-green-500/20 text-green-400 rounded">Sí</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-red-500/20 text-red-400 rounded">No</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs {{ $model->is_active ? 'bg-green-500/20 text-green-400' : 'bg-[#888888]/20 text-[#888888]' }} rounded">
                                        {{ $model->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.models.toggle', $model) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-sm {{ $model->is_active ? 'text-red-400 hover:text-red-300' : 'text-green-400 hover:text-green-300' }}">
                                            {{ $model->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-[#888888]">No hay modelos</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
