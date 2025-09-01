<?php

namespace App\Providers;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        Inertia::share([
            'cartCount' => function () {
                if (Auth::check()) {
                    return Cart::where('user_id', Auth::id())
                        ->withCount('cartItems')
                        ->first()
                            ?->cart_items_count ?? 0;
                }
                return 0;
            }
        ]);
    }
}
