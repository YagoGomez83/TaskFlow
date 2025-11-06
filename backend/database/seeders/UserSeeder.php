<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@taskflow.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ],
            [
                'name' => 'John Manager',
                'email' => 'john@taskflow.com',
                'password' => Hash::make('password123'),
                'role' => 'manager',
            ],
            [
                'name' => 'Jane Member',
                'email' => 'jane@taskflow.com',
                'password' => Hash::make('password123'),
                'role' => 'member',
            ],
            [
                'name' => 'Bob Developer',
                'email' => 'bob@taskflow.com',
                'password' => Hash::make('password123'),
                'role' => 'member',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
