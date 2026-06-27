<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@carpia.cl'],
            [
                'name' => 'Admin CarpIA',
                'password' => Hash::make('password'),
                'credits' => 1000,
            ]
        );

        UserSetting::updateOrCreate(
            ['user_id' => $user->id],
            [
                'theme' => 'dark',
                'language' => 'es',
                'temperature' => 0.7,
                'max_tokens' => 2048,
            ]
        );
    }
}
