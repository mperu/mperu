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
        $this->authorize('view', $order);

        $order->loadMissing(['project', 'quote']);

        return view('orders.show', compact('order'));
    }

    public function markDepositPaid(Order $order)
    {
        $this->authorize('markDepositPaid', $order);

        $order->update([
            'status' => 'deposit_paid',
            'deposit_paid_at' => now(),
        ]);

        return back()->with('status', 'Acconto segnato come pagato ✅');
    }

    public function markBalancePaid(Order $order)
    {
        $this->authorize('markBalancePaid', $order);

        $order->update([
            'status' => 'paid',
            'balance_paid_at' => now(),
        ]);

        $project = Project::firstOrCreate(
            ['order_id' => $order->id],
            [
                'user_id' => $order->user_id,
                'status' => 'new',
                'subdomain' => null,
                'snapshot_path' => null,
            ]
        );

        if ($project->wasRecentlyCreated) {
            $project->updates()->create([
                'type' => 'project_created',
                'meta' => [
                    'by' => 'system',
                    'order_id' => $order->id,
                ],
            ]);
        }

        if ($project->user_id !== $order->user_id) {
            $project->update(['user_id' => $order->user_id]);
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('status', 'Saldo segnato come pagato ✅ Progetto pronto: carica i materiali.');
    }
}