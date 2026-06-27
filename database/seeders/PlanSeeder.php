<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::updateOrCreate(
            ['name' => 'free'],
            [
                'display_name' => 'Free',
                'description' => 'Ideal para probar CarpIA',
                'monthly_credits' => 1000,
                'monthly_price' => 0,
                'yearly_price' => 0,
                'features' => [
                    'Acceso a modelos gratuitos',
                    '1,000 créditos mensuales',
                    'Historial de conversaciones',
                    'Biblioteca de prompts básica',
                ],
                'is_active' => true,
                'sort_order' => 0,
            ]
        );

        Plan::updateOrCreate(
            ['name' => 'premium'],
            [
                'display_name' => 'Premium',
                'description' => 'Para usuarios frecuentes',
                'monthly_credits' => 50000,
                'monthly_price' => 9900, // $9.900 CLP
                'yearly_price' => 99000, // $99.000 CLP (2 meses gratis)
                'features' => [
                    'Todos los modelos gratuitos',
                    '50,000 créditos mensuales',
                    'Modelos premium (GPT-4o, Claude)',
                    'Exportar conversaciones',
                    'Soporte prioritario',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        Plan::updateOrCreate(
            ['name' => 'pro'],
            [
                'display_name' => 'Pro',
                'description' => 'Para profesionales y empresas',
                'monthly_credits' => 200000,
                'monthly_price' => 29900, // $29.900 CLP
                'yearly_price' => 299000, // $299.000 CLP (2 meses gratis)
                'features' => [
                    'Todos los modelos',
                    '200,000 créditos mensuales',
                    'Modelos premium + API',
                    'Exportar conversaciones',
                    'Soporte prioritario 24/7',
                    'Acceso anticipado a nuevas funciones',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ]
        );
    }
}
