<?php

namespace App\Http\Controllers;

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
        $balance = $total - $deposit; // 600.00

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
        // Sicurezza: il cliente può vedere solo i suoi preventivi
        abort_unless($quote->user_id === auth()->id(), 403);

        return view('quotes.show', compact('quote'));
    }
}