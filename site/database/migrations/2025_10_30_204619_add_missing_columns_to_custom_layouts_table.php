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
        Schema::table('custom_layouts', function (Blueprint $table) {
            $table->tinyInteger('disable_jackpot')->default(0)->nullable();
            $table->tinyInteger('disable_button_float')->default(0)->nullable();
            $table->tinyInteger('disable_last_winners')->default(0)->nullable();
            $table->tinyInteger('disable_slider_text')->default(0)->nullable();
            $table->string('search_border_color', 20)->default('#FFFFFF')->nullable();
            $table->string('Border_bottons_and_selected', 20)->default('#FFFFFF')->nullable();
            $table->string('background_bottom_navigation', 20)->nullable();
            $table->string('background_bottom_navigation_dark', 20)->nullable();
            $table->string('borders_and_dividers_colors', 20)->nullable();
            $table->string('search_back', 20)->nullable();
            $table->string('color_bt_1', 20)->nullable();
            $table->string('color_bt_2', 20)->nullable();
            $table->string('color_bt_3', 20)->nullable();
            $table->string('color_bt_4', 20)->nullable();
            $table->string('bt_1_link')->nullable();
            $table->string('bt_2_link')->nullable();
            $table->string('bt_3_link')->nullable();
            $table->string('bt_4_link')->nullable();
            $table->string('bt_5_link')->nullable();
            $table->string('value_color_jackpot', 20)->nullable();
            $table->string('value_wallet_navtop', 20)->nullable();
            $table->string('bonus_color_dep', 20)->nullable();
            $table->string('colors_deposit_value', 20)->nullable();
            $table->string('color_players', 20)->nullable();
            $table->longText('modal_termos_register')->nullable();
            $table->longText('modal_termos_cpf')->nullable();
            $table->string('placeholder_background', 20)->nullable();
            $table->string('card_transaction', 20)->nullable();
            $table->string('back_sub_color', 20)->nullable();
            $table->string('item_sub_color', 20)->nullable();
            $table->string('text_sub_color', 20)->nullable();
            $table->string('title_sub_color', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_layouts', function (Blueprint $table) {
            //
        });
    }
};
