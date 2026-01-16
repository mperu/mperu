<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'slug' => 'bronze',
                'name' => 'BRONZE — Sito Vetrina Professionale',
                'description' => "Fino a 5 pagine · Template personalizzabile · Responsive · Colori/font/logo · Contatti/WhatsApp · Social + Maps · SEO tecnica base · 1 revisione · Consegna 7–10 giorni",
                'base_price' => 187500, // lordo
                'preview_image' => null,
                'is_active' => true,
            ],
            [
                'slug' => 'silver',
                'name' => 'SILVER — Sito Evoluto',
                'description' => "Fino a 10 pagine · Blog/Portfolio dinamico · Backend contenuti · Newsletter/Lead base · Design personalizzato · Form avanzati · 3 revisioni · SEO base + report iniziale",
                'base_price' => 250000, // lordo
                'preview_image' => null,
                'is_active' => true,
            ],
            [
                'slug' => 'gold',
                'name' => 'GOLD — Premium Full Custom',
                'description' => "Design full custom · Funzionalità senza limiti prefissati · Animazioni · Multilingua · Copywriting incluso · SEO avanzata · Integrazioni pro · Consulenza brand/marketing · Manutenzione 6 mesi",
                'base_price' => 400000, // indicativo lordo “da”
                'preview_image' => null,
                'is_active' => true,
            ],
        ];

        foreach ($rows as $row) {
            Template::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }
}