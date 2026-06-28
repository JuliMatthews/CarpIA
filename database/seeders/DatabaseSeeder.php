<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AiProviderSeeder::class,
            AiModelSeeder::class,
            PlanSeeder::class,
            UserSeeder::class,
            SuperUserSeeder::class,
        ]);
    }
}
