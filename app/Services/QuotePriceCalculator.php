<?php

namespace App\Services;

use App\Models\Template;
use App\Models\TemplateOption;

class QuotePriceCalculator
{
    /**
     * @param array $selected  es: ['hosting_12m' => '1', 'extra_pages' => '2']
     * @return array{total:int, breakdown:array<int,array{label:string, amount:int}>}
     */
    public function calculate(Template $template, array $selected): array
    {
        $total = (int) $template->base_price;

        $breakdown = [
            ['label' => 'Base: ' . $template->name, 'amount' => (int) $template->base_price],
        ];

        $options = TemplateOption::query()
            ->where('is_active', true)
            ->get()
            ->keyBy('key');

        foreach ($selected as $key => $value) {
            if (!isset($options[$key])) continue;

            $opt = $options[$key];

            if ($opt->type === 'bool') {
                if ((string) $value !== '1') continue;

                $amount = (int) $opt->price_delta;
                $total += $amount;
                $breakdown[] = ['label' => $opt->label, 'amount' => $amount];
                continue;
            }

            if ($opt->type === 'number') {
                $qty = max(0, (int) $value);
                if ($qty <= 0) continue;

                $amount = $qty * (int) $opt->price_delta;
                $total += $amount;
                $breakdown[] = ['label' => $opt->label . ' x ' . $qty, 'amount' => $amount];
            }
        }

        return ['total' => $total, 'breakdown' => $breakdown];
    }
}