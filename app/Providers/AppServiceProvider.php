<?php

namespace App\Providers;

use App\Models\Transaction;
use App\Observers\TransactionObserver;
use App\Services\Payment\PaymentGatewayFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('PaymentGatewayFactory', function ($app) {
            return new PaymentGatewayFactory();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Transaction::observe(TransactionObserver::class);

        Broadcast::routes(['middleware' => ['auth:sanctum']]);
        require base_path('routes/channels.php');

        Broadcast::extend('socket', function ($app, $config) {
            return new class($config) implements \Illuminate\Contracts\Broadcasting\Broadcaster {
                protected $config;

                public function __construct($config)
                {
                    $this->config = $config;
                }

                public function auth($request)
                {
                    // متدی برای احراز هویت در صورت نیاز
                }

                public function validAuthenticationResponse($request, $result)
                {
                    return $result;
                }

                public function broadcast(array $channels, $event, array $payload = [])
                {
                    // متدی برای ارسال رویداد‌ها
                }
            };
        });
    }
}
