<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::updateOrCreate(
            ['name' => 'premium'],
            [
                'display_name' => 'Premium',
                'description' => 'Acceso completo a todos los modelos de IA',
                'monthly_credits' => 50000,
                'monthly_price' => 1990,
                'yearly_price' => 19900,
                'features' => [
                    'Todos los modelos de IA',
                    '50,000 créditos mensuales',
                    'Modelos premium (GPT-4o, Claude)',
                    'Exportar conversaciones',
                    'Soporte prioritario',
                ],
                'is_active' => true,
                'sort_order' => 0,
            ]
        );
    }
}
