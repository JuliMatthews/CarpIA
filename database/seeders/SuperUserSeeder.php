<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'ia.carpia.cl@gmail.com'],
            [
                'name' => 'Super Admin CarpIA',
                'password' => Hash::make('Xha6$.jLKky73'),
                'credits' => 999999,
                'plan' => 'pro',
                'is_admin' => true,
            ]
        );

        UserSetting::updateOrCreate(
            ['user_id' => $user->id],
            [
                'theme' => 'dark',
                'language' => 'es',
                'temperature' => 0.7,
                'max_tokens' => 4096,
            ]
        );
    }
}
