<?php

namespace Database\Seeders;

use App\Models\TemplateOption;
use Illuminate\Database\Seeder;

class TemplateOptionSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['key'=>'hosting_12m','label'=>'Dominio + Hosting 12 mesi','type'=>'bool','price_delta'=>7000,'constraints'=>null,'is_active'=>true],

            ['key'=>'multilingual','label'=>'Multilingua (1 lingua)','type'=>'bool','price_delta'=>25000,'constraints'=>null,'is_active'=>true],

            ['key'=>'blog_news','label'=>'Blog / News base','type'=>'bool','price_delta'=>20000,'constraints'=>['available_for'=>['bronze','silver']],'is_active'=>true],

            ['key'=>'copywriting_basic','label'=>'Copywriting (fino a 5 pagine)','type'=>'bool','price_delta'=>35000,'constraints'=>['available_for'=>['bronze','silver']],'is_active'=>true],

            ['key'=>'seo_advanced','label'=>'SEO avanzata','type'=>'bool','price_delta'=>30000,'constraints'=>['available_for'=>['silver','gold']],'is_active'=>true],

            ['key'=>'booking_base','label'=>'Booking base (calendario appuntamenti)','type'=>'bool','price_delta'=>35000,'constraints'=>['available_for'=>['bronze','silver']],'is_active'=>true],

            ['key'=>'booking_premium','label'=>'Booking premium (pagamento)','type'=>'bool','price_delta'=>60000,'constraints'=>['available_for'=>['silver','gold']],'is_active'=>true],

            ['key'=>'ecommerce_base','label'=>'Ecommerce base (fino a 30 prodotti)','type'=>'bool','price_delta'=>150000,'constraints'=>['available_for'=>['silver','gold']],'is_active'=>true],

            ['key'=>'extra_pages','label'=>'Pagine extra','type'=>'number','price_delta'=>8000,'constraints'=>null,'is_active'=>true],
        ];

        foreach ($rows as $row) {
            TemplateOption::updateOrCreate(['key' => $row['key']], $row);
        }
    }
}