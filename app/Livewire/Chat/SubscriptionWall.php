<?php

namespace App\Livewire\Chat;

use App\Models\Plan;
use App\Services\SubscriptionService;
use Livewire\Component;

class SubscriptionWall extends Component
{
    public ?int $selectedPlanId = null;
    public bool $isYearly = false;

    public function getPlansProperty()
    {
        return Plan::active()->ordered()->get();
    }

    public function getSelectedPlanProperty(): ?Plan
    {
        return $this->selectedPlanId ? Plan::find($this->selectedPlanId) : null;
    }

    public function selectPlan(int $planId): void
    {
        $this->selectedPlanId = $planId;
    }

    public function toggleYearly(): void
    {
        $this->isYearly = !$this->isYearly;
    }

    public function getCheckoutUrl(): string
    {
        if (!$this->selectedPlanId) {
            return '#';
        }

        return route('checkout.create') . '?' . http_build_query([
            'plan_id' => $this->selectedPlanId,
            'is_yearly' => $this->isYearly,
        ]);
    }

    public function render()
    {
        return view('livewire.chat.subscription-wall');
    }
}
