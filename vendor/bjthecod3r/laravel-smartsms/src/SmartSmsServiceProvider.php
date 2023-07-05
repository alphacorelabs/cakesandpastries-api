<?php

/*
 * This file is part of the Laravel Smartsms package.
 *
 * (c) Bolaji Ajani <fabulousbj@hotmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BJTheCod3r\SmartSms;

use Illuminate\Support\ServiceProvider;

class SmartSmsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'bjthecod3r');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'bjthecod3r');
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
        $this->mergeConfigFrom(__DIR__.'/../config/smartsms.php', 'smartsms');

        // Register the service the package provides.
        $this->app->singleton('smartsms', function ($app) {
            return new SmartSms;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['smartsms'];
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
            __DIR__.'/../config/smartsms.php' => config_path('smartsms.php'),
        ], 'smartsms.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/bjthecod3r'),
        ], 'smartsms.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/bjthecod3r'),
        ], 'smartsms.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/bjthecod3r'),
        ], 'smartsms.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
