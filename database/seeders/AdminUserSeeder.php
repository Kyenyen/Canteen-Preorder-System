<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $lastUser = User::orderBy('user_id', 'desc')->first();
        $number = $lastUser ? intval(substr($lastUser->user_id, 1)) + 1 : 1;
        $userId = 'U' . str_pad($number, 4, '0', STR_PAD_LEFT);

        User::create([
            'user_id' => $userId,
            'username' => 'SuperAdmin',
            'email' => 'admin@tarc.edu.my',
            'password' => Hash::make('admin1234!!'), 
            'role' => 'admin', 
        ]);
    }
}