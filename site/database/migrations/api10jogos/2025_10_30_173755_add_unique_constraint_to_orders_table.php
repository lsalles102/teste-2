<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Adicionar constraint UNIQUE para garantir idempotência de transações
            // round_id + user_id garantem que a mesma transação não seja processada duas vezes
            $table->unique(['round_id', 'user_id'], 'orders_round_user_unique');
            
            // Índice adicional para performance em consultas por transaction_id
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique('orders_round_user_unique');
            $table->dropIndex(['transaction_id']);
        });
    }
};
