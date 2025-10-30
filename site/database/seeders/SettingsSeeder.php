<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('settings')->insert([
            'software_name' => 'Laravel',
            'currency_code' => 'BRL',
            'decimal_format' => 'dot',
            'currency_position' => 'left',
            'prefix' => 'R$',
            'storage' => 'local',
            'initial_bonus' => 0,
            'min_deposit' => 20.00,
            'max_deposit' => 0.00,
            'min_withdrawal' => 20.00,
            'max_withdrawal' => 0.00,
            'stripe_is_enable' => 1,
            'suitpay_is_enable' => 1,
            'bspay_is_enable' => 0,
            'sharkpay_is_enable' => 1,
            'ezzebank_is_enable' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
