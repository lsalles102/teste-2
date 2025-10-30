<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomLayoutsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('custom_layouts')->insert([
            'primary_color' => '#FFFFFF',
            'title_color' => '#ffffff',
            'text_color' => '#98A7B5',
            'sub_text_color' => '#656E78',
            'placeholder_color' => '#FFFFFF',
            'background_color' => '#24262B',
            'background_base' => '#ECEFF1',
            'background_base_dark' => '#24262B',
            'carousel_banners' => '#1E2024',
            'carousel_banners_dark' => '#1E2024',
            'border_radius' => '.25rem',
            'search_border_color' => '#FFFFFF',
            'Border_bottons_and_selected' => '#FFFFFF',
            'disable_jackpot' => 0,
            'disable_button_float' => 0,
            'disable_last_winners' => 0,
            'disable_slider_text' => 0,
            'custom_header' => '',
            'custom_body' => '',
            'custom_css' => '',
            'custom_js' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
