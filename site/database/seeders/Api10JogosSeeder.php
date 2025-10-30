<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Api10JogosSeeder extends Seeder
{
    public function run(): void
    {
        // Criar configurações padrão do sistema
        DB::table('settings')->insert([
            'software_name' => 'Cassino Online',
            'software_description' => 'Sistema de Cassino com API 10 Jogos',
            'currency_code' => 'BRL',
            'decimal_format' => 'dot',
            'currency_position' => 'left',
            'prefix' => 'R$',
            'storage' => 'local',
            'min_deposit' => 10.00,
            'max_deposit' => 10000.00,
            'min_withdrawal' => 20.00,
            'max_withdrawal' => 5000.00,
            'initial_bonus' => 10.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Criar moeda padrão BRL
        DB::table('currencies')->insert([
            'code' => 'BRL',
            'name' => 'Real Brasileiro',
            'symbol' => 'R$',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Criar usuário de teste
        $userId = DB::table('users')->insertGetId([
            'name' => 'Usuário Teste',
            'email' => 'teste@cassino.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role_id' => 1,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Criar carteira para o usuário
        DB::table('wallets')->insert([
            'user_id' => $userId,
            'currency' => 'BRL',
            'symbol' => 'R$',
            'balance' => 1000.00,
            'balance_withdrawal' => 0.00,
            'balance_bonus_rollover' => 0.00,
            'balance_deposit_rollover' => 0.00,
            'balance_bonus' => 100.00,
            'balance_cryptocurrency' => 0.00,
            'balance_demo' => 0.00,
            'refer_rewards' => 0.00,
            'hide_balance' => false,
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Criar chave da API 10 Jogos (exemplo - será configurado pelo admin)
        DB::table('games_keys')->insert([
            'api10jogos_agent_token' => '',
            'api10jogos_secret_key' => '',
            'api10jogos_url' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Criar categoria e provider para os jogos
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Slots',
            'description' => 'Jogos de caça-níqueis',
            'slug' => 'slots',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $providerId = DB::table('providers')->insertGetId([
            'code' => 'pgsoft',
            'name' => 'PG Soft',
            'status' => 1,
            'rtp' => 96,
            'views' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Criar alguns jogos de exemplo
        $games = [
            ['Fortune Tiger', 'fortune-tiger', 126],
            ['Fortune Ox', 'fortune-ox', 98],
            ['Fortune Dragon', 'fortune-dragon', 1695365],
            ['Fortune Rabbit', 'fortune-rabbit', 1543462],
            ['Fortune Mouse', 'fortune-mouse', 68],
        ];

        foreach ($games as $game) {
            DB::table('games')->insert([
                'provider_id' => $providerId,
                'game_id' => (string)$game[2],
                'game_name' => $game[0],
                'game_code' => $game[1],
                'cover' => '/images/games/' . $game[1] . '.png',
                'status' => '1',
                'rtp' => 96,
                'distribution' => 'pgsoft',
                'views' => 0,
                'is_featured' => false,
                'show_home' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
