<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Administrator
        User::updateOrCreate(
            ['email' => 'admin@cargotrace.test'],
            [
                'name' => 'Alexander Vance',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+1-555-0199',
                'company_name' => 'CargoTrace Global Operations',
                'preferred_currency' => 'USD',
            ]
        );

        // 2. Logistics Operator
        User::updateOrCreate(
            ['email' => 'operator@cargotrace.test'],
            [
                'name' => 'Elena Rostova',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'phone' => '+44-20-7946-0912',
                'company_name' => 'Global Port Logistics Terminal',
                'preferred_currency' => 'EUR',
            ]
        );

        // 3. Customer 1: Global Trade Corp
        User::updateOrCreate(
            ['email' => 'customer@globaltrade.test'],
            [
                'name' => 'Marcus Chen',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '+65-6789-0123',
                'company_name' => 'Global Trade Corp Singapore',
                'preferred_currency' => 'SGD',
            ]
        );

        // 4. Customer 2: Pacific Brands International
        User::updateOrCreate(
            ['email' => 'customer2@pacificbrands.test'],
            [
                'name' => 'Sarah Jenkins',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '+1-415-555-2671',
                'company_name' => 'Pacific Brands International',
                'preferred_currency' => 'USD',
            ]
        );
    }
}
