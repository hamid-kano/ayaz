<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'أحمد محمد',
            'email' => 'admin@ayaz.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'فاطمة علي',
            'email' => 'fatima@ayaz.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'عمر خالد',
            'email' => 'omar@ayaz.com',
            'password' => Hash::make('password'),
        ]);
    }
}