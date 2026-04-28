<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WebsiteManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StoreUser::updateOrCreate(
            ['email' => 'website_manager@homefoods.com'],
            [
                'name' => 'Global Website Manager',
                'password' => Hash::make('password123'),
                'store_id' => 1, // Assuming store 1 is the main/headquarters store
                'is_active' => true,
                'is_website_manager' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );
        
        $this->command->info('Website Manager seeded successfully: website_manager@homefoods.com / password123');
    }
}
