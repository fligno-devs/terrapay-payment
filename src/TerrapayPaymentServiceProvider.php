<?php

namespace FlignoDevs\TerrapayPayment;

use Illuminate\Support\ServiceProvider;

class TerrapayPaymentServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'fligno-devs');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'fligno-devs');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/terrapay-payment.php', 'terrapay-payment');

        // Register the service the package provides.
        $this->app->singleton('terrapay-payment', function ($app) {
            return new TerrapayPayment;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['terrapay-payment'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/terrapay-payment.php' => config_path('terrapay-payment.php'),
        ], 'terrapay-payment.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/fligno-devs'),
        ], 'terrapay-payment.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/fligno-devs'),
        ], 'terrapay-payment.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/fligno-devs'),
        ], 'terrapay-payment.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
