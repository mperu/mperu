<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function view(User $user, Order $order): bool
    {
        return (int) $order->user_id === (int) $user->id;
    }

    public function markDepositPaid(User $user, Order $order): bool
    {
        return $this->view($user, $order) && $order->status === 'pending';
    }

    public function markBalancePaid(User $user, Order $order): bool
    {
        return $this->view($user, $order) && $order->status === 'deposit_paid';
    }
}