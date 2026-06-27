<aside x-data="{ open: false }" class="fixed left-0 top-0 w-64 h-screen bg-[#161616] border-r border-[#2a2a2a] flex flex-col z-50">
    <!-- Logo -->
    <div class="px-6 py-5 border-b border-[#2a2a2a]">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('pet-2.png') }}" alt="CarpIA" class="w-8 h-8" />
            <span class="text-xl font-bold text-[#7c3aed]">CarpIA</span>
        </a>
    </div>

    <!-- Nueva conversación -->
    <div class="px-4 py-4">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-[#7c3aed] hover:bg-[#6d28d9] text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span class="font-medium">Nueva conversación</span>
        </a>
    </div>

    <!-- Scrollable content -->
    <div class="flex-1 overflow-y-auto flex flex-col">
        <!-- Historial (Livewire) -->
        <livewire:sidebar.conversation-list />

        <!-- Favoritos (Livewire) -->
        <div class="border-t border-[#2a2a2a] py-2">
            <livewire:sidebar.favorites />
        </div>

        <!-- Biblioteca (Livewire) -->
        <div class="border-t border-[#2a2a2a] py-2">
            <livewire:sidebar.prompt-library />
        </div>
    </div>

    <!-- Usuario -->
    <div class="border-t border-[#2a2a2a] px-4 py-4">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-3 w-full px-3 py-2 rounded-lg text-[#888888] hover:bg-[#1e1e1e] hover:text-[#f0f0f0] transition-colors text-sm">
                <div class="w-8 h-8 rounded-full bg-[#7c3aed] flex items-center justify-center text-white text-sm font-medium">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <span class="flex-1 text-left truncate">{{ Auth::user()->name }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" @click.away="open = false" x-transition class="absolute bottom-full left-0 w-full mb-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg shadow-lg overflow-hidden">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-[#888888] hover:bg-[#2a2a2a] hover:text-[#f0f0f0]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Perfil</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 text-sm text-[#888888] hover:bg-[#2a2a2a] hover:text-[#f0f0f0]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Salir</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Mobile menu button -->
    <button @click="open = !open" class="absolute top-5 right-4 sm:hidden text-[#888888] hover:text-[#f0f0f0]">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path :class="{'hidden': open, 'inline-flex': !open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</aside>

<!-- Mobile sidebar overlay -->
<div x-show="open" @click="open = false" class="fixed inset-0 bg-black/50 z-40 sm:hidden" x-transition.opacity></div>
