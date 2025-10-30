<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games_keys', function (Blueprint $table) {
            $table->string('api10jogos_agent_token')->nullable();
            $table->string('api10jogos_secret_key')->nullable();
            $table->string('api10jogos_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('games_keys', function (Blueprint $table) {
            $table->dropColumn(['api10jogos_agent_token', 'api10jogos_secret_key', 'api10jogos_url']);
        });
    }
};
