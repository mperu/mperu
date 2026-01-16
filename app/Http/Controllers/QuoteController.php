<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Quote;
use App\Models\Template;
use App\Models\TemplateOption;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('quotes.index', compact('quotes'));
    }

    public function create(Request $request)
    {
        // Arriva da /templates -> /quotes/create?template=bronze
        $slug = $request->query('template');

        $template = null;
        $options = collect();

        if ($slug) {
            $template = Template::query()
                ->where('is_active', true)
                ->where('slug', $slug)
                ->first();

            if ($template) {
                $options = TemplateOption::query()
                    ->where('is_active', true)
                    ->orderBy('id')
                    ->get()
                    ->filter(function ($opt) use ($template) {
                        $availableFor = data_get($opt->constraints, 'available_for');
                        if (!$availableFor) return true;

                        return in_array($template->slug, (array) $availableFor, true);
                    })
                    ->values();
            }
        }

        return view('quotes.create', compact('template', 'options'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:100'],

            // Totale COMPENSO (il tuo prezzo) in EURO, es: 1875.00
            'total_gross' => ['required', 'numeric', 'min:0'],

            // Meta (template + opzioni) salvata nel quote
            'config_json' => ['nullable', 'array'],

            // Template selezionato
            'template_id' => ['nullable', 'integer', 'exists:templates,id'],
        ]);

        $gross = round((float) $data['total_gross'], 2);

        // Ritenuta 20% (additiva nel totale cliente)
        $withholdingRate = 20;
        $withholding = round($gross * ($withholdingRate / 100), 2);

        // Marca da bollo
        $stampDuty = 2.00;

        // Totale cliente = compenso + ritenuta + bollo
        $totalToPay = round($gross + $withholding + $stampDuty, 2);

        // Acconto 30% sul totale cliente
        $deposit = round($totalToPay * 0.30, 2);
        $balance = round($totalToPay - $deposit, 2);

        $config = (array) ($data['config_json'] ?? []);
        $config['title'] = $data['title'] ?? ($config['title'] ?? 'Preventivo');

        $config['fiscal'] = [
            'mode' => 'withholding_additive',
            'rate' => $withholdingRate,
            'gross' => $gross,
            'withholding' => $withholding,
            'stamp_duty' => $stampDuty,
            'total_to_pay' => $totalToPay,
        ];

        $quote = Quote::create([
            'user_id' => auth()->id(),
            'status' => 'draft',

            // Compenso (il tuo prezzo)
            'total_amount' => $gross,

            // Totale che paga il cliente
            'net_amount' => $totalToPay,

            'deposit_amount' => $deposit,
            'balance_amount' => $balance,

            'fiscal_mode' => 'withholding',
            'withholding_rate' => $withholdingRate,
            'withholding_amount' => $withholding,
            'stamp_duty_amount' => $stampDuty,

            'template_id' => $data['template_id'] ?? null,
            'config_json' => $config,
        ]);

        return redirect()
            ->route('quotes.show', $quote)
            ->with('status', 'Preventivo creato ✅');
    }

    public function show(Quote $quote)
    {
        abort_unless((int) $quote->user_id === (int) auth()->id(), 403);

        return view('quotes.show', compact('quote'));
    }

    public function accept(Quote $quote)
    {
        abort_unless((int) $quote->user_id === (int) auth()->id(), 403);

        if ($quote->status !== 'draft') {
            return back()->withErrors([
                'quote' => 'Questo preventivo non è più in stato draft.',
            ]);
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'quote_id' => $quote->id,
            'status' => 'pending',
            'total_amount' => $quote->net_amount,
            'deposit_amount' => $quote->deposit_amount,
            'balance_amount' => $quote->balance_amount,
        ]);

        $quote->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return redirect()
            ->route('orders.show', $order)
            ->with('status', 'Preventivo accettato ✅ Ordine creato.');
    }

    /**
     * NON ACCETTO (DELETE logico): status=rejected
     */
    public function destroy(Quote $quote)
    {
        abort_unless((int) $quote->user_id === (int) auth()->id(), 403);

        if ($quote->status !== 'draft') {
            return back()->withErrors([
                'quote' => 'Puoi rifiutare solo preventivi in stato draft.',
            ]);
        }

        $quote->update([
            'status' => 'rejected',
        ]);

        return redirect()
            ->route('quotes.index')
            ->with('status', 'Preventivo rifiutato.');
    }
}