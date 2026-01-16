<?php

namespace App\Console\Commands;

use App\Models\Quote;
use Illuminate\Console\Command;

class RecalcQuotes extends Command
{
    protected $signature = 'quotes:recalc {--only-pending-orders : Aggiorna anche gli ordini solo se pending (default: true)}';
    protected $description = 'Ricalcola ritenuta + bollo + totale da pagare + acconto/saldo per tutti i preventivi in withholding.';

    public function handle(): int
    {
        $onlyPending = true; // default
        if ($this->option('only-pending-orders') === false) {
            $onlyPending = true; // keep default
        }

        $count = 0;

        Quote::query()
            ->where('fiscal_mode', 'withholding')
            ->orderBy('id')
            ->chunkById(200, function ($quotes) use (&$count, $onlyPending) {

                foreach ($quotes as $q) {
                    $gross = round((float) ($q->total_amount ?? 0), 2);
                    $rate  = (int) ($q->withholding_rate ?? 20);
                    $stamp = round((float) ($q->stamp_duty_amount ?? 2.00), 2);

                    // ✅ tua logica: il cliente paga LORDO + ritenuta + bollo
                    $withholding = round($gross * ($rate / 100), 2);
                    $totalToPay  = round($gross + $withholding + $stamp, 2);

                    // acconto 30% sul totale che paga il cliente
                    $deposit = round($totalToPay * 0.30, 2);
                    $balance = round($totalToPay - $deposit, 2);

                    $q->update([
                        'withholding_amount' => $withholding,
                        'net_amount' => $totalToPay,       // qui net_amount = TOTALE DA PAGARE (cliente)
                        'deposit_amount' => $deposit,
                        'balance_amount' => $balance,
                    ]);

                    // riallinea ordine SOLO se esiste e (di default) se è pending
                    if ($q->relationLoaded('order') === false) {
                        $q->load('order');
                    }

                    if ($q->order) {
                        if (!$onlyPending || $q->order->status === 'pending') {
                            $q->order->update([
                                'total_amount' => $totalToPay,
                                'deposit_amount' => $deposit,
                                'balance_amount' => $balance,
                            ]);
                        }
                    }

                    $count++;
                }
            });

        $this->info("OK. Preventivi ricalcolati: {$count}");

        return self::SUCCESS;
    }
}