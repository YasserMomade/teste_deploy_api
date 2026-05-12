<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Counter;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use App\Observers\ClientObserver;
use App\Observers\CounterObserver;
use App\Observers\CountryObserver;
use App\Observers\InvoiceObserver;
use App\Observers\OrderObserver;
use App\Observers\StoreObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        Schema::defaultStringLength(length: 191);
        Country::observe(CountryObserver::class);
        Counter::observe(CounterObserver::class);
        Client::observe(ClientObserver::class);
        User::observe(UserObserver::class);
        Store::observe(StoreObserver::class);
        Invoice::observe(InvoiceObserver::class);
        Order::observe(OrderObserver::class);
    }
}
