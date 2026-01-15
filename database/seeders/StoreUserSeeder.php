<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreUser;
use Illuminate\Support\Facades\Hash;

class StoreUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if super admin already exists to avoid duplicates
        if (!StoreUser::where('email', 'admin@store.com')->exists()) {
            StoreUser::create([
                'name'     => 'Super Admin',
                'email'    => 'admin@store.com',
                'password' => Hash::make('password'), // Default password
               
            ]);
        }
    }
}