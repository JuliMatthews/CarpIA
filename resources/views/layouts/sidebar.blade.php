<aside x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed left-0 top-0 z-50 h-screen w-64 bg-[#161616] border-r border-[#2a2a2a] flex flex-col lg:translate-x-0">
    {{-- Logo --}}
    <div class="flex items-center justify-between px-5 py-5 border-b border-[#2a2a2a]">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('pet-2.png') }}" alt="CarpIA" class="w-8 h-8" />
            <span class="text-xl font-bold text-[#7c3aed]">CarpIA</span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden flex items-center justify-center w-8 h-8 rounded-lg text-[#888888] hover:bg-[#1e1e1e] hover:text-[#f0f0f0] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Nueva conversación button --}}
    <div class="px-4 pt-4 pb-2">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#7c3aed] hover:bg-[#6d28d9] text-white text-sm font-medium transition-all duration-200 shadow-lg shadow-[#7c3aed]/20">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>{{ __('Nueva conversación') }}</span>
        </a>
    </div>

    {{-- Scrollable content --}}
    <div class="flex-1 overflow-y-auto px-3 py-2 space-y-1">
        <div class="sidebar-section-title">{{ __('Principal') }}</div>

        <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'sidebar-item-active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>{{ __('Inicio') }}</span>
        </a>

        <a href="{{ route('chat') }}" class="sidebar-item {{ request()->routeIs('chat*') ? 'sidebar-item-active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <span>{{ __('Chat') }}</span>
        </a>

        <a href="{{ route('planes') }}" class="sidebar-item {{ request()->routeIs('planes') ? 'sidebar-item-active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
            <span>{{ __('Planes') }}</span>
        </a>

        <div class="sidebar-section-title">{{ __('Historial') }}</div>

        <livewire:sidebar.conversation-list />
        <livewire:sidebar.favorites />

        <div class="sidebar-section-title">{{ __('Herramientas') }}</div>

        <livewire:sidebar.prompt-library />

        @if(Auth::user()->isAdmin())
            <div class="sidebar-section-title">{{ __('Administración') }}</div>

            <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'sidebar-item-active' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ __('Panel Admin') }}</span>
            </a>

            <a href="{{ route('admin.users') }}" class="sidebar-item {{ request()->routeIs('admin.users*') ? 'sidebar-item-active' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
                <span>{{ __('Usuarios') }}</span>
            </a>

            <a href="{{ route('admin.models') }}" class="sidebar-item {{ request()->routeIs('admin.models*') ? 'sidebar-item-active' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>{{ __('Modelos') }}</span>
            </a>

            <a href="{{ route('admin.promo-codes') }}" class="sidebar-item {{ request()->routeIs('admin.promo-codes*') ? 'sidebar-item-active' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span>{{ __('Promo Codes') }}</span>
            </a>
        @endif
    </div>
</aside>
