<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-[#f0f0f0] leading-tight">
                {{ __('Códigos Promocionales') }}
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

            {{-- Crear código --}}
            <div class="mb-6 p-6 bg-[#161616] border border-[#2a2a2a] rounded-xl">
                <h3 class="text-lg font-medium text-[#f0f0f0] mb-4">Crear nuevo código</h3>
                <form method="POST" action="{{ route('admin.promo-codes.store') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm text-[#888888] mb-1">Código</label>
                        <input type="text" name="code" required maxlength="50" placeholder="ej: CARPIA24" class="w-full px-3 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm focus:border-[#7c3aed] focus:outline-none uppercase">
                    </div>
                    <div>
                        <label class="block text-sm text-[#888888] mb-1">Duración (horas)</label>
                        <input type="number" name="duration_hours" required min="1" value="24" class="w-full px-3 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm focus:border-[#7c3aed] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm text-[#888888] mb-1">Usos máximos</label>
                        <input type="number" name="max_uses" min="1" placeholder="Ilimitado" class="w-full px-3 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm focus:border-[#7c3aed] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm text-[#888888] mb-1">Expira</label>
                        <input type="datetime-local" name="expires_at" class="w-full px-3 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm focus:border-[#7c3aed] focus:outline-none">
                    </div>
                    <div class="md:col-span-2 lg:col-span-4">
                        <label class="block text-sm text-[#888888] mb-1">Descripción (uso interno)</label>
                        <input type="text" name="description" maxlength="500" placeholder="Ej: Promo lanzamiento redes sociales" class="w-full px-3 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm focus:border-[#7c3aed] focus:outline-none">
                    </div>
                    <div class="md:col-span-2 lg:col-span-4 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-[#7c3aed] hover:bg-[#6d28d9] text-white text-sm rounded-lg transition-colors">
                            Crear código
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tabla de códigos --}}
            <div class="bg-[#161616] border border-[#2a2a2a] rounded-xl overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-[#2a2a2a]">
                            <th class="px-8 py-5 text-left text-sm font-semibold text-[#888888] uppercase">Código</th>
                            <th class="px-8 py-5 text-left text-sm font-semibold text-[#888888] uppercase">Descripción</th>
                            <th class="px-8 py-5 text-left text-sm font-semibold text-[#888888] uppercase">Usos</th>
                            <th class="px-8 py-5 text-left text-sm font-semibold text-[#888888] uppercase">Duración</th>
                            <th class="px-8 py-5 text-left text-sm font-semibold text-[#888888] uppercase">Expira</th>
                            <th class="px-8 py-5 text-left text-sm font-semibold text-[#888888] uppercase">Estado</th>
                            <th class="px-8 py-5 text-left text-sm font-semibold text-[#888888] uppercase">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2a2a2a]">
                        @forelse($codes as $code)
                            <tr class="hover:bg-[#1e1e1e]">
                                <td class="px-8 py-5">
                                    <span class="text-base font-mono font-bold text-[#f0f0f0]">{{ $code->code }}</span>
                                </td>
                                <td class="px-8 py-5 text-sm text-[#888888]">{{ $code->description ?: '—' }}</td>
                                <td class="px-8 py-5 text-sm text-[#f0f0f0]">
                                    {{ number_format($code->redemptions_count) }}
                                    @if($code->max_uses)
                                        / {{ number_format($code->max_uses) }}
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-sm text-[#f0f0f0]">{{ $code->duration_hours }}h</td>
                                <td class="px-8 py-5 text-sm text-[#888888]">
                                    {{ $code->expires_at ? $code->expires_at->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1.5 text-sm rounded {{ $code->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                        {{ $code->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <form method="POST" action="{{ route('admin.promo-codes.toggle', $code) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-sm text-[#7c3aed] hover:text-[#a78bfa]">
                                            {{ $code->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-10 text-center text-base text-[#888888]">No hay códigos promocionales</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-8 py-5 border-t border-[#2a2a2a]">
                    {{ $codes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
