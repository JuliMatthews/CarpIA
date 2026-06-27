<div>
    <form wire:submit="save" class="space-y-6">
        {{-- Success message --}}
        @if(session('settings-saved'))
            <div class="p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-sm text-green-400">
                Configuración guardada correctamente.
            </div>
        @endif

        {{-- Appearance --}}
        <div>
            <h3 class="text-lg font-medium text-[#f0f0f0] mb-4">Apariencia</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-[#888888] mb-2">Tema</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model="theme" value="dark" class="border-[#2a2a2a] bg-[#1e1e1e] text-[#7c3aed] focus:ring-[#7c3aed]">
                            <span class="text-sm text-[#f0f0f0]">Modo oscuro</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model="theme" value="light" class="border-[#2a2a2a] bg-[#1e1e1e] text-[#7c3aed] focus:ring-[#7c3aed]">
                            <span class="text-sm text-[#f0f0f0]">Modo claro</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-[#888888] mb-2">Idioma</label>
                    <select wire:model="language" class="w-full px-3 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm focus:border-[#7c3aed] focus:outline-none">
                        <option value="es">Español</option>
                        <option value="en">English</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Default Model --}}
        <div>
            <h3 class="text-lg font-medium text-[#f0f0f0] mb-4">Modelo por defecto</h3>

            <div>
                <label class="block text-sm text-[#888888] mb-2">Modelo predeterminado para nuevas conversaciones</label>
                <select wire:model="defaultModelId" class="w-full px-3 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm focus:border-[#7c3aed] focus:outline-none">
                    <option value="">Ninguno (seleccionar manualmente)</option>
                    @foreach($models as $model)
                        <option value="{{ $model['id'] }}">{{ $model['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Generation Settings --}}
        <div>
            <h3 class="text-lg font-medium text-[#f0f0f0] mb-4">Generación</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-[#888888] mb-2">
                        Temperatura: {{ $temperature }}
                    </label>
                    <input
                        type="range"
                        wire:model="temperature"
                        min="0"
                        max="2"
                        step="0.1"
                        class="w-full h-2 bg-[#2a2a2a] rounded-lg appearance-none cursor-pointer accent-[#7c3aed]"
                    >
                    <div class="flex justify-between text-xs text-[#888888] mt-1">
                        <span>Preciso (0)</span>
                        <span>Creativo (2)</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-[#888888] mb-2">Máximo de tokens</label>
                    <input
                        type="number"
                        wire:model="maxTokens"
                        min="100"
                        max="8192"
                        class="w-full px-3 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm focus:border-[#7c3aed] focus:outline-none"
                    >
                    <p class="text-xs text-[#888888] mt-1">Número máximo de tokens en la respuesta (100-8192)</p>
                </div>
            </div>
        </div>

        {{-- Save button --}}
        <div class="flex justify-end pt-4 border-t border-[#2a2a2a]">
            <button
                type="submit"
                class="px-6 py-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white text-sm font-medium rounded-lg transition-colors"
            >
                Guardar configuración
            </button>
        </div>
    </form>
</div>
