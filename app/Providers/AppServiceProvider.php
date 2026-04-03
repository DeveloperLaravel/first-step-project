<?php

namespace App\Providers;

use App\Models\Card;
use App\Models\Order;
use App\Models\OrderItem;
use App\Observer\OrderItemObserver;
use App\Service\CardService;
use App\Service\OrderService ;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       OrderItem::observe(OrderItemObserver::class);
        Order::observe(OrderService::class);
       Card::observe(CardService::class);
    }
}
