<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Quote;
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

    public function create()
    {
        return view('quotes.create');
    }

    public function store(Request $request)
    {
        // MVP: preventivo dummy (poi lo colleghiamo al configuratore)
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:100'],
        ]);

        $total = 799.00;
        $deposit = 199.00;
        $balance = $total - $deposit;

        $quote = Quote::create([
            'user_id' => auth()->id(),
            'status' => 'draft',
            'total_amount' => $total,
            'deposit_amount' => $deposit,
            'balance_amount' => $balance,
            'config_json' => [
                'title' => $data['title'] ?? 'Preventivo base',
                'stack' => 'Laravel + Blade + Bootstrap + MySQL',
                'note' => 'Preventivo generato (MVP)',
            ],
        ]);

        return redirect()->route('quotes.show', $quote)
            ->with('status', 'Preventivo creato in bozza.');
    }

    public function show(Quote $quote)
    {
        abort_unless($quote->user_id === auth()->id(), 403);

        return view('quotes.show', compact('quote'));
    }

    /**
     * STEP 10: accetta preventivo (draft) e crea ordine
     */
    public function accept(Quote $quote)
    {
        abort_unless($quote->user_id === auth()->id(), 403);

        if ($quote->status !== 'draft') {
            return back()->withErrors([
                'quote' => 'Questo preventivo non è più in stato draft.',
            ]);
        }

        // Crea ordine collegato al preventivo
        $order = Order::create([
            'user_id' => auth()->id(),
            'quote_id' => $quote->id,
            'status' => 'pending',
            'total_amount' => $quote->total_amount,
            'deposit_amount' => $quote->deposit_amount,
            'balance_amount' => $quote->balance_amount,
        ]);

        // Aggiorna quote
        $quote->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return redirect()->route('orders.show', $order)
            ->with('status', 'Preventivo accettato. Ordine creato.');
    }
}