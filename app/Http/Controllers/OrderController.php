<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Project;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->loadMissing('project');

        return view('orders.show', compact('order'));
    }

    /**
     * STEP 11: Segna acconto pagato
     */
    public function markDepositPaid(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if ($order->status !== 'pending') {
            return back()->withErrors([
                'order' => 'Puoi segnare l’acconto solo se l’ordine è in stato pending.',
            ]);
        }

        $order->update([
            'status' => 'deposit_paid',
            'deposit_paid_at' => now(),
        ]);

        return back()->with('status', 'Acconto segnato come pagato');
    }

    /**
     * STEP 11: Segna saldo pagato e crea Project
     */
    public function markBalancePaid(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if ($order->status !== 'deposit_paid') {
            return back()->withErrors([
                'order' => 'Puoi segnare il saldo solo dopo l’acconto (status deposit_paid).',
            ]);
        }

        $order->update([
            'status' => 'paid',
            'balance_paid_at' => now(),
        ]);

        // 1 ordine -> 1 progetto (crea se non esiste)
        Project::firstOrCreate(
            ['order_id' => $order->id],
            [
                'user_id' => $order->user_id,
                'status' => 'new',
                'subdomain' => null,
                'snapshot_path' => null,
            ]
        );

        return back()->with('status', 'Saldo segnato come pagato Progetto creato.');
    }
}