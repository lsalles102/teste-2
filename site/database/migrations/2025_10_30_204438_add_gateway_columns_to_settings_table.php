<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->tinyInteger('sharkpay_is_enable')->default(1)->nullable();
            $table->tinyInteger('ezzebank_is_enable')->default(0)->nullable();
            $table->tinyInteger('ezzepay_is_enable')->default(0)->nullable();
            $table->string('software_background')->nullable();
            $table->tinyInteger('turn_on_football')->default(1)->nullable();
            $table->tinyInteger('revshare_reverse')->default(1)->nullable();
            $table->string('image_jackpot')->nullable();
            $table->tinyInteger('maintenance_mode')->default(0)->nullable();
            $table->string('cpa_percentage_baseline', 14)->nullable();
            $table->string('cpa_percentage_n1', 14)->nullable();
            $table->string('cpa_percentage_n2', 14)->nullable();
            $table->string('image_cassino_sidebar')->nullable();
            $table->string('image_favoritos_sidebar')->nullable();
            $table->string('image_wallet_sidebar')->nullable();
            $table->string('image_suporte_sidebar')->nullable();
            $table->string('image_promotions_sidebar')->nullable();
            $table->string('image_indique_sidebar')->nullable();
            $table->string('image_home_bottom')->nullable();
            $table->string('image_cassino_bottom')->nullable();
            $table->string('image_deposito_bottom')->nullable();
            $table->string('image_convidar_bottom')->nullable();
            $table->string('image_wallet_bottom')->nullable();
            $table->string('image_user_nav')->nullable();
            $table->string('image_home_sidebar')->nullable();
            $table->string('image_menu_nav')->nullable();
            $table->string('message_home_page')->nullable();
            $table->decimal('valor_por_bau', 10, 0)->nullable();
            $table->decimal('deposito_minimo_bau', 10, 0)->nullable();
            $table->decimal('cirus_baseline', 20, 2)->default(20.00)->nullable();
            $table->decimal('cirus_aposta', 20, 2)->default(20.00)->nullable();
            $table->decimal('cirus_valor', 20, 2)->default(20.00)->nullable();
            $table->string('icon_bt_1')->nullable();
            $table->string('icon_bt_2')->nullable();
            $table->string('icon_bt_3')->nullable();
            $table->string('icon_bt_4')->nullable();
            $table->string('icon_bt_5')->nullable();
            $table->string('icon_bt_6')->nullable();
            $table->string('icon_bt_7')->nullable();
            $table->string('icon_bt_8')->nullable();
            $table->string('name_bt_1')->nullable();
            $table->string('name_bt_2')->nullable();
            $table->string('name_bt_3')->nullable();
            $table->string('name_bt_4')->nullable();
            $table->string('img_bg_1')->nullable();
            $table->string('icon_wt_1')->nullable();
            $table->string('icon_wt_2')->nullable();
            $table->string('icon_wt_3')->nullable();
            $table->string('icon_wt_4')->nullable();
            $table->string('icon_wt_5')->nullable();
            $table->string('icon_wt_6')->nullable();
            $table->string('icon_wt_7')->nullable();
            $table->string('icon_wt_8')->nullable();
            $table->decimal('saldo_ini', 10, 2)->nullable();
            $table->text('modal_pop_up')->nullable();
            $table->string('img_modal_pop')->nullable();
            $table->tinyInteger('modal_active')->default(0)->nullable();
            $table->string('software_loading')->nullable();
            $table->string('image_home_bottom_hover')->nullable();
            $table->string('image_cassino_bottom_hover')->nullable();
            $table->string('image_deposito_bottom_hover')->nullable();
            $table->string('image_convidar_bottom_hover')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
};
