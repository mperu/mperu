<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Project;
use App\Policies\OrderPolicy;
use App\Policies\ProjectPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Project::class => ProjectPolicy::class,
        Order::class   => OrderPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}