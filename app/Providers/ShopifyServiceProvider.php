<?php

namespace App\Providers;

use Shopify\Context;
use Illuminate\Support\ServiceProvider;
use Shopify\Auth\FileSessionStorage;

class ShopifyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $sessionStoragePath = storage_path('app/shopify_sessions');

        Context::initialize(
            apiKey: 'bd37312aa8663c5c4f72b90177d30ae5',
            apiSecretKey: 'd23cc400c6fb630b8e7408207384f694',
            scopes: ['read_products'],
            hostName: 'eleventhree',
            sessionStorage: new FileSessionStorage($sessionStoragePath),
            apiVersion: '2023-04',
            isEmbeddedApp: true,
            isPrivateApp: false,
        );
    }
}

