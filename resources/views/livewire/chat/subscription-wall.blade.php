<div class="flex flex-col items-center justify-center min-h-[60vh] p-6">
    <div class="max-w-2xl w-full">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-20 h-20 mx-auto mb-4 bg-[#7c3aed] rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-[#f0f0f0] mb-2">Desbloquea todo el potencial</h2>
            <p class="text-[#888888]">Elige un plan para acceder a todos los modelos de IA</p>
        </div>

        {{-- Billing Toggle --}}
        <div class="flex items-center justify-center gap-3 mb-8">
            <span class="text-sm {{ !$isYearly ? 'text-[#f0f0f0]' : 'text-[#888888]' }}">Mensual</span>
            <button 
                wire:click="toggleYearly"
                class="relative w-12 h-6 bg-[#2a2a2a] rounded-full transition-colors {{ $isYearly ? 'bg-[#7c3aed]' : '' }}"
            >
                <span class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform {{ $isYearly ? 'translate-x-6' : '' }}"></span>
            </button>
            <span class="text-sm {{ $isYearly ? 'text-[#f0f0f0]' : 'text-[#888888]' }}">
                Anual
                <span class="text-[#10b981] text-xs ml-1">Ahorra 20%</span>
            </span>
        </div>

        {{-- Plans Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            @foreach($this->plans as $plan)
                @if($plan->name !== 'free')
                    <div 
                        class="border rounded-xl p-6 cursor-pointer transition-all {{ $selectedPlanId === $plan->id ? 'border-[#7c3aed] bg-[#7c3aed]/10' : 'border-[#2a2a2a] bg-[#161616] hover:border-[#3a3a3a]' }}"
                        wire:click="selectPlan({{ $plan->id }})"
                    >
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-[#f0f0f0]">{{ $plan->display_name }}</h3>
                                <p class="text-sm text-[#888888]">{{ $plan->description }}</p>
                            </div>
                            @if($plan->name === 'premium')
                                <span class="px-2 py-1 text-xs bg-[#7c3aed] text-white rounded-full">Popular</span>
                            @endif
                        </div>
                        
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-[#f0f0f0]">
                                ${{ number_format($isYearly ? $plan->yearly_price / 12 : $plan->monthly_price, 0, ',', '.') }}
                            </span>
                            <span class="text-[#888888]">/mes</span>
                        </div>

                        @if($isYearly && $plan->yearly_price > 0)
                            <p class="text-sm text-[#10b981] mb-4">
                                Pago anual: ${{ number_format($plan->yearly_price, 0, ',', '.') }}
                            </p>
                        @endif

                        <ul class="space-y-2">
                            @foreach(json_decode($plan->features, true) ?? [] as $feature)
                                <li class="flex items-center gap-2 text-sm text-[#888888]">
                                    <svg class="w-4 h-4 text-[#10b981] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- CTA Button --}}
        @if($selectedPlanId)
            <div class="text-center">
                <a 
                    href="{{ $this->checkoutUrl }}"
                    class="inline-flex items-center gap-2 px-8 py-3 bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-semibold rounded-lg transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Pagar con Webpay
                </a>
                <p class="text-sm text-[#888888] mt-3">Pago seguro vía Transbank</p>
            </div>
        @endif

        {{-- Promo Code --}}
        <div class="mt-8 pt-6 border-t border-[#2a2a2a]">
            <livewire:settings.redeem-promo-code />
        </div>
    </div>
</div>
