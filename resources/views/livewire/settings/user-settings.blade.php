<div>
    <form wire:submit="save" class="space-y-6">
        {{-- Success message --}}
        @if(session('settings-saved'))
            <div class="p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-sm text-green-400">
                {{ __('Configuración guardada correctamente.') }}
            </div>
        @endif

        {{-- Appearance --}}
        <div>
            <h3 class="text-lg font-medium text-[#f0f0f0] mb-4">{{ __('Apariencia') }}</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-[#888888] mb-2">{{ __('Tema') }}</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model="theme" value="dark" class="border-[#2a2a2a] bg-[#1e1e1e] text-[#7c3aed] focus:ring-[#7c3aed]">
                            <span class="text-sm text-[#f0f0f0]">{{ __('Modo oscuro') }}</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model="theme" value="light" class="border-[#2a2a2a] bg-[#1e1e1e] text-[#7c3aed] focus:ring-[#7c3aed]">
                            <span class="text-sm text-[#f0f0f0]">{{ __('Modo claro') }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Custom Instructions --}}
        <div>
            <h3 class="text-lg font-medium text-[#f0f0f0] mb-4">{{ __('Instrucciones personalizadas') }}</h3>

            <div>
                <label class="block text-sm text-[#888888] mb-2">
                    {{ __('System prompt global: dile a CarpIA cómo comportarse') }}
                </label>
                <textarea
                    wire:model="systemPrompt"
                    rows="4"
                    placeholder="{{ __('Ej: Siempre responde en chileno, sé formal y profesional.') }}"
                    class="input resize-none"
                ></textarea>
                <p class="text-xs text-[#888888] mt-1">{{ __('Estas instrucciones se antepondrán a todos tus mensajes. Máximo 2000 caracteres.') }}</p>
            </div>
        </div>

        {{-- Default Model --}}
        <div>
            <h3 class="text-lg font-medium text-[#f0f0f0] mb-4">{{ __('Modelo por defecto') }}</h3>

            <div>
                <label class="block text-sm text-[#888888] mb-2">{{ __('Modelo predeterminado para nuevas conversaciones') }}</label>
                <select wire:model="defaultModelId" class="input">
                    <option value="">{{ __('Ninguno (seleccionar manualmente)') }}</option>
                    @foreach($models as $model)
                        <option value="{{ $model['id'] }}">{{ $model['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Generation Settings --}}
        <div>
            <h3 class="text-lg font-medium text-[#f0f0f0] mb-4">{{ __('Generación') }}</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-[#888888] mb-2">
                        {{ __('Temperatura') }}: {{ $temperature }}
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
                        <span>{{ __('Preciso (0)') }}</span>
                        <span>{{ __('Creativo (2)') }}</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-[#888888] mb-2">{{ __('Máximo de tokens') }}</label>
                    <input
                        type="number"
                        wire:model="maxTokens"
                        min="100"
                        max="8192"
                        class="input"
                    >
                    <p class="text-xs text-[#888888] mt-1">{{ __('Número máximo de tokens en la respuesta (100-8192)') }}</p>
                </div>
            </div>
        </div>

        {{-- Save button --}}
        <div class="flex justify-end pt-4 border-t border-[#2a2a2a]">
            <button
                type="submit"
                class="btn-primary"
            >
                {{ __('Guardar configuración') }}
            </button>
        </div>
    </form>
</div>
